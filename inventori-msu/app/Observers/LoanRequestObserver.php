<?php

namespace App\Observers;

use App\Mail\NewLoanRequestAdmin;
use App\Models\LoanRequest;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * ShouldHandleEventsAfterCommit penting: Cart dan Api\LoanController menyimpan
 * LoanItem SETELAH LoanRequest di dalam transaksi yang sama. Tanpa ini, email
 * terkirim saat daftar barang belum tersimpan dan tabelnya kosong.
 */
class LoanRequestObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Pengajuan dibuat dari tiga tempat: Livewire Cart (barang), Livewire
     * Booking (ruangan), dan Api\LoanController. Observer dipakai supaya
     * notifikasi admin cukup ditulis sekali dan otomatis ikut jalur baru.
     */
    public function created(LoanRequest $loan): void
    {
        // Seeder dan perintah artisan tidak boleh mengirim email ke admin.
        if (app()->runningInConsole()) {
            return;
        }

        $adminAddress = config('mail.admin_address');

        if (empty($adminAddress)) {
            Log::warning('MAIL_ADMIN_ADDRESS belum diisi, notifikasi pengajuan tidak dikirim.');
            return;
        }

        // Email tidak boleh menjatuhkan pengajuan: kegagalan dicatat, bukan dilempar.
        try {
            $loan->load('items');
            Mail::to($adminAddress)->send(new NewLoanRequestAdmin($loan));
            Log::info('Notifikasi pengajuan #' . $loan->id . ' dikirim ke admin.');
        } catch (\Throwable $e) {
            Log::error('Notifikasi admin gagal dikirim untuk pengajuan #' . $loan->id . ': ' . $e->getMessage());
        }
    }
}
