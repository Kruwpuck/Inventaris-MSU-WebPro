<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LoanRequest extends Model
{
    protected $fillable = [
        'borrower_name',
        'borrower_email',
        'borrower_phone',
        'department',             // ✅ Baru
        'nim_nip',                // ✅ Baru
        'borrower_reason',        // Keperluan
        'proposal_path',          // ✅ tambah ini
        'loan_date_start',
        'loan_date_end',
        'start_time',
        'end_time',
        'ktp_path',             // ✅ Baru
        'activity_location',    // ✅ Baru
        'activity_description', // ✅ Baru
        'status',
        'rejection_reason',
        'donation_amount', // ✅ Baru
    ];

    protected $casts = [
        'loan_date_start' => 'datetime',
        'loan_date_end' => 'datetime',
    ];

    // daftar inventory yang dipinjam + qty di pivot
    public function items()
    {
        return $this->belongsToMany(Inventory::class, 'loan_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    // akses pivot loan_items langsung (lebih fleksibel)
    public function loanItems()
    {
        return $this->hasMany(LoanItem::class);
    }

    public function loanRecord(): HasOne
    {
        return $this->hasOne(LoanRecord::class);
    }
}
