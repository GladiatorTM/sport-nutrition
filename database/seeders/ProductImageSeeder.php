<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\File;

class ProductImageSeeder extends Seeder
{
    public function run()
    {
        // Отримуємо всі продукти
        $products = Product::all();
        
        // Масив для зберігання доступних картинок для кожного типу
        $availableImages = [
            'protein' => $this->getAvailableImages('protein'),
            'creatine' => $this->getAvailableImages('creatine'),
            'bcaa' => $this->getAvailableImages('bcaa'),
            'vitamins' => $this->getAvailableImages('vitamins'),
            'preworkout' => $this->getAvailableImages('preworkout'),
            'fatburner' => $this->getAvailableImages('fatburner')
        ];

        // Для кожного продукту
        foreach ($products as $product) {
            $name = strtolower($product->name);
            $imagePath = null;

            // Перевіряємо тип продукту
            if (str_contains($name, 'protein') || str_contains($name, 'протеїн') || str_contains($name, 'протеин')) {
                $type = 'protein';
            } elseif (str_contains($name, 'creatine') || str_contains($name, 'креатин')) {
                $type = 'creatine';
            } elseif (str_contains($name, 'bcaa') || str_contains($name, 'бцаа') || str_contains($name, 'бсаа')) {
                $type = 'bcaa';
            } elseif (str_contains($name, 'vitamin') || str_contains($name, 'вітамін') || str_contains($name, 'витамин')) {
                $type = 'vitamins';
            } elseif (str_contains($name, 'pre-workout') || str_contains($name, 'preworkout') || str_contains($name, 'предтрен')) {
                $type = 'preworkout';
            } elseif (str_contains($name, 'fat burner') || str_contains($name, 'fatburner') || str_contains($name, 'жиросжигатель') || str_contains($name, 'жироспалювач')) {
                $type = 'fatburner';
            } else {
                continue; // Пропускаємо якщо тип не визначено
            }

            // Якщо є доступні картинки для цього типу
            if (!empty($availableImages[$type])) {
                // Вибираємо випадкову картинку
                $imagePath = 'images/products/' . $type . '/' . $availableImages[$type][array_rand($availableImages[$type])];
                
                // Оновлюємо продукт
                $product->image_path = $imagePath;
                $product->save();
            }
        }
    }

    private function getAvailableImages($type)
    {
        $path = public_path("images/products/{$type}");
        if (!is_dir($path)) {
            return [];
        }

        $files = File::glob($path . '/*.{jpg,jpeg,png,jfif}', GLOB_BRACE);
        return array_map(function($file) {
            return basename($file);
        }, $files);
    }
} 