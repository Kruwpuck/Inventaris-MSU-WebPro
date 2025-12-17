<?php

namespace App\Livewire\Guest;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Livewire\Borrower\HandlesCart;

class Cart extends Component
{
    use WithFileUploads, HandlesCart;

    // Borrower Details
    public $activeItemId; // For Tabs UI
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

    // file proposal/dokumen dari guest
    public $document_file;

    protected $listeners = ['cart-updated' => '$refresh'];

    public function setActiveItem($id)
    {
        $this->activeItemId = $id;
    }

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

    public function messages()
    {
        return [
            'borrower_name.required' => 'Nama penanggung jawab wajib diisi.',
            'borrower_email.required' => 'Email wajib diisi.',
            'borrower_email.email' => 'Format email tidak valid.',
            'borrower_phone.required' => 'Nomor telepon wajib diisi.',
            'borrower_reason.required' => 'Keperluan wajib diisi.',
            'loan_date_start.required' => 'Tanggal peminjaman wajib diisi.',
            'borrower_nim.required' => 'NIM/NIP wajib diisi.',
            'borrower_prodi.required' => 'Program studi / Unit wajib diisi.',
            'loan_time_start.required' => 'Jam mulai wajib diisi.',
            'loan_duration.required' => 'Durasi wajib diisi.',
            'document_file.required' => 'Dokumen persyaratan wajib diunggah.',
            'document_file.max' => 'Ukuran file maksimal 10MB.',
            'borrower_description.required' => 'Deskripsi keperluan wajib diisi.',
        ];
    }

    public function submit()
    {
        $this->validate();

        $cart = $this->getCart();
        if (!$cart || count($cart) === 0) {
            session()->flash('error', 'Keranjang kosong.');
            return;
        }

        // Validate Stock Availability
        foreach ($cart as $cartItem) {
            $inv = \App\Models\Inventory::find($cartItem['id']);
            if (!$inv) {
                session()->flash('error', "Item tidak ditemukan dalam database.");
                return;
            }

            // Check stock limit
            if ($inv->category == 'barang' && $cartItem['quantity'] > $inv->stock) {
                session()->flash('error', "Stok untuk '{$inv->name}' tidak mencukupi (Tersedia: {$inv->stock}, Diminta: {$cartItem['quantity']}).");
                return;
            }
        }

        // ✅ Save proposal/document file
        $path = $this->document_file->store('documents', 'public'); // ex: documents/xxxx.pdf

        // Calculate DateTime
        try {
            $startDateTime = \Carbon\Carbon::parse($this->loan_date_start . ' ' . $this->loan_time_start);
            $endDateTime = $startDateTime->copy()->addHours((int) $this->loan_duration);
        } catch (\Exception $e) {
            $startDateTime = now();
            $endDateTime = now()->addHour();
        }

        // Create Loan Request
        $fullReason = $this->borrower_reason;
        if ($this->borrower_description) {
            $fullReason .= " (" . $this->borrower_description . ")";
        }

        // Using DB transaction
        \Illuminate\Support\Facades\DB::transaction(function () use ($cart, $path, $startDateTime, $endDateTime, $fullReason) {

            $loan = \App\Models\LoanRequest::create([
                'borrower_name' => $this->borrower_name,
                'borrower_email' => $this->borrower_email,
                'borrower_phone' => $this->borrower_phone,
                'borrower_reason' => $fullReason,
                'proposal_path' => $path,          // ✅ INI KUNCI UTAMANYA
                'loan_date_start' => $startDateTime,
                'loan_date_end' => $endDateTime,
                'status' => 'pending',
            ]);

            foreach ($cart as $cartItem) {
                \App\Models\LoanItem::create([
                    'loan_request_id' => $loan->id,
                    'inventory_id' => $cartItem['id'],
                    'quantity' => (int) $cartItem['quantity']
                ]);
            }
        });

        $this->clearCart();

        return redirect()->route('guest.success')
            ->with('success', 'Peminjaman berhasil diajukan! Silahkan tunggu persetujuan pengelola.');
    }

    public function render()
    {
        $cart = $this->getCart();

        // Auto-select tab logic
        if ($this->activeItemId === null || !isset($cart[$this->activeItemId])) {
            $this->activeItemId = !empty($cart) ? array_key_first($cart) : null;
        }

        return view('livewire.guest.cart', ['cart' => $cart]);
    }
}
