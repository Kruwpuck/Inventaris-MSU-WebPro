<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanItem extends Model
{
    protected $table = 'loan_items';

    protected $fillable = [
        'loan_request_id',
        'inventory_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function loanRequest()
    {
        return $this->belongsTo(LoanRequest::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
