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

                // ===== map status backend -> UI (LOGIC COPIED FROM Laporan.php) =====
                $startTime = $lr->start_time ?? '00:00:00';
                $endTime   = $lr->end_time ?? '00:00:00';

                // Format: Date | Time
                // Tgl Pinjam = loan_date_start + start_time
                $waktuPinjamStr = $tglPinjam->format('m/d/Y') . ' | ' . \Carbon\Carbon::parse($startTime)->format('H:i');
                
                // Jatuh Tempo = loan_date_end + end_time
                $jatuhTempoStr = $jatuhTempo->format('m/d/Y') . ' | ' . \Carbon\Carbon::parse($endTime)->format('H:i');

                // ===== map status backend -> UI =====
                $statusUi = 'Unknown';
                $waktuKembaliStr = '-';

                switch ($lr->status) {
                    case 'returned':
                        $waktuKembaliStr = '-';
                        $actualReturn = null;

                        if ($lr->loanRecord && $lr->loanRecord->returned_at) {
                            $actualReturn = Carbon::parse($lr->loanRecord->returned_at);
                            // Format: Date | Time
                            $waktuKembaliStr = $actualReturn->format('m/d/Y | H:i');
                        } else {
                            // Fallback if returned but no timestamp (unlikely)
                            $waktuKembaliStr = $jatuhTempo->format('m/d/Y | H:i'); 
                        }
                        
                        // Status logic remains same
                        if ($lr->loanRecord && $lr->loanRecord->is_submitted) {
                            if ($actualReturn && $actualReturn->gt($jatuhTempo)) {
                                $statusUi = 'Terlambat';
                            } else {
                                $statusUi = 'Selesai';
                            }
                        } else {
                             $statusUi = 'Sudah Kembali';
                        }
                        break;
                    
                    case 'handed_over':
                        $statusUi = $today->gt($jatuhTempo) ? 'Terlambat' : 'Sedang Dipinjam';
                        break;

                    case 'approved':
                        $statusUi = 'Siap Diambil';
                        break;

                    case 'pending':
                        $statusUi = 'Menunggu Approve';
                        break;

                    case 'rejected':
                        $statusUi = 'Ditolak';
                        break;

                    default:
                        $statusUi = $today->gt($jatuhTempo) ? 'Terlambat' : 'Sedang Dipinjam';
                        break;
                }

                $kategoriUi = $inv->category === 'ruangan' ? 'Ruangan' : 'Barang';

                return [
                    'nama_item'        => $inv->name,
                    'kategori'         => $kategoriUi,
                    'peminjam'         => $lr->borrower_name,
                    
                    // Fields for Display (Export)
                    'waktu_pinjam'     => $waktuPinjamStr,
                    'jatuh_tempo'      => $jatuhTempoStr,
                    'waktu_kembali'    => $waktuKembaliStr,
                    
                    // Fields for Filter
                    '_raw_tgl_pinjam'  => $tglPinjam,
                    '_raw_jatuh_tempo' => $jatuhTempo,

                    'jumlah'           => (int) ($inv->pivot->quantity ?? 1),
                    'status'           => $statusUi,
                    'keterangan'       => optional($lr->loanRecord)->notes ?? '-'
                ];
            });
        })->values();

        // ✅ FILTER: hanya status yang ada di dropdown laporan (MATCH UI)
        $allowedStatuses = ['Sedang Dipinjam', 'Sudah Kembali', 'Terlambat', 'Siap Diambil', 'Selesai'];
        $rows = $rows->filter(fn ($r) => in_array($r['status'], $allowedStatuses, true))->values();

        /**
         * ==========================
         * FILTER BERDASARKAN PERIODE
         * ==========================
         */
        $periodeLabel = 'Semua Waktu';
        
        if ($periode === 'custom') {
            // Flexible Custom Filter (Start-only, End-only, or Both)
            $from = $fromParam ? Carbon::parse($fromParam)->startOfDay() : null;
            $to   = $toParam   ? Carbon::parse($toParam)->endOfDay()   : null;

            if ($from && $to && $from->gt($to)) {
                 [$from, $to] = [$to, $from];
            }

            if ($from || $to) {
                $rows = $rows->filter(function ($r) use ($from, $to) {
                    $d = $r['_raw_tgl_pinjam'];
                    if ($from && $d->lt($from)) return false;
                    if ($to && $d->gt($to)) return false;
                    return true;
                })->values();

                if ($from && $to) {
                     $periodeLabel = $from->format('m/d/Y') . ' - ' . $to->format('m/d/Y');
                } elseif ($from) {
                     $periodeLabel = 'Dari ' . $from->format('m/d/Y');
                } elseif ($to) {
                     $periodeLabel = 'Sampai ' . $to->format('m/d/Y');
                }
            }

        } else {
            // Preset Periode
            [$from, $to, $label] = $this->resolvePeriode($periode);
            $periodeLabel = $label;
            
            if ($from && $to) {
                $rows = $rows->filter(fn ($r) => $r['_raw_tgl_pinjam']->betweenIncluded($from, $to))->values();
            }
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
                    strtolower($r['waktu_pinjam']) . ' ' .
                    strtolower($r['jatuh_tempo']) . ' ' .
                    strtolower($r['waktu_kembali']) . ' ' .
                    strtolower($r['keterangan']) . ' ' .
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
     * resolve periode preset
     */
    private function resolvePeriode(string $periode): array
    {
        $today = Carbon::today();

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
            'all', 'custom' => [null, null, 'Semua Waktu'], // 'custom' logic handled in main
            default => [
                $today->copy()->startOfMonth()->startOfDay(),
                $today->copy()->endOfMonth()->endOfDay(),
                'Bulan Ini'
            ],
        };
    }
}
