<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'expiry_date',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_voucher');
    }
}
