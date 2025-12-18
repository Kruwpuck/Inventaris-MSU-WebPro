<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanRecord extends Model
{
    protected $fillable = [
        'loan_request_id',
        'picked_up_at',
        'returned_at',
        'is_submitted',
        'notes',
    ];

    protected $casts = [
        'picked_up_at' => 'datetime',
        'returned_at' => 'datetime',
        'is_submitted' => 'boolean',
    ];

    /**
     * Relasi ke LoanRequest (Data Peminjam ada di sini)
     */
    public function loanRequest(): BelongsTo
    {
        return $this->belongsTo(LoanRequest::class);
    }

    /**
     * Accessor untuk Nama Peminjam (shortcut)
     */
    public function getBorrowerNameAttribute()
    {
        return $this->loanRequest ? $this->loanRequest->borrower_name : '-';
    }

    /**
     * Accessor untuk Detail Barang/Fasilitas (shortcut)
     * Menggabungkan nama barang dengan koma.
     */
    public function getItemDetailsAttribute()
    {
        if (!$this->loanRequest) {
            return '-';
        }

        // Ambil item dari relasi di LoanRequest
        $items = $this->loanRequest->items; 
        
        if ($items->isEmpty()) {
            return 'Tidak ada item';
        }

        return $items->pluck('name')->join(', ');
    }
}
