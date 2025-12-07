<?php

namespace App\Livewire\Pengelola;

use Livewire\Component;
use App\Models\LoanRequest;
use Carbon\Carbon;

class Laporan extends Component
{
    public $q = '';

    // filter state (tetap dipakai JS di blade)
    public $vPeriode  = '1m';   // 2w | 1m | prev1m | all
    public $vKategori = 'all';  // all | Barang | Ruangan
    public $vStatus   = 'all';  // all | Sedang Dipinjam | Sudah Kembali | Terlambat

    public function render()
    {
        // ambil semua loan + items + inventory
        $requests = LoanRequest::with(['items'])
            ->orderByDesc('id')
            ->get();

        $today = Carbon::today();

        // flatten: 1 loan_request bisa punya banyak item
        $laporans = $requests->flatMap(function ($lr) use ($today) {
            return $lr->items->map(function ($inv) use ($lr, $today) {

                $tglPinjam  = Carbon::parse($lr->loan_date_start);
                $jatuhTempo = Carbon::parse($lr->loan_date_end);

                // ===== map status backend -> UI =====
                if ($lr->status === 'returned') {
                    $statusUi   = 'Sudah Kembali';
                    $tglKembali = $jatuhTempo->format('m/d/Y'); // sementara pakai loan_date_end
                } else {
                    if ($today->gt($jatuhTempo)) {
                        $statusUi = 'Terlambat';
                    } else {
                        $statusUi = 'Sedang Dipinjam';
                    }
                    $tglKembali = '-';
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
