<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Міграція для створення таблиці категорій
 * 
 * Ця міграція створює таблицю 'categories' для зберігання інформації
 * про категорії продуктів в системі Sport Nutrition.
 * 
 * Структура таблиці:
 * - id: унікальний ідентифікатор категорії
 * - name: назва категорії (унікальна)
 * - slug: URL-дружній ідентифікатор категорії (унікальний)
 * - description: опис категорії (опціонально)
 * - timestamps: автоматичні поля created_at та updated_at
 */
return new class extends Migration
{
    /**
     * Виконує міграцію - створює таблицю категорій
     * 
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            // Первинний ключ
            $table->id();
            
            // Назва категорії (унікальна для уникнення дублікатів)
            $table->string('name')->unique();
            
            // Slug для URL (унікальний, використовується в маршрутах)
            $table->string('slug')->unique();
            
            // Опис категорії (може бути null)
            $table->text('description')->nullable();
            
            // Автоматичні поля часу створення та оновлення
            $table->timestamps();
        });
    }

    /**
     * Відкатує міграцію - видаляє таблицю категорій
     * 
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}; 