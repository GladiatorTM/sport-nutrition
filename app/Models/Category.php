<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Модель Category - представляє категорію продуктів в системі
 * 
 * Ця модель відповідає за:
 * - Зберігання інформації про категорії продуктів
 * - Зв'язок з продуктами (один до багатьох)
 * - Групування продуктів по категоріях
 * - Організацію структури каталогу товарів
 */
class Category extends Model
{
    use HasFactory;

    /**
     * Масив полів, які можна масово заповнювати
     * 
     * @var array
     */
    protected $fillable = [
        'name',        // Назва категорії
        'description'  // Опис категорії
    ];

    /**
     * Зв'язок з продуктами категорії
     * 
     * Повертає всі продукти, що належать до цієї категорії.
     * Один до багатьох: одна категорія може мати багато продуктів.
     * 
     * @return HasMany Зв'язок з моделлю Product
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
} 