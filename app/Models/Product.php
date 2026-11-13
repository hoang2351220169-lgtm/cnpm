<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'quantity',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute(): string
    {
        $image = trim((string) $this->image);

        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        if (str_contains($image, '/')) {
            return asset(ltrim($image, '/'));
        }

        $name = mb_strtolower($this->name);
        $photo = match (true) {
            str_contains($name, 'váy'), str_contains($name, 'đầm') => 'photo-1595777457583-95e059d581b8',
            str_contains($name, 'quần'), str_contains($name, 'jean') => 'photo-1542272604-787c3835535d',
            str_contains($name, 'khoác') => 'photo-1551028719-00167b16eac5',
            default => 'photo-1521572163474-6864f9cf17ab',
        };

        return "https://images.unsplash.com/{$photo}?auto=format&fit=crop&w=1000&q=85";
    }
}