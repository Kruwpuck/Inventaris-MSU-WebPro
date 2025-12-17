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
        $periode  = $request->query('periode', '1m');
        $kategori = $request->query('kategori', 'all');
        $status   = $request->query('status', 'all');
        $q        = $request->query('q', '');

        // khusus custom range
        $fromParam = $request->query('from'); // YYYY-MM-DD
        $toParam   = $request->query('to');   // YYYY-MM-DD

        // NOTE: loanRecord dipakai di mapping status -> wajib di-load
        $requests = LoanRequest::with(['items', 'loanRecord'])
            ->orderByDesc('id')
            ->get();

        $today = Carbon::today();

        // flatten loan_request -> item
        $rows = $requests->flatMap(function ($lr) use ($today) {
            return $lr->items->map(function ($inv) use ($lr, $today) {

                $tglPinjam  = Carbon::parse($lr->loan_date_start);
                $jatuhTempo = Carbon::parse($lr->loan_date_end);

                // map backend -> UI status
                $statusUi = 'Unknown';
                $tglKembali = '-';

                switch ($lr->status) {
                    case 'returned':
                        if ($lr->loanRecord && $lr->loanRecord->is_submitted) {
                            $statusUi = 'Sudah Kembali';
                        } else {
                            $statusUi = 'Menunggu Submit';
                        }

                        if ($lr->loanRecord && $lr->loanRecord->returned_at) {
                            $tglKembali = Carbon::parse($lr->loanRecord->returned_at)->format('m/d/Y');
                        } else {
                            $tglKembali = $jatuhTempo->format('m/d/Y');
                        }
                        break;

                    case 'handed_over':
                        $statusUi = $today->gt($jatuhTempo) ? 'Terlambat' : 'Sedang Dipinjam';
                        $tglKembali = '-';
                        break;

                    case 'approved':
                        $statusUi = 'Siap Diambil';
                        $tglKembali = '-';
                        break;

                    case 'pending':
                        $statusUi = 'Menunggu Approve';
                        $tglKembali = '-';
                        break;

                    case 'rejected':
                        $statusUi = 'Ditolak';
                        $tglKembali = '-';
                        break;

                    default:
                        $statusUi = $today->gt($jatuhTempo) ? 'Terlambat' : 'Sedang Dipinjam';
                        $tglKembali = '-';
                        break;
                }

                $kategoriUi = $inv->category === 'ruangan' ? 'Ruangan' : 'Barang';

                return [
                    'nama_item'   => $inv->name,
                    'kategori'    => $kategoriUi,
                    'peminjam'    => $lr->borrower_name,
                    'tgl_pinjam'  => $tglPinjam,   // Carbon
                    'jatuh_tempo' => $jatuhTempo,  // Carbon
                    'tgl_kembali' => $tglKembali,
                    'jumlah'      => (int) ($inv->pivot->quantity ?? 1),
                    'status'      => $statusUi,
                ];
            });
        })->values();

        // ✅ FILTER: hanya status yang ada di dropdown laporan
        $allowedStatuses = ['Sedang Dipinjam', 'Sudah Kembali', 'Terlambat', 'Siap Diambil'];
        $rows = $rows->filter(fn ($r) => in_array($r['status'], $allowedStatuses, true))->values();

        /**
         * ==========================
         * FILTER BERDASARKAN PERIODE
         * ==========================
         */
        [$from, $to, $periodeLabel] = $this->resolvePeriode($periode, $fromParam, $toParam);
        if ($from && $to) {
            // inclusive (tanggal awal & akhir ikut)
            $rows = $rows->filter(fn ($r) => $r['tgl_pinjam']->betweenIncluded($from, $to))->values();
        }

        /**
         * ==========================
         * FILTER KATEGORI
         * ==========================
         */
        if ($kategori !== 'all') {
            $rows = $rows->filter(fn ($r) => $r['kategori'] === $kategori)->values();
        }

        /**
         * ==========================
         * FILTER STATUS
         * ==========================
         */
        if ($status !== 'all') {
            $rows = $rows->filter(fn ($r) => $r['status'] === $status)->values();
        }

        /**
         * ==========================
         * SEARCH TEXT
         * ==========================
         */
        if ($q) {
            $qLower = strtolower($q);

            $rows = $rows->filter(function ($r) use ($qLower) {
                $blob =
                    strtolower($r['nama_item']) . ' ' .
                    strtolower($r['kategori']) . ' ' .
                    strtolower($r['peminjam']) . ' ' .
                    strtolower($r['status']) . ' ' .
                    $r['tgl_pinjam']->format('m/d/Y') . ' ' .
                    $r['jatuh_tempo']->format('m/d/Y') . ' ' .
                    strtolower((string) $r['tgl_kembali']) . ' ' .
                    (string) $r['jumlah'];

                return str_contains($blob, $qLower);
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
                'rows'          => $rows,
                'periode_label' => $periodeLabel,
                'kategori'      => $kategori,
                'status'        => $status,
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

    /**
     * resolve periode preset + custom (from/to)
     */
    private function resolvePeriode(string $periode, ?string $fromParam = null, ?string $toParam = null): array
    {
        $today = Carbon::today();

        if ($periode === 'custom') {
            if ($fromParam && $toParam) {
                try {
                    $from = Carbon::parse($fromParam)->startOfDay();
                    $to   = Carbon::parse($toParam)->endOfDay();

                    if ($from->gt($to)) {
                        [$from, $to] = [$to, $from];
                    }

                    return [
                        $from,
                        $to,
                        $from->format('m/d/Y') . ' - ' . $to->format('m/d/Y'),
                    ];
                } catch (\Throwable $e) {
                    return [null, null, 'Semua Waktu'];
                }
            }

            return [null, null, 'Semua Waktu'];
        }

        return match ($periode) {
            '2w' => [
                $today->copy()->subDays(13)->startOfDay(),
                $today->copy()->endOfDay(),
                '2 Minggu Terakhir'
            ],
            '1m' => [
                $today->copy()->startOfMonth()->startOfDay(),
                $today->copy()->endOfMonth()->endOfDay(),
                'Bulan Ini'
            ],
            'prev1m' => [
                $today->copy()->subMonthNoOverflow()->startOfMonth()->startOfDay(),
                $today->copy()->subMonthNoOverflow()->endOfMonth()->endOfDay(),
                'Bulan Lalu'
            ],
            'all' => [null, null, 'Semua Waktu'],
            default => [
                $today->copy()->startOfMonth()->startOfDay(),
                $today->copy()->endOfMonth()->endOfDay(),
                'Bulan Ini'
            ],
        };
    }
}
