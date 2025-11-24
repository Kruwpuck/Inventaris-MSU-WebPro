<?php

namespace App\Livewire\Pengelola;

use Livewire\Component;

class Beranda extends Component
{
    public $q = '';

    public function render()
    {
        // =========================
        // DUMMY DATA BARANG (sesuai HTML)
        // =========================
        $barangs = collect([
            (object)[
                'id' => 1,
                'nama' => 'Set Proyektor',
                'deskripsi' => 'Unit proyektor untuk kegiatan presentasi.',
                'stok' => 5,
                'gambar' => 'aset/proyektor.png',
                'is_active' => true,
            ],
            (object)[
                'id' => 2,
                'nama' => 'Set Sound System Portable',
                'deskripsi' => 'Speaker untuk kegiatan indoor dan outdoor.',
                'stok' => 8,
                'gambar' => 'aset/speaker.png',
                'is_active' => true,
            ],
            (object)[
                'id' => 3,
                'nama' => 'Terpal',
                'deskripsi' => 'Terpal untuk kegiatan luar ruangan.',
                'stok' => 10,
                'gambar' => 'aset/terpal.png',
                'is_active' => true,
            ],
            (object)[
                'id' => 4,
                'nama' => 'Akun Zoom MSU',
                'deskripsi' => 'Akun Zoom resmi untuk kegiatan online MSU.',
                'stok' => 6,
                'gambar' => 'aset/zoom.jpg',
                'is_active' => true,
            ],
            (object)[
                'id' => 5,
                'nama' => 'Karpet',
                'deskripsi' => 'Karpet untuk aula dan ruangan besar.',
                'stok' => 12,
                'gambar' => 'aset/karpet.jpeg',
                'is_active' => true,
            ],
            (object)[
                'id' => 6,
                'nama' => 'Peralatan Bukber',
                'deskripsi' => 'Peralatan tambahan untuk kegiatan MSU.',
                'stok' => 6,
                'gambar' => 'aset/buker.jpg',
                'is_active' => true,
            ],
            (object)[
                'id' => 7,
                'nama' => 'Hijab',
                'deskripsi' => 'Hijab seragam untuk kegiatan MSU.',
                'stok' => 6,
                'gambar' => 'aset/hijab.png',
                'is_active' => true,
            ],
            (object)[
                'id' => 8,
                'nama' => 'Meja',
                'deskripsi' => 'Meja serbaguna untuk kegiatan belajar.',
                'stok' => 4,
                'gambar' => 'aset/meja.png',
                'is_active' => true,
            ],
            (object)[
                'id' => 9,
                'nama' => 'Sofa',
                'deskripsi' => 'Sofa nyaman untuk ruang tamu dan santai.',
                'stok' => 3,
                'gambar' => 'aset/sofa.webp',
                'is_active' => true,
            ],
        ]);

        // =========================
        // DUMMY DATA FASILITAS (sesuai HTML)
        // =========================
        $fasilitas = collect([
            (object)[
                'id' => 1,
                'nama' => 'Ruang Utama',
                'deskripsi' => 'Kapasitas besar untuk acara utama.',
                'status' => 'Tersedia',
                'gambar' => 'aset/ruang utama.jpg',
                'is_active' => true,
            ],
            (object)[
                'id' => 2,
                'nama' => 'Ruang Tamu VIP',
                'deskripsi' => 'Kapasitas 10 orang untuk tamu penting.',
                'status' => 'Tersedia',
                'gambar' => 'aset/ruangTamu.jpg',
                'is_active' => true,
            ],
            (object)[
                'id' => 3,
                'nama' => 'Plaza',
                'deskripsi' => 'Area outdoor serbaguna di depan masjid.',
                'status' => 'Tersedia',
                'gambar' => 'aset/plaza.jpg',
                'is_active' => true,
            ],
            (object)[
                'id' => 4,
                'nama' => 'Selasar/Teras Utara Masjid',
                'deskripsi' => 'Area teras di sisi utara masjid.',
                'status' => 'Tersedia',
                'gambar' => 'aset/selasar.jpg',
                'is_active' => true,
            ],
            (object)[
                'id' => 5,
                'nama' => 'Lantai 2 Ruang Kaca',
                'deskripsi' => 'Ruang pertemuan indoor di lantai 2.',
                'status' => 'Tersedia',
                'gambar' => 'aset/ruangKaca.jpg',
                'is_active' => true,
            ],
        ]);

        // =========================
        // FILTER SEARCH (untuk dummy biar search jalan)
        // =========================
        if ($this->q) {
            $keyword = strtolower($this->q);

            $barangs = $barangs->filter(function ($b) use ($keyword) {
                return str_contains(strtolower($b->nama), $keyword)
                    || str_contains(strtolower($b->deskripsi), $keyword);
            })->values();

            $fasilitas = $fasilitas->filter(function ($f) use ($keyword) {
                return str_contains(strtolower($f->nama), $keyword)
                    || str_contains(strtolower($f->deskripsi), $keyword);
            })->values();
        }

        return view('livewire.pengelola.beranda', [
            'barangs' => $barangs,
            'fasilitas' => $fasilitas,
        ])->layout('pengelola.layouts.pengelola');
    }
}
