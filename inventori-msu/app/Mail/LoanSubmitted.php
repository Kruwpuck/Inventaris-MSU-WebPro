<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\LoanRequest; 

class LoanSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $loan;

    // Kita inject data peminjaman ke sini
    public function __construct(LoanRequest $loan)
    {
        $this->loan = $loan;
    }

    public function build()
    {
        return $this->subject('Konfirmasi Peminjaman Barang - MSU')
                    ->view('emails.loan_submitted'); 
    }
}
