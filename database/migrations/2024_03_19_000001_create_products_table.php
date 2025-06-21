<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Міграція для створення таблиці продуктів
 * 
 * Ця міграція створює таблицю 'products' для зберігання інформації
 * про всі продукти в системі Sport Nutrition.
 * 
 * Структура таблиці:
 * - id: унікальний ідентифікатор продукту
 * - name: назва продукту
 * - description: детальний опис продукту
 * - price: ціна продукту (десятичне число з 2 знаками після коми)
 * - category_id: зовнішній ключ до таблиці категорій
 * - image: шлях до зображення продукту (опціонально)
 * - image_path: додатковий шлях до зображення (опціонально)
 * - timestamps: автоматичні поля created_at та updated_at
 */
return new class extends Migration
{
    /**
     * Виконує міграцію - створює таблицю продуктів
     * 
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            // Первинний ключ
            $table->id();
            
            // Назва продукту (обов'язкове поле)
            $table->string('name');
            
            // Детальний опис продукту
            $table->text('description');
            
            // Ціна продукту (максимум 10 цифр, 2 після коми)
            $table->decimal('price', 10, 2);
            
            // Зовнішній ключ до таблиці категорій з каскадним видаленням
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            
            // Шлях до зображення продукту (може бути null)
            $table->string('image')->nullable();
            
            // Додатковий шлях до зображення (може бути null)
            $table->string('image_path')->nullable();
            
            // Автоматичні поля часу створення та оновлення
            $table->timestamps();
        });
    }

    /**
     * Відкатує міграцію - видаляє таблицю продуктів
     * 
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}; 