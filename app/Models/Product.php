<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Модель Product - представляє продукт в системі
 * 
 * Ця модель відповідає за:
 * - Зберігання інформації про продукти (назва, опис, ціна, категорія)
 * - Зв'язок з категорією продукту
 * - Автоматичне визначення зображення продукту на основі його типу
 * - Управління цінами (поточна та стара ціна для акцій)
 */
class Product extends Model
{
    use HasFactory;

    /**
     * Масив полів, які можна масово заповнювати
     * 
     * @var array
     */
    protected $fillable = [
        'name',        // Назва продукту
        'description', // Опис продукту
        'price',       // Поточна ціна
        'old_price',   // Стара ціна (для акційних товарів)
        'category_id', // ID категорії
        'image',       // Шлях до зображення в базі даних
        'image_path',  // Додатковий шлях до зображення
    ];

    /**
     * Зв'язок з категорією продукту
     * 
     * Повертає категорію, до якої належить цей продукт.
     * 
     * @return BelongsTo Зв'язок з моделлю Category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Аксесор для отримання шляху до зображення продукту
     * 
     * Цей метод автоматично визначає зображення для продукту:
     * 1. Якщо є збережене зображення - повертає його
     * 2. Якщо немає - аналізує назву продукту та вибирає відповідне зображення
     * 3. Якщо тип не визначено - повертає дефолтне зображення
     * 
     * Логіка визначення типу продукту:
     * - protein: протеїни
     * - creatine: креатин
     * - bcaa: амінокислоти
     * - vitamins: вітаміни
     * - preworkout: передтренувальні комплекси
     * - fatburner: жироспалювачі
     * 
     * @param string|null $value Збережений шлях до зображення
     * @return string Шлях до зображення продукту
     */
    public function getImagePathAttribute($value)
    {
        // Якщо є збережене зображення - повертаємо його
        if ($value) {
            return '/' . ltrim($value, '/');
        }

        // Визначаємо тип товару на основі назви (приводимо до нижнього регістру)
        $name = strtolower($this->name);
        
        // Масив ключових слів для різних типів товарів
        $productTypes = [
            'protein' => ['protein', 'протеїн', 'протеин'],
            'creatine' => ['creatine', 'креатин'],
            'bcaa' => ['bcaa', 'бцаа', 'бсаа'],
            'vitamins' => ['vitamin', 'вітамін', 'витамин'],
            'preworkout' => ['pre-workout', 'preworkout', 'предтрен'],
            'fatburner' => ['fat burner', 'fatburner', 'жиросжигатель', 'жироспалювач']
        ];

        // Перевіряємо до якого типу належить товар
        foreach ($productTypes as $type => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($name, $keyword)) {
                    // Шукаємо всі файли в папці для цього типу
                    $path = public_path("products/{$type}");
                    if (is_dir($path)) {
                        // Знаходимо всі зображення (jpg, jpeg, png, jfif)
                        $files = glob("{$path}/*.{jpg,jpeg,png,jfif}", GLOB_BRACE);
                        if (!empty($files)) {
                            // Вибираємо випадковий файл для різноманітності
                            $randomFile = $files[array_rand($files)];
                            return '/products/' . $type . '/' . basename($randomFile);
                        }
                    }
                }
            }
        }

        // Якщо тип не визначено або немає файлів - повертаємо дефолтне зображення
        return '/images/no-image.png';
    }
} 