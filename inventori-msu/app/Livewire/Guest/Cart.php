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
    public $location; // New

    // Loan Details
    public $loan_date_start;
    public $loan_time_start;
    public $loan_date_end; // New
    public $loan_time_end; // New
    // duration is calculated

    public $donation_amount = 0;

    // file proposal/dokumen dari guest
    public $document_file;
    public $ktp_file; // New

    protected $listeners = ['cart-updated' => '$refresh'];

    public function mount()
    {
         // Optional: Load donation presets or defaults
    }

    public function setActiveItem($id)
    {
        $this->activeItemId = $id;
    }

    protected $rules = [
        'borrower_name' => 'required',
        'borrower_email' => 'required|email',
        'borrower_phone' => 'required',
        'borrower_reason' => 'required',
        'borrower_nim' => 'required',
        'borrower_prodi' => 'required',
        'location' => 'required',
        'loan_date_start' => 'required|date',
        'loan_time_start' => 'required',
        'loan_date_end' => 'required|date|after_or_equal:loan_date_start',
        'loan_time_end' => 'required',
        'document_file' => 'required|file|mimes:pdf|max:10240', // 10MB, PDF only
        'ktp_file' => 'required|file|max:10240',
        'borrower_description' => 'required',
    ];

    public function messages()
    {
        return [
            'borrower_name.required' => 'Nama penanggung jawab wajib diisi.',
            'borrower_email.required' => 'Email wajib diisi.',
            'borrower_email.email' => 'Format email tidak valid.',
            'borrower_phone.required' => 'Nomor telepon wajib diisi.',
            'borrower_reason.required' => 'Keperluan wajib diisi.',
            'borrower_nim.required' => 'NIM/NIP wajib diisi.',
            'borrower_prodi.required' => 'Program studi / Unit wajib diisi.',
            'location.required' => 'Lokasi kegiatan wajib diisi.',
            'loan_date_start.required' => 'Tanggal mulai wajib diisi.',
            'loan_time_start.required' => 'Jam mulai wajib diisi.',
            'loan_date_end.required' => 'Tanggal selesai wajib diisi.',
            'loan_time_end.required' => 'Jam selesai wajib diisi.',
            'document_file.required' => 'Dokumen proposal wajib diunggah.',
            'document_file.mimes' => 'Proposal harus berformat PDF.',
            'document_file.max' => 'Ukuran file maksimal 10MB.',
            'ktp_file.required' => 'Dokumen identitas (KTM/KTP) wajib diunggah.',
            'ktp_file.max' => 'Ukuran file maksimal 10MB.',
            'borrower_description.required' => 'Deskripsi kegiatan wajib diisi.',
        ];
    }

    public function submit()
    {
        $this->validate();

        $cart = $this->getCart();
        if (!$cart || count($cart) === 0) {
            $this->addError('cart', 'Keranjang kosong.');
            return;
        }

        // Parse Dates
        try {
            $startDateTime = \Carbon\Carbon::parse($this->loan_date_start . ' ' . $this->loan_time_start);
            $endDateTime = \Carbon\Carbon::parse($this->loan_date_end . ' ' . $this->loan_time_end);

            if ($startDateTime->isPast()) {
                 $this->addError('loan_date_start', 'Waktu peminjaman sudah terlewat.');
                 return;
            }
            if ($startDateTime->greaterThanOrEqualTo($endDateTime)) {
                 $this->addError('loan_time_end', 'Waktu selesai harus lebih lambat dari waktu mulai.');
                 return;
            }
        } catch (\Exception $e) {
            $this->addError('loan_date_start', 'Format tanggal/waktu tidak valid.');
            return;
        }

        // Validate Stock Availability
        $hasErrors = false;
        foreach ($cart as $cartItem) {
            // Find inventory by ID if present, otherwise by Name
            $inv = null;
            if (isset($cartItem['id'])) {
                $inv = \App\Models\Inventory::find($cartItem['id']);
            }
            if (!$inv && isset($cartItem['name'])) {
                $inv = \App\Models\Inventory::where('name', $cartItem['name'])->first();
            }

            if (!$inv) {
                // If item not found, maybe ignore or error?
                $this->addError('cart', "Item '{$cartItem['name']}' tidak ditemukan dalam database.");
                $hasErrors = true;
                continue;
            }

            // Check stock limit
            if ($inv->category == 'barang' && $cartItem['quantity'] > $inv->stock) {
                $this->addError('cart', "Stok untuk '{$inv->name}' tidak mencukupi (Tersedia: {$inv->stock}, Diminta: {$cartItem['quantity']}).");
                $hasErrors = true;
            }
        }

        if ($hasErrors) return;

        // Save files
        $proposalPath = $this->document_file->store('proposals', 'public');
        $ktpPath = $this->ktp_file->store('ktp', 'public');

        // Create Loan Request
        // Using DB transaction
        $loan = \Illuminate\Support\Facades\DB::transaction(function () use ($cart, $proposalPath, $ktpPath, $startDateTime, $endDateTime) {

            // Calculate duration in hours (approx) for record keeping, though start/end is more precise
            $duration = $startDateTime->diffInHours($endDateTime);

            $loan = \App\Models\LoanRequest::create([
                'borrower_name' => $this->borrower_name,
                'borrower_email' => $this->borrower_email,
                'borrower_phone' => $this->borrower_phone,
                'borrower_reason' => $this->borrower_reason,
                
                'nim_nip' => $this->borrower_nim,
                'department' => $this->borrower_prodi,
                'activity_description' => $this->borrower_description,
                'activity_location' => $this->location,
                'donation_amount' => $this->donation_amount ?: 0,

                'proposal_path' => $proposalPath,
                'ktp_path' => $ktpPath,

                'loan_date_start' => $startDateTime->toDateString(),
                'loan_date_end' => $endDateTime->toDateString(),
                'start_time' => $startDateTime->toTimeString(),
                'end_time' => $endDateTime->toTimeString(),
                'duration' => $duration ?: 1, // Fallback
                'status' => 'PENDING',
            ]);

            foreach ($cart as $cartItem) {
                 // Resolve ID again
                $inv = null;
                if (isset($cartItem['id'])) $inv = \App\Models\Inventory::find($cartItem['id']);
                if (!$inv && isset($cartItem['name'])) $inv = \App\Models\Inventory::where('name', $cartItem['name'])->first();
                
                if ($inv) {
                    \App\Models\LoanItem::create([
                        'loan_request_id' => $loan->id,
                        'inventory_id' => $inv->id,
                        'quantity' => (int) $cartItem['quantity']
                    ]);
                }
            }

            return $loan;
        });

        // Send Email
        try {
            \Illuminate\Support\Facades\Log::info('Proses kirim email dimulai untuk: ' . $this->borrower_email);
            \Illuminate\Support\Facades\Mail::to($this->borrower_email)->send(new \App\Mail\LoanSubmitted($loan));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Email gagal dikirim: ' . $e->getMessage());
        }

        // Clear cart
        $this->clearCart(); // From HandlesCart trait

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
