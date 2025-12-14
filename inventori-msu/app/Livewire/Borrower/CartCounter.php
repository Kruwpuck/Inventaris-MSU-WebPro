<?php

namespace App\Livewire\Borrower;

use Livewire\Component;
use Livewire\Attributes\On;

class CartCounter extends Component
{
    public $count = 0;
    public $type = 'navbar';

    public function mount($type = 'navbar')
    {
        $this->type = $type;
        $this->updateCount();
    }

    #[On('cart-updated')]
    public function updateCount()
    {
        $cart = session()->get('cart', []);
        $this->count = count($cart);
    }

    #[On('clear-cart')]
    public function clearCart()
    {
        session()->forget('cart');
        $this->count = 0;
    }

    public function render()
    {
        return view('livewire.borrower.cart-counter');
    }
}
