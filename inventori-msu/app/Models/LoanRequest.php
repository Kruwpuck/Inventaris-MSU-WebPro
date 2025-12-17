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
        'borrower_reason',
        'proposal_path',          // ✅ tambah ini
        'loan_date_start',
        'loan_date_end',
        'start_time', // Jam Mulai
        'duration',   // Durasi (integer)
        'status',
        'rejection_reason',
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
