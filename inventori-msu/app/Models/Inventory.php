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
    ];

    protected $casts = [
        'stock' => 'integer',
        'capacity' => 'integer',
    ];
}
