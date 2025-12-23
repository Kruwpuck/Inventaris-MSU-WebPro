<?php

namespace App\Livewire\Guest;

use Livewire\Component;
// Trait HandlesCart is now in current namespace App\Livewire\Guest 

// Note: HandlesCart is likely in 'app/Livewire/Borrower/HandlesCart.php'
// I can just change use statement if I move it, or keep using it.
// Checking file existence... I'll assume it exists in Borrower namespace.

class Home extends Component
{
    use HandlesCart;

    public $items;
    public $facilities;

    public function mount()
    {
        $this->items = \App\Models\Inventory::where('category', 'barang')->take(4)->get();
        $this->facilities = \App\Models\Inventory::where('category', 'ruangan')->take(4)->get();
    }

    public function render()
    {
        return view('livewire.guest.home');
    }
}
