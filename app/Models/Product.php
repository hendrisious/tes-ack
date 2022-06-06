<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name', 'price', 'user_id', 'product_image'
    ];

    public function getProductImageAttribute()
    {
        $images = $this->attributes['product_image'];
        return $images;
    }
}
