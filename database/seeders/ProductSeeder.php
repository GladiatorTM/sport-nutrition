<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Категорії
        $categories = [
            'Акції' => ['desc' => 'Товари зі знижками та акціями', 'slug' => 'aktsii'],
            'Спорт' => ['desc' => 'Спортивні добавки для тренувань', 'slug' => 'sport'],
            'Добавки' => ['desc' => 'Омега-3, креатин, амінокислоти та інше', 'slug' => 'dobavky'],
            'Вітаміни' => ['desc' => 'Вітаміни для чоловіків і жінок', 'slug' => 'vitaminy'],
        ];
        $categoryIds = [];
        foreach ($categories as $name => $data) {
            $categoryIds[$name] = Category::firstOrCreate([
                'name' => $name
            ], [
                'description' => $data['desc'],
                'slug' => $data['slug']
            ])->id;
        }

        // Бренди
        $brands = ['Optimum Nutrition', 'MyProtein', 'Scitec', 'OstroVit', 'BioTech', 'NOW Foods', 'Universal', 'Dymatize'];

        // Картинки для товарів (імітація, можна додати свої jpg/png у public/images/products)
        $images = glob(public_path('images/products/*.{jpg,png,jpeg}', GLOB_BRACE));

        // 20 акційних товарів
        for ($i = 1; $i <= 20; $i++) {
            Product::create([
                'name' => 'Акційний товар #' . $i,
                'description' => 'Товар зі знижкою. Ідеально для спортсменів. Бренд: ' . $brands[$i % count($brands)],
                'price' => rand(200, 800),
                'category_id' => $categoryIds['Акції'],
                'image' => null,
                'image_path' => $images ? str_replace(public_path(), '', $images[array_rand($images)]) : null,
            ]);
        }
        // 40 спорт (добавки)
        for ($i = 1; $i <= 40; $i++) {
            Product::create([
                'name' => 'Спортивна добавка #' . $i,
                'description' => 'Ефективна спортивна добавка для тренувань. Бренд: ' . $brands[$i % count($brands)],
                'price' => rand(300, 1200),
                'category_id' => $categoryIds['Спорт'],
                'image' => null,
                'image_path' => $images ? str_replace(public_path(), '', $images[array_rand($images)]) : null,
            ]);
        }
        // 20 добавки (омега 3 та інші)
        $supps = ['Омега-3', 'Креатин', 'BCAA', 'Глютамін', 'L-карнітин', 'Магній', 'Цинк', 'Мелатонін', 'Колаген', 'CLA'];
        for ($i = 1; $i <= 20; $i++) {
            $supp = $supps[$i % count($supps)];
            Product::create([
                'name' => $supp . ' #' . $i,
                'description' => $supp . ' для здоровʼя та відновлення. Бренд: ' . $brands[$i % count($brands)],
                'price' => rand(150, 700),
                'category_id' => $categoryIds['Добавки'],
                'image' => null,
                'image_path' => $images ? str_replace(public_path(), '', $images[array_rand($images)]) : null,
            ]);
        }
        // 20 вітамінів (для чоловіків і жінок)
        $vitamins = ['Вітамін C', 'Вітамін D3', 'Мультивітаміни', 'Вітамін B12', 'Вітамін E', 'Вітамін A', 'Вітамін K2', 'Вітамін B6', 'Фолієва кислота', 'Комплекс для жінок', 'Комплекс для чоловіків'];
        for ($i = 1; $i <= 20; $i++) {
            $vit = $vitamins[$i % count($vitamins)];
            $for = $i % 2 === 0 ? 'для жінок' : 'для чоловіків';
            Product::create([
                'name' => $vit . ' ' . $for . ' #' . $i,
                'description' => $vit . ' ' . $for . '. Бренд: ' . $brands[$i % count($brands)],
                'price' => rand(100, 500),
                'category_id' => $categoryIds['Вітаміни'],
                'image' => null,
                'image_path' => $images ? str_replace(public_path(), '', $images[array_rand($images)]) : null,
            ]);
        }

        // Протеины
        for ($i = 1; $i <= 20; $i++) {
            DB::table('products')->insert([
                'name' => "Протеїн #{$i}",
                'description' => "Високоякісний протеїн для набору м'язової маси та відновлення після тренувань. Містить всі необхідні амінокислоти та легко засвоюється.",
                'price' => rand(800, 2000),
                'old_price' => rand(2001, 2500),
                'category_id' => $categoryIds['Добавки'],
                'created_at' => now(),
                'updated_at' => now(),
                'views' => rand(10, 1000),
                'image_path' => "images/products/protein/protein(" . rand(1, 7) . ").jfif"
            ]);
        }

        // Креатин
        for ($i = 1; $i <= 20; $i++) {
            DB::table('products')->insert([
                'name' => "Креатин #{$i}",
                'description' => "Креатин моногідрат найвищої якості. Сприяє збільшенню сили та витривалості під час тренувань, прискорює відновлення м'язів.",
                'price' => rand(500, 1500),
                'old_price' => rand(1501, 2000),
                'category_id' => $categoryIds['Добавки'],
                'created_at' => now(),
                'updated_at' => now(),
                'views' => rand(10, 1000),
                'image_path' => "images/products/creatine/creatine(" . rand(1, 8) . ").jfif"
            ]);
        }

        // BCAA
        for ($i = 1; $i <= 20; $i++) {
            DB::table('products')->insert([
                'name' => "BCAA #{$i}",
                'description' => "Комплекс амінокислот з розгалуженими ланцюгами у оптимальному співвідношенні 2:1:1. Запобігає катаболізму та прискорює відновлення.",
                'price' => rand(600, 1800),
                'old_price' => rand(1801, 2300),
                'category_id' => $categoryIds['Добавки'],
                'created_at' => now(),
                'updated_at' => now(),
                'views' => rand(10, 1000),
                'image_path' => "images/products/bcaa/bcaa(" . rand(1, 8) . ").jfif"
            ]);
        }
    }
} 