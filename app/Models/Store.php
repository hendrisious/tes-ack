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

    public function toArray()
    {
        $toArray = parent::toArray();
        $toArray['store_image'] = $this->store_image;
        return $toArray;
    }

    public function getStoreImageAttribute()
    {
        return $this->attributes['store_image'];
    }
}
