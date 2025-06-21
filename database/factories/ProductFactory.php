<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => 'Протеїн ' . $this->faker->unique()->word(),
            'description' => $this->faker->sentence(10),
            'price' => $this->faker->randomFloat(2, 500, 2500),
            'category_id' => Category::inRandomOrder()->first()?->id ?? 1,
            'image_path' => null,
        ];
    }
} 