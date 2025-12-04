<?php

namespace App\Livewire\Borrower;

use App\Models\Inventory;
use Illuminate\Support\Facades\Session;

trait HandlesCart
{
    public function addToCart($id)
    {
        $item = Inventory::find($id);
        if (!$item) {
            return;
        }

        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            // Check stock limit
            if ($item->category == 'barang' && $cart[$id]['quantity'] < $item->stock) {
                $cart[$id]['quantity']++;
            } elseif ($item->category == 'fasilitas') {
                // Facilities usually 1 per booking? Or multiple?
                // Assuming 1 for now based on HTML "data-max=1"
                $cart[$id]['quantity'] = 1;
            }
        } else {
            $cart[$id] = [
                'id' => $item->id,
                'name' => $item->name,
                'category' => $item->category,
                'image_path' => $item->image_path,
                'quantity' => 1,
                'max' => $item->category == 'barang' ? $item->stock : 1,
            ];
        }

        Session::put('cart', $cart);
        $this->dispatch('cart-updated');
        session()->flash('success', 'Berhasil menambahkan ' . $item->name . ' ke keranjang.');
    }
}
