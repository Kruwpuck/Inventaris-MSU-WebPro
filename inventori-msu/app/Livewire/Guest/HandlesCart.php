<?php

namespace App\Livewire\Guest;

use App\Models\Inventory;
use Illuminate\Support\Facades\Session;

trait HandlesCart
{
    public function getCart()
    {
        return Session::get('cart', []);
    }

    public function addToCart($id)
    {
        $item = Inventory::find($id);
        if (!$item) {
            session()->flash('error', 'Item tidak ditemukan.');
            return;
        }

        $cart = $this->getCart();
        $limit = $item->category == 'barang' ? $item->stock : 1;

        if (isset($cart[$id])) {
            if ($cart[$id]['quantity'] < $limit) {
                $cart[$id]['quantity']++;
                session()->flash('success', 'Jumlah item berhasil ditambahkan.');
            } else {
                session()->flash('error', 'Stok tidak mencukupi.');
                return;
            }
        } else {
            if ($limit < 1) {
                 session()->flash('error', 'Stok habis.');
                 return;
            }
            $cart[$id] = [
                'id' => $item->id,
                'name' => $item->name,
                'category' => $item->category,
                'image_path' => $item->image_path,
                'quantity' => 1,
                'max' => $limit,
            ];
            session()->flash('success', 'Berhasil menambahkan ' . $item->name . ' ke keranjang.');
        }

        Session::put('cart', $cart);
        $this->dispatch('cart-updated');
    }

    public function increment($id)
    {
        $cart = $this->getCart();
        if (!isset($cart[$id])) return;

        $item = Inventory::find($id);
        if (!$item) return;

        $limit = $item->category == 'barang' ? $item->stock : 1;

        if ($cart[$id]['quantity'] < $limit) {
            $cart[$id]['quantity']++;
            Session::put('cart', $cart);
            $this->dispatch('cart-updated');
        } else {
             $this->dispatch('toast', message: 'Stok maksimum tercapai.');
        }
    }

    public function decrement($id)
    {
        $cart = $this->getCart();
        if (!isset($cart[$id])) return;

        if ($cart[$id]['quantity'] > 1) {
            $cart[$id]['quantity']--;
        } else {
            unset($cart[$id]);
        }
        
        Session::put('cart', $cart);
        $this->dispatch('cart-updated');
    }

    public function removeFromCart($id)
    {
        $cart = $this->getCart();
        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
            $this->dispatch('cart-updated');
        }
    }

    public function clearCart()
    {
        Session::forget('cart');
        $this->dispatch('cart-updated');
    }
}
