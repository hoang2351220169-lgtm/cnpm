<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'category_id' => 1,
            'name' => 'Áo thun Basic trắng',
            'description' => 'Áo thun cotton mềm mại, thoáng mát.',
            'price' => 199000,
            'image' => 'ao-thun-trang.jpg',
            'quantity' => 50,
        ]);

        Product::create([
            'category_id' => 1,
            'name' => 'Áo Polo nam',
            'description' => 'Áo Polo nam phong cách lịch sự.',
            'price' => 299000,
            'image' => 'ao-polo.jpg',
            'quantity' => 40,
        ]);

        Product::create([
            'category_id' => 2,
            'name' => 'Quần Jean nam',
            'description' => 'Quần Jean thời trang, form đẹp.',
            'price' => 399000,
            'image' => 'quan-jean.jpg',
            'quantity' => 30,
        ]);

        Product::create([
            'category_id' => 2,
            'name' => 'Quần Short nam',
            'description' => 'Quần short thoải mái, phù hợp mùa hè.',
            'price' => 249000,
            'image' => 'quan-short.jpg',
            'quantity' => 35,
        ]);

        Product::create([
            'category_id' => 3,
            'name' => 'Váy nữ thời trang',
            'description' => 'Váy nữ phong cách trẻ trung.',
            'price' => 349000,
            'image' => 'vay-nu.jpg',
            'quantity' => 25,
        ]);

        Product::create([
            'category_id' => 4,
            'name' => 'Áo khoác thời trang',
            'description' => 'Áo khoác phong cách hiện đại.',
            'price' => 499000,
            'image' => 'ao-khoac.jpg',
            'quantity' => 20,
        ]);
    }
}