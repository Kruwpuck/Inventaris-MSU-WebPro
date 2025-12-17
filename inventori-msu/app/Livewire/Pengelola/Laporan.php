<?php

namespace App\Livewire\Pengelola;

use Livewire\Component;
use App\Models\LoanRequest;
use Carbon\Carbon;

class Laporan extends Component
{
    public $q = '';

    // filter state (tetap dipakai JS di blade)
    public $vPeriode = 'all';   // 2w | 1m | prev1m | all
    public $vKategori = 'all';  // all | Barang | Ruangan
    public $vStatus = 'all';    // all | Sedang Dipinjam | Sudah Kembali | Terlambat | Siap Diambil

    public function render()
    {
        // ambil semua loan + items + inventory
        $requests = LoanRequest::with(['items', 'loanRecord'])
            ->orderByDesc('id')
            ->get();

        $today = Carbon::today();

        // flatten: 1 loan_request bisa punya banyak item
        $laporans = $requests->flatMap(function ($lr) use ($today) {
            return $lr->items->map(function ($inv) use ($lr, $today) {

                $tglPinjam = Carbon::parse($lr->loan_date_start);
                $jatuhTempo = Carbon::parse($lr->loan_date_end);

                // ===== map status backend -> UI =====
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
                    
                    // ... other cases (handed_over, etc) - logic unchanged for statusUi mapping ...
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

                return (object) [
                    'nama_item'    => $inv->name,
                    'kategori'     => $kategoriUi,
                    'peminjam'     => $lr->borrower_name,
                    'waktu_pinjam' => $waktuPinjamStr,
                    'jatuh_tempo'  => $jatuhTempoStr,
                    'waktu_kembali'=> $waktuKembaliStr,
                    'jumlah'       => (int) ($inv->pivot->quantity ?? 1),
                    'status'       => $statusUi,
                    'keterangan'   => optional($lr->loanRecord)->notes ?? '-'
                ];
            });
        });

        // ✅ FILTER: hanya status yang ada di dropdown laporan
        $allowedStatuses = ['Sedang Dipinjam', 'Sudah Kembali', 'Terlambat', 'Siap Diambil', 'Selesai'];
        $laporans = $laporans->filter(fn ($r) => in_array($r->status, $allowedStatuses, true))->values();

        // optional: search backend biar list gak terlalu banyak
        if ($this->q) {
            $q = strtolower($this->q);
            $laporans = $laporans->filter(function ($r) use ($q) {
                return str_contains(strtolower($r->nama_item), $q)
                    || str_contains(strtolower($r->kategori), $q)
                    || str_contains(strtolower($r->peminjam), $q)
                    || str_contains(strtolower($r->status), $q);
            })->values();
        }

        return view('livewire.pengelola.laporan', [
            'laporans' => $laporans,
        ])->layout('pengelola.layouts.pengelola');
    }
}
