<?php

namespace App\Livewire\Guest;

use Livewire\Component;
// use App\Livewire\Borrower\HandlesCart; (Removed)

class Catalogue extends Component
{
    use HandlesCart;

    public $category;
    public $items;
    public $search = '';

    public function mount()
    {
        // Infer category from route name or parameter
        // Since we will change routes to guest.*
        if (request()->routeIs('guest.catalogue.barang')) {
            $this->category = 'barang';
        } elseif (request()->routeIs('guest.catalogue.ruangan')) {
            $this->category = 'ruangan';
        } else {
            // Fallback check in case route names differ
            if(request()->segment(1) == 'ruangan'){
                $this->category = 'ruangan';
            } else {
                $this->category = 'barang';
            }
        }

        $this->loadItems();
    }

    public function updatedSearch()
    {
        $this->loadItems();
    }

    public function loadItems()
    {
        $this->items = \App\Models\Inventory::where('category', $this->category)
            ->where('name', 'like', '%' . $this->search . '%')
            ->get();
    }

    public function render()
    {
        return view('livewire.guest.catalogue');
    }
}
