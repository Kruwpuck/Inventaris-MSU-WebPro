<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'stock',
        'capacity',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'stock'     => 'integer',
        'capacity'  => 'integer',
        'is_active' => 'boolean',
    ];

    public function loanRequests()
    {
        return $this->belongsToMany(LoanRequest::class, 'loan_items')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function loanItems()
    {
        return $this->hasMany(LoanItem::class);
    }
}
