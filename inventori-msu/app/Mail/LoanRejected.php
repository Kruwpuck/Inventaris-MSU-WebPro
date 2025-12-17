<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\LoanRequest;

class LoanRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $loan;

    public function __construct(LoanRequest $loan)
    {
        $this->loan = $loan;
    }

    public function build()
    {
        return $this->subject('Pemberitahuan Penolakan Peminjaman - MSU')
                    ->view('emails.loan_rejected'); 
    }
}
