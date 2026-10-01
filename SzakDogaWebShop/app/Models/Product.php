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
        'discount',
        'description',
        'image',
        'category_id',
    ];

public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function salePrice(): float
    {
        return round($this->price * (1 - ($this->discount ?? 0) / 100), 2);
    }
}
