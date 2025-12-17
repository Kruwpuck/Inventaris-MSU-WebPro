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
                $statusUi = 'Unknown';
                $tglKembali = '-';

                switch ($lr->status) {
                    case 'returned':
                        // (NOTE) di laporan kamu sebelumnya ada "Menunggu Submit".
                        // Karena dropdown kamu TIDAK punya itu, biarkan tetap mapping,
                        // nanti akan ter-filter dan tidak akan tampil.
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
                    'nama_item'   => $inv->name,
                    'kategori'    => $kategoriUi,
                    'peminjam'    => $lr->borrower_name,
                    'tgl_pinjam'  => $tglPinjam->format('m/d/Y'),
                    'jatuh_tempo' => $jatuhTempo->format('m/d/Y'),
                    'tgl_kembali' => $tglKembali,
                    'jumlah'      => (int) ($inv->pivot->quantity ?? 1),
                    'status'      => $statusUi,
                ];
            });
        });

        // ✅ FILTER: hanya status yang ada di dropdown laporan
        $allowedStatuses = ['Sedang Dipinjam', 'Sudah Kembali', 'Terlambat', 'Siap Diambil'];
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
