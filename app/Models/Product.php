<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'manufacturer_date',
        'expiry_date',
        'copies',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'manufacturer_date' => 'date',
        'expiry_date' => 'date',
        'copies' => 'integer',
    ];
}
