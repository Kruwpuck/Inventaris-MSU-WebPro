<?php

namespace App\Livewire\Guest;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\LoanRequest;
use Illuminate\Support\Facades\Auth;

class Booking extends Component
{
    use WithFileUploads;

    public $purpose;
    public $loan_date;
    public $start_time; // <--- Pastikan properti ini ada
    public $duration;   // <--- Pastikan properti ini ada
    public $proposal;
    public $description;

    protected $rules = [
        'purpose' => 'required|string|max:255',
        'loan_date' => 'required|date|after_or_equal:today',
        'start_time' => 'required',           // <--- Validasi Jam
        'duration' => 'required|integer|min:1', // <--- Validasi Durasi
        'proposal' => 'required|file|mimes:pdf,jpg,png|max:10240',
        'description' => 'required|string',
    ];

    public function submitBooking()
    {
        // dd('CEK 1: Berhasil Masuk Fungsi Submit');
        $this->validate();

        // Upload Proposal
        $path = $this->proposal->store('proposals', 'public');

        // Hitung Tanggal Selesai (Opsional, jika 1 hari sama saja)
        // Jika durasi bisa lewat hari, perlu logika tambahan. 
        // Untuk simpelnya kita set start & end di hari yang sama dulu.
        $endDate = $this->loan_date;

        // SIMPAN KE DATABASE
        $loan = LoanRequest::create([
            'user_id' => Auth::id(),
            'borrower_name' => Auth::user()->name,
            'purpose' => $this->purpose,
            'loan_date_start' => $this->loan_date,
            'loan_date_end' => $endDate,

            // --- BAGIAN PENTING YANG KURANG ---
            'start_time' => $this->start_time, // Simpan Jam
            'duration' => $this->duration,   // Simpan Durasi
            'activity_description' => $this->description, // Simpan Deskripsi
            // ----------------------------------

            'proposal_path' => $path,
            'status' => 'pending',
        ]);

        // ... logic simpan items/barang (attach pivot) ...

        session()->flash('success', 'Pengajuan berhasil dikirim!');
        return redirect()->route('peminjaman.index'); // Sesuaikan redirect
    }

    public function render()
    {
        return view('livewire.peminjaman.booking');
    }
}