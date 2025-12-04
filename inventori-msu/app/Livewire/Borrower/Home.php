<?php

namespace App\Livewire\Borrower;

use Livewire\Component;

class Home extends Component
{
    use HandlesCart;

    public $items;
    public $facilities;

    public function mount()
    {
        $this->items = \App\Models\Inventory::where('category', 'barang')->take(4)->get();
        $this->facilities = \App\Models\Inventory::where('category', 'fasilitas')->take(4)->get();
    }

    public function render()
    {
        return view('livewire.borrower.home');
    }
}
