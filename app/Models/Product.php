<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'unit',
        'image'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function images()
    {
        return $this->belongsToMany(Image::class, 'product_image');
    }
}
