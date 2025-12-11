<?php

namespace App\Livewire\Borrower;

use Livewire\Component;
use Livewire\WithFileUploads;

class Cart extends Component
{
    use WithFileUploads;

    public $cart_json; 

    // Borrower Details
    public $borrower_name;
    public $borrower_phone;
    public $borrower_email;
    public $borrower_reason;
    public $borrower_nim;
    public $borrower_prodi;
    public $borrower_description;
    
    // Loan Details
    public $loan_date_start;
    public $loan_time_start;
    public $loan_duration;
    
    public $donation_amount;
    public $document_file; 

    protected $rules = [
        'borrower_name' => 'required',
        'borrower_email' => 'required|email',
        'borrower_phone' => 'required',
        'borrower_reason' => 'required',
        'loan_date_start' => 'required|date',
        'borrower_nim' => 'required',
        'borrower_prodi' => 'required',
        'loan_time_start' => 'required',
        'loan_duration' => 'required',
        'document_file' => 'required|file|max:10240', // 10MB
    ];

    public function submit()
    {
        $this->validate();

        // Parse Cart JSON
        $items = json_decode($this->cart_json, true);
        if (!$items || count($items) === 0) {
            // Log::info("Cart empty, but proceeding for debugging.");
            $items = []; 
        }

        // Save File
        $path = $this->document_file->store('documents', 'public');

        // Calculate DateTime
        try {
            $startDateTime = \Carbon\Carbon::parse($this->loan_date_start . ' ' . $this->loan_time_start);
            $endDateTime = $startDateTime->copy()->addHours((int)$this->loan_duration);
        } catch (\Exception $e) {
            $startDateTime = now();
            $endDateTime = now()->addHour();
        }

        // Create Loan Request
        // Note: combining local reason + long description
        $fullReason = $this->borrower_reason; 
        if ($this->borrower_description) {
            $fullReason .= " (" . $this->borrower_description . ")";
        }

        // Using DB transaction
        \Illuminate\Support\Facades\DB::transaction(function() use ($items, $path, $startDateTime, $endDateTime, $fullReason) {
            
            $loan = \App\Models\LoanRequest::create([
                'borrower_name' => $this->borrower_name,
                'borrower_email' => $this->borrower_email,
                'borrower_phone' => $this->borrower_phone,
                'borrower_reason' => $fullReason,
                'loan_date_start' => $startDateTime,
                'loan_date_end' => $endDateTime,
                'status' => 'pending',
                // 'document_path' => $path, // If you add this column later
            ]);

            foreach($items as $cartItem) {
                // Find inventory by name (fallback since we didn't store IDs in cart JS yet)
                $inv = \App\Models\Inventory::where('name', $cartItem['name'])->first();
                
                if ($inv) {
                     // Check if item already added (unique constraint)
                    $exists = \App\Models\LoanItem::where('loan_request_id', $loan->id)
                                ->where('inventory_id', $inv->id)
                                ->exists();
                    
                    if (!$exists) {
                        \App\Models\LoanItem::create([
                            'loan_request_id' => $loan->id,
                            'inventory_id' => $inv->id,
                            'quantity' => (int)($cartItem['qty'] ?? 1)
                        ]);
                    }
                }
            }
        });

        // Clear Server Session Cart
        session()->forget('cart'); 

        return redirect()->route('success')->with('success', 'Peminjaman berhasil diajukan! Silahkan tunggu persetujuan pengelola.');
    }

    public function render()
    {
        return view('livewire.borrower.cart');
    }
}
