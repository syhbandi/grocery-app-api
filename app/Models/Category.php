<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category');
    }

    public function images()
    {
        return $this->belongsToMany(Image::class, 'category_image');
    }
}
