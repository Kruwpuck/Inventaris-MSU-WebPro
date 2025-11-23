<?php

namespace App\Livewire\Pengelola;

use Livewire\Component;

class Laporan extends Component
{
    public $q = '';

    // filter state (dummy mode)
    public $vPeriode = '1m'; // 2w | 1m | prev1m | all
    public $vKategori = 'all'; // all | Barang | Ruangan
    public $vStatus = 'all';   // all | Sedang Dipinjam | Sudah Kembali | Terlambat

    public function render()
    {
        // =========================
        // DUMMY DATA LAPORAN (sesuai HTML kamu)
        // =========================
        $laporans = collect([
            (object)[
                'nama_item' => 'Proyektor',
                'kategori' => 'Barang',
                'peminjam' => 'Ruslan Ismail',
                'tgl_pinjam' => '10/01/2024',
                'jatuh_tempo' => '10/03/2024',
                'tgl_kembali' => '10/03/2024',
                'jumlah' => 2,
                'status' => 'Sudah Kembali',
            ],
            (object)[
                'nama_item' => 'Karpet',
                'kategori' => 'Barang',
                'peminjam' => 'Buana Ahmad',
                'tgl_pinjam' => '10/26/2024',
                'jatuh_tempo' => '11/02/2024',
                'tgl_kembali' => '-',
                'jumlah' => 1,
                'status' => 'Sedang Dipinjam',
            ],
            (object)[
                'nama_item' => 'Speaker',
                'kategori' => 'Barang',
                'peminjam' => 'Hendra Saputra',
                'tgl_pinjam' => '10/10/2024',
                'jatuh_tempo' => '10/15/2024',
                'tgl_kembali' => '10/17/2024',
                'jumlah' => 1,
                'status' => 'Terlambat',
            ],
            (object)[
                'nama_item' => 'Ruang Utama',
                'kategori' => 'Ruangan',
                'peminjam' => 'Ahmad Abdullah',
                'tgl_pinjam' => '09/26/2024',
                'jatuh_tempo' => '09/27/2024',
                'tgl_kembali' => '09/27/2024',
                'jumlah' => 1,
                'status' => 'Sudah Kembali',
            ],
            (object)[
                'nama_item' => 'Pelataran Masjid',
                'kategori' => 'Ruangan',
                'peminjam' => 'Ismail Sulaiman',
                'tgl_pinjam' => '10/25/2024',
                'jatuh_tempo' => '10/29/2024',
                'tgl_kembali' => '-',
                'jumlah' => 1,
                'status' => 'Sedang Dipinjam',
            ],
            (object)[
                'nama_item' => 'Meja Kayu',
                'kategori' => 'Barang',
                'peminjam' => 'Putra Idris',
                'tgl_pinjam' => '09/29/2024',
                'jatuh_tempo' => '10/01/2024',
                'tgl_kembali' => '10/01/2024',
                'jumlah' => 3,
                'status' => 'Sudah Kembali',
            ],
            (object)[
                'nama_item' => 'Terpal',
                'kategori' => 'Barang',
                'peminjam' => 'Wira Cahya',
                'tgl_pinjam' => '10/20/2024',
                'jatuh_tempo' => '10/24/2024',
                'tgl_kembali' => '10/26/2024',
                'jumlah' => 2,
                'status' => 'Terlambat',
            ],
            (object)[
                'nama_item' => 'Hijab',
                'kategori' => 'Barang',
                'peminjam' => 'Dina Rahma',
                'tgl_pinjam' => '10/27/2024',
                'jatuh_tempo' => '11/03/2024',
                'tgl_kembali' => '-',
                'jumlah' => 4,
                'status' => 'Sedang Dipinjam',
            ],
            (object)[
                'nama_item' => 'Selasar Selatan',
                'kategori' => 'Ruangan',
                'peminjam' => 'Fatimah',
                'tgl_pinjam' => '10/02/2024',
                'jatuh_tempo' => '10/02/2024',
                'tgl_kembali' => '10/02/2024',
                'jumlah' => 1,
                'status' => 'Sudah Kembali',
            ],
            (object)[
                'nama_item' => 'Sofa',
                'kategori' => 'Barang',
                'peminjam' => 'Ridho Ali',
                'tgl_pinjam' => '10/25/2024',
                'jatuh_tempo' => '11/01/2024',
                'tgl_kembali' => '-',
                'jumlah' => 2,
                'status' => 'Sedang Dipinjam',
            ],
            (object)[
                'nama_item' => 'Akun Zoom MSU',
                'kategori' => 'Barang',
                'peminjam' => 'Nur Halimah',
                'tgl_pinjam' => '09/18/2024',
                'jatuh_tempo' => '09/19/2024',
                'tgl_kembali' => '09/19/2024',
                'jumlah' => 1,
                'status' => 'Sudah Kembali',
            ],
            (object)[
                'nama_item' => 'Lantai 2 Timur',
                'kategori' => 'Ruangan',
                'peminjam' => 'Muhammad Zaki',
                'tgl_pinjam' => '10/26/2024',
                'jatuh_tempo' => '10/30/2024',
                'tgl_kembali' => '-',
                'jumlah' => 1,
                'status' => 'Sedang Dipinjam',
            ],
            (object)[
                'nama_item' => 'Meja',
                'kategori' => 'Barang',
                'peminjam' => 'Rahman Mansur',
                'tgl_pinjam' => '10/26/2022',
                'jatuh_tempo' => '10/29/2022',
                'tgl_kembali' => '10/29/2022',
                'jumlah' => 2,
                'status' => 'Sudah Kembali',
            ],
            (object)[
                'nama_item' => 'Proyektor',
                'kategori' => 'Barang',
                'peminjam' => 'Putri Melati',
                'tgl_pinjam' => '10/17/2024',
                'jatuh_tempo' => '10/20/2024',
                'tgl_kembali' => '10/22/2024',
                'jumlah' => 1,
                'status' => 'Terlambat',
            ],
            (object)[
                'nama_item' => 'Ruang Tamu VIP',
                'kategori' => 'Ruangan',
                'peminjam' => 'Salim Yusuf',
                'tgl_pinjam' => '10/15/2024',
                'jatuh_tempo' => '10/15/2024',
                'tgl_kembali' => '10/15/2024',
                'jumlah' => 1,
                'status' => 'Sudah Kembali',
            ],
        ]);

        return view('livewire.pengelola.laporan', [
            'laporans' => $laporans,
        ])->layout('pengelola.layouts.pengelola');
    }
}
