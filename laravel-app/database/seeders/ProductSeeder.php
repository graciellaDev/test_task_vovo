<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::query()->pluck('id');

        if ($categories->isEmpty()) {
            return;
        }

        $adjectives = ['Smart', 'Classic', 'Portable', 'Premium', 'Eco', 'Compact', 'Pro', 'Ultra'];
        $nouns = ['Headphones', 'Lamp', 'Bottle', 'Backpack', 'Keyboard', 'Chair', 'Watch', 'Camera', 'Mixer', 'Book'];

        for ($i = 0; $i < 50; $i++) {
            $name = $adjectives[array_rand($adjectives)].' '.$nouns[array_rand($nouns)].' '.random_int(100, 999);

            Product::create([
                'name' => $name,
                'price' => number_format(random_int(199, 19999) / 100, 2, '.', ''),
                'category_id' => $categories->random(),
                'in_stock' => (bool) random_int(0, 1),
                'rating' => round(random_int(10, 50) / 10, 1), // 1.0 - 5.0
            ]);
        }
    }
}

