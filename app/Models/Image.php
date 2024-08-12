<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['url'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_image');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_image');
    }
}
