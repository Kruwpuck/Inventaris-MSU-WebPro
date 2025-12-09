<?php

namespace App\Livewire\Borrower;

use Illuminate\Support\Facades\Session;

trait HandlesLoanTime
{
    public $startDate;
    public $endDate;
    public $startTime;
    public $duration = 1; // Default 1 hour

    public function mountHandlesLoanTime()
    {
        // Load from session if exists
        if (Session::has('loan_session')) {
            $session = Session::get('loan_session');
            $this->startDate = $session['start_date'];
            $this->endDate   = $session['end_date'];
            $this->startTime = $session['start_time'];
            $this->duration  = $session['duration'];
        }
    }

    public function checkAvailability()
    {
        $this->validate([
            'startDate' => 'required|date|after_or_equal:today',
            'endDate'   => 'required|date|after_or_equal:startDate',
            'startTime' => 'required',
            'duration'  => 'required|integer|min:1',
        ], [
            'startDate.required' => 'Tanggal pakai harus diisi',
            'endDate.required'   => 'Tanggal kembali harus diisi',
            'startTime.required' => 'Jam mulai harus diisi',
            'duration.required'  => 'Durasi harus diisi',
        ]);

        // Logic "Cek Ketersediaan" could go here (query DB for overlaps)
        // For now, we assume it's available and just save the preference.

        $loanData = [
            'start_date' => $this->startDate,
            'end_date'   => $this->endDate,
            'start_time' => $this->startTime,
            'duration'   => $this->duration,
            'timestamp'  => now(),
        ];

        Session::put('loan_session', $loanData);
        
        $this->dispatch('loan-time-updated'); // Event for UI updates
        session()->flash('success', 'Waktu peminjaman berhasil diset.');
    }
}
