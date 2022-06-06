<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name', 'address', 'city', 'province', 'store_image', 'user_id'
    ];

}
