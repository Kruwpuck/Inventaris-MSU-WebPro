<?php

namespace App\Livewire\Borrower;

use Livewire\Component;

class Catalogue extends Component
{
    use HandlesCart;

    public $category;
    public $items;
    public $search = '';

    public function mount()
    {
        if (request()->routeIs('catalogue.barang')) {
            $this->category = 'barang';
        } elseif (request()->routeIs('catalogue.ruangan')) {
            $this->category = 'fasilitas';
        } else {
            $this->category = 'barang'; // Default
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
        return view('livewire.borrower.catalogue');
    }
}
