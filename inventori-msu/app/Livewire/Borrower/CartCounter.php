<?php

namespace App\Livewire\Borrower;

use Livewire\Component;
use Livewire\Attributes\On;

class CartCounter extends Component
{
    public $count = 0;

    public function mount()
    {
        $this->updateCount();
    }

    #[On('cart-updated')]
    public function updateCount()
    {
        $cart = session()->get('cart', []);
        $this->count = count($cart);
    }

    public function render()
    {
        return view('livewire.borrower.cart-counter');
    }
}
