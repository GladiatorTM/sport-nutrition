<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UpdateProductImages extends Command
{
    protected $signature = 'products:update-images';
    protected $description = 'Update product image paths in database';

    private $typeKeywords = [
        'protein' => ['protein', 'протеїн', 'протеин', 'протеін'],
        'creatine' => ['creatine', 'креатин', 'креатін'],
        'bcaa' => ['bcaa', 'бцаа', 'бсаа', 'бца', 'бса'],
        'vitamins' => ['vitamin', 'вітамін', 'витамин', 'вітамін', 'витамин'],
        'preworkout' => ['pre-workout', 'preworkout', 'предтрен', 'предтреник'],
        'fatburner' => ['fat burner', 'fatburner', 'жиросжигатель', 'жироспалювач', 'жиросжигатель', 'жироспалювач']
    ];

    public function handle()
    {
        $this->info('Starting product images update...');

        // Отримуємо всі продукти
        $products = Product::all();
        $this->info("Found {$products->count()} products");

        // Масив для зберігання доступних картинок для кожного типу
        $availableImages = [];
        foreach (array_keys($this->typeKeywords) as $type) {
            $availableImages[$type] = $this->getAvailableImages($type);
            $this->info("Found " . count($availableImages[$type]) . " images for type {$type}");
        }

        $updated = 0;
        $skipped = 0;

        // Для кожного продукту
        foreach ($products as $product) {
            $name = trim(mb_strtolower($product->name));
            $oldPath = $product->image_path;
            $imagePath = null;
            $type = null;

            // Перевіряємо тип продукту (початок слова)
            foreach ($this->typeKeywords as $productType => $keywords) {
                foreach ($keywords as $keyword) {
                    if (Str::startsWith($name, $keyword)) {
                        $type = $productType;
                        break 2;
                    }
                }
            }

            if (!$type) {
                $this->warn("Skipping product '{$product->name}' (after trim: '{$name}') - type not determined");
                $skipped++;
                continue;
            }

            // Якщо є доступні картинки для цього типу
            if (!empty($availableImages[$type])) {
                // Вибираємо випадкову картинку
                $imagePath = 'images/products/' . $type . '/' . $availableImages[$type][array_rand($availableImages[$type])];
                
                // Оновлюємо продукт
                $product->image_path = $imagePath;
                $product->save();
                
                $this->info("Updated {$product->name}: {$oldPath} -> {$imagePath}");
                $updated++;
            } else {
                $this->warn("No images found for type {$type}");
                $skipped++;
            }
        }

        $this->info("Update completed. Updated: {$updated}, Skipped: {$skipped}");
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