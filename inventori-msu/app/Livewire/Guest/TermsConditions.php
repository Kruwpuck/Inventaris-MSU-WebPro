<?php

namespace App\Livewire\Guest;

use Livewire\Component;

class TermsConditions extends Component
{
    public function render()
    {
        return view('livewire.guest.terms-conditions')
            ->layout('components.layouts.app', ['title' => 'Syarat dan Ketentuan - MSU']);
    }
}
