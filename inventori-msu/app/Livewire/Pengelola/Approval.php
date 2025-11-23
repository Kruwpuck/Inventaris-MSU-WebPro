<?php

namespace App\Livewire\Pengelola;

use Livewire\Component;

class Approval extends Component
{
    public function render()
    {
        return view('livewire.pengelola.approval')
            ->layout('pengelola.layouts.pengelola');
    }
}
