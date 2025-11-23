<?php

namespace App\Livewire\Pengelola;

use Livewire\Component;

class TambahHapus extends Component
{
    // masih dummy, belum simpan ke DB
    public function render()
    {
        return view('livewire.pengelola.tambah-hapus')
            ->layout('pengelola.layouts.pengelola');
    }
}
