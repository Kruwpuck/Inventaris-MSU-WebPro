<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\LoanRequest;
use App\Exports\LaporanExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanExportController extends Controller
{
    public function export(Request $request, string $format)
    {
        // ambil filter dari query string (dikirim JS)
        $periode = $request->query('periode', '1m');
        $kategori = $request->query('kategori', 'all');
        $status = $request->query('status', 'all');
        $q = $request->query('q', '');

        $requests = LoanRequest::with(['items'])
            ->orderByDesc('id')
            ->get();

        $today = Carbon::today();

        // flatten loan_request -> item
        $rows = $requests->flatMap(function ($lr) use ($today) {
            return $lr->items->map(function ($inv) use ($lr, $today) {

                $tglPinjam = Carbon::parse($lr->loan_date_start);
                $jatuhTempo = Carbon::parse($lr->loan_date_end);

                // map backend -> UI status
                if ($lr->status === 'returned') {
                    if ($lr->loanRecord && $lr->loanRecord->is_submitted) {
                        $statusUi = 'Sudah Kembali';
                    } else {
                        $statusUi = 'Menunggu Submit';
                    }
                    $tglKembali = $jatuhTempo->format('m/d/Y');
                } else {
                    $statusUi = $today->gt($jatuhTempo) ? 'Terlambat' : 'Sedang Dipinjam';
                    $tglKembali = '-';
                }

                $kategoriUi = $inv->category === 'ruangan' ? 'Ruangan' : 'Barang';

                return [
                    'nama_item' => $inv->name,
                    'kategori' => $kategoriUi,
                    'peminjam' => $lr->borrower_name,

                    // simpan Carbon dulu biar bisa difilter periode
                    'tgl_pinjam' => $tglPinjam,
                    'jatuh_tempo' => $jatuhTempo,

                    'tgl_kembali' => $tglKembali,
                    'jumlah' => (int) ($inv->pivot->quantity ?? 1),
                    'status' => $statusUi,
                ];
            });
        })->values();

        /**
         * ==========================
         * FILTER BERDASARKAN PERIODE
         * ==========================
         */
        [$from, $to, $periodeLabel] = $this->resolvePeriode($periode);
        if ($from && $to) {
            $rows = $rows->filter(fn($r) => $r['tgl_pinjam']->between($from, $to))->values();
        }

        /**
         * ==========================
         * FILTER KATEGORI
         * ==========================
         */
        if ($kategori !== 'all') {
            $rows = $rows->filter(fn($r) => $r['kategori'] === $kategori)->values();
        }

        /**
         * ==========================
         * FILTER STATUS
         * ==========================
         */
        if ($status !== 'all') {
            $rows = $rows->filter(fn($r) => $r['status'] === $status)->values();
        }

        /**
         * ==========================
         * SEARCH TEXT
         * ==========================
         */
        if ($q) {
            $qLower = strtolower($q);
            $rows = $rows->filter(function ($r) use ($qLower) {
                return str_contains(strtolower($r['nama_item']), $qLower)
                    || str_contains(strtolower($r['kategori']), $qLower)
                    || str_contains(strtolower($r['peminjam']), $qLower)
                    || str_contains(strtolower($r['status']), $qLower);
            })->values();
        }

        $filename = 'laporan_peminjaman_' . now()->format('Ymd_His');

        /**
         * ==========================
         * EXPORT PDF
         * ==========================
         */
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.laporan-pdf', [
                'rows' => $rows,
                'periode_label' => $periodeLabel, // <<< FIX ERROR KEMARIN
                'kategori' => $kategori,
                'status' => $status,
            ])->setPaper('a4', 'landscape');

            return $pdf->download($filename . '.pdf');
        }

        /**
         * ==========================
         * EXPORT XLSX / CSV
         * ==========================
         */
        if ($format === 'csv') {
            return Excel::download(
                new LaporanExport($rows),
                $filename . '.csv',
                \Maatwebsite\Excel\Excel::CSV
            );
        }

        return Excel::download(new LaporanExport($rows), $filename . '.xlsx');
    }

    private function resolvePeriode(string $periode): array
    {
        $today = Carbon::today();

        return match ($periode) {
            '2w' => [
                $today->copy()->subDays(13),
                $today,
                '2 Minggu Terakhir'
            ],
            '1m' => [
                $today->copy()->startOfMonth(),
                $today->copy()->endOfMonth(),
                'Bulan Ini'
            ],
            'prev1m' => [
                $today->copy()->subMonthNoOverflow()->startOfMonth(),
                $today->copy()->subMonthNoOverflow()->endOfMonth(),
                'Bulan Lalu'
            ],
            'all' => [null, null, 'Semua Waktu'],
            default => [
                $today->copy()->startOfMonth(),
                $today->copy()->endOfMonth(),
                'Bulan Ini'
            ],
        };
    }
}
