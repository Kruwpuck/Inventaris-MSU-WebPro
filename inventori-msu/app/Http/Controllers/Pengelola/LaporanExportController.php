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
        // 1. Get Filters from Query String
        // Note: 'periode' param is legacy/ignored if using from/to, but kept for compatibility
        $kategori = $request->query('kategori', 'all');
        $status   = $request->query('status', 'all');
        $q        = $request->query('q', '');
        
        $dateFrom = $request->query('from'); // YYYY-MM-DD
        $dateTo   = $request->query('to');   // YYYY-MM-DD

        // 2. Base Query & Date Filter (Match Laporan.php)
        $query = LoanRequest::with(['items', 'loanRecord'])->orderByDesc('id');

        if ($dateFrom) {
            $query->whereDate('loan_date_start', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('loan_date_start', '<=', $dateTo);
        }

        $requests = $query->get();
        $today = Carbon::today();

        // 3. Transform & Map (Match Laporan.php)
        $rows = $requests->flatMap(function ($lr) use ($today) {
            return $lr->items->map(function ($inv) use ($lr, $today) {

                $tglPinjam  = Carbon::parse($lr->loan_date_start);
                $jatuhTempo = Carbon::parse($lr->loan_date_end);
                
                $startTime = $lr->start_time ?? '00:00:00';
                $endTime   = $lr->end_time ?? '00:00:00';

                // Format: d/m/Y | H:i
                $waktuPinjamStr = $tglPinjam->format('d/m/Y') . ' | ' . Carbon::parse($startTime)->format('H:i');
                $jatuhTempoStr  = $jatuhTempo->format('d/m/Y') . ' | ' . Carbon::parse($endTime)->format('H:i');

                // Status Logic
                $statusUi = 'Unknown';
                $waktuKembaliStr = '-';

                switch ($lr->status) {
                    case 'returned':
                        $waktuKembaliStr = '-';
                        $actualReturn = null;
                        if ($lr->loanRecord && $lr->loanRecord->returned_at) {
                            $actualReturn = Carbon::parse($lr->loanRecord->returned_at);
                            $waktuKembaliStr = $actualReturn->format('d/m/Y | H:i');
                        } else {
                            $waktuKembaliStr = $jatuhTempo->format('d/m/Y | H:i'); 
                        }
                        
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
                    
                    // Display
                    'waktu_pinjam'     => $waktuPinjamStr,
                    'jatuh_tempo'      => $jatuhTempoStr,
                    'waktu_kembali'    => $waktuKembaliStr,

                    'jumlah'           => (int) ($inv->pivot->quantity ?? 1),
                    'status'           => $statusUi,
                    'keterangan'       => optional($lr->loanRecord)->notes ?? '-'
                ];
            });
        })->values();

        // 4. In-Memory Filtering (Match Laporan.php)
        
        // Strict Allowed Statuses
        $allowedStatuses = ['Sedang Dipinjam', 'Sudah Kembali', 'Terlambat', 'Siap Diambil', 'Selesai'];
        $rows = $rows->filter(fn ($r) => in_array($r['status'], $allowedStatuses, true));

        // Filter Category
        if ($kategori !== 'all') {
            $rows = $rows->filter(fn ($r) => $r['kategori'] === $kategori);
        }

        // Filter Status
        if ($status !== 'all') {
            $rows = $rows->filter(fn ($r) => $r['status'] === $status);
        }

        // Search
        if ($q) {
            $qLower = strtolower($q);
            $rows = $rows->filter(function ($r) use ($qLower) {
                return str_contains(strtolower($r['nama_item']), $qLower)
                    || str_contains(strtolower($r['kategori']), $qLower)
                    || str_contains(strtolower($r['peminjam']), $qLower)
                    || str_contains(strtolower($r['status']), $qLower);
            });
        }
        
        $rows = $rows->values();

        // 5. Generate Export
        $filename = 'laporan_peminjaman_' . now()->format('dmY_His');
        $periodeLabel = ($dateFrom ? Carbon::parse($dateFrom)->format('d/m/Y') : '...') . ' - ' . ($dateTo ? Carbon::parse($dateTo)->format('d/m/Y') : '...');
        if (!$dateFrom && !$dateTo) $periodeLabel = 'Semua Waktu';

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.laporan-pdf', [
                'rows'          => $rows,
                'periode_label' => $periodeLabel,
                'kategori'      => $kategori === 'all' ? 'Semua Kategori' : $kategori,
                'status'        => $status === 'all' ? 'Semua Status' : $status,
            ])->setPaper('a4', 'landscape');

            return $pdf->download($filename . '.pdf');
        }

        if ($format === 'csv') {
            return Excel::download(
                new LaporanExport($rows),
                $filename . '.csv',
                \Maatwebsite\Excel\Excel::CSV
            );
        }

        return Excel::download(new LaporanExport($rows), $filename . '.xlsx');
    }
}
