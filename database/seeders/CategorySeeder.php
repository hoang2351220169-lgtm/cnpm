<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create([
            'name' => 'Áo',
            'description' => 'Các loại áo thời trang nam nữ',
        ]);

        Category::create([
            'name' => 'Quần',
            'description' => 'Các loại quần thời trang',
        ]);

        Category::create([
            'name' => 'Váy',
            'description' => 'Các loại váy thời trang nữ',
        ]);

        Category::create([
            'name' => 'Áo khoác',
            'description' => 'Các loại áo khoác thời trang',
        ]);
    }
}