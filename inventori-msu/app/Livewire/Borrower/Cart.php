<?php

namespace App\Livewire\Borrower;

use Livewire\Component;

class Cart extends Component
{
    public $cart = [];
    public $borrower_name;
    public $borrower_email;
    public $borrower_phone;
    public $borrower_reason;
    public $loan_date_start;
    public $loan_date_end;

    protected $rules = [
        'borrower_name' => 'required|string|max:255',
        'borrower_email' => 'required|email|max:255',
        'borrower_phone' => 'required|string|max:20',
        'borrower_reason' => 'required|string',
        'loan_date_start' => 'required|date|after_or_equal:today',
        'loan_date_end' => 'required|date|after_or_equal:loan_date_start',
    ];

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    public function updateQuantity($id, $action)
    {
        if (!isset($this->cart[$id])) return;

        if ($action == 'inc') {
            if ($this->cart[$id]['category'] == 'barang' && $this->cart[$id]['quantity'] < $this->cart[$id]['max']) {
                $this->cart[$id]['quantity']++;
            }
        } elseif ($action == 'dec') {
            if ($this->cart[$id]['quantity'] > 1) {
                $this->cart[$id]['quantity']--;
            }
        }

        session()->put('cart', $this->cart);
        $this->dispatch('cart-updated');
    }

    public function removeItem($id)
    {
        unset($this->cart[$id]);
        session()->put('cart', $this->cart);
        $this->dispatch('cart-updated');
    }

    public function submit()
    {
        $this->validate();

        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang kosong.');
            return;
        }

        $loanRequest = \App\Models\LoanRequest::create([
            'borrower_name' => $this->borrower_name,
            'borrower_email' => $this->borrower_email,
            'borrower_phone' => $this->borrower_phone,
            'borrower_reason' => $this->borrower_reason,
            'loan_date_start' => $this->loan_date_start,
            'loan_date_end' => $this->loan_date_end,
            'status' => 'pending',
        ]);

        foreach ($this->cart as $item) {
            \App\Models\LoanItem::create([
                'loan_request_id' => $loanRequest->id,
                'inventory_id' => $item['id'],
                'quantity' => $item['quantity'],
            ]);
        }

        session()->forget('cart');
        $this->dispatch('cart-updated');

        return redirect()->route('success');
    }

    public function render()
    {
        return view('livewire.borrower.cart');
    }
}
