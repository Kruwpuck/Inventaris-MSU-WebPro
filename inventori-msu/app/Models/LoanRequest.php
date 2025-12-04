<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanRequest extends Model
{
    protected $fillable = [
        'borrower_name',
        'borrower_email',
        'borrower_phone',
        'borrower_reason',
        'loan_date_start',
        'loan_date_end',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'loan_date_start' => 'date',
        'loan_date_end' => 'date',
    ];

    public function items()
    {
        return $this->belongsToMany(Inventory::class, 'loan_items')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}
