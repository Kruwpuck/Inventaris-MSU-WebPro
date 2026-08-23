<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\LoanRequest;

class NewLoanRequestAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $loan;

    public function __construct(LoanRequest $loan)
    {
        $this->loan = $loan;
    }

    public function build()
    {
        // Nama peminjam masuk ke subjek supaya admin bisa mencarinya di inbox.
        return $this->subject('Pengajuan Peminjaman Baru - ' . $this->loan->borrower_name)
            ->replyTo($this->loan->borrower_email, $this->loan->borrower_name)
            ->view('emails.loan_admin_notice');
    }
}
