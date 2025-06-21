<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Міграція для додавання поля views до таблиці продуктів
 * 
 * Ця міграція додає поле views до таблиці products для відстеження
 * кількості переглядів кожного продукту.
 * 
 * Поле views:
 * - Тип: unsignedBigInteger (велике ціле число без знаку)
 * - За замовчуванням: 0 (новий продукт не має переглядів)
 * - Розташоване після поля old_price
 * - Призначення: підрахунок популярності продуктів для сортування
 */
return new class extends Migration
{
    /**
     * Виконує міграцію - додає поле views до таблиці products
     * 
     * @return void
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Додаємо поле для підрахунку переглядів продукту
            $table->unsignedBigInteger('views')->default(0)->after('old_price');
        });
    }

    /**
     * Відкатує міграцію - видаляє поле views з таблиці products
     * 
     * @return void
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Видаляємо поле views
            $table->dropColumn('views');
        });
    }
};
