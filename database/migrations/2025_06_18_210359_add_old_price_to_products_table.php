<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Міграція для додавання поля old_price до таблиці продуктів
 * 
 * Ця міграція додає поле old_price до таблиці products для підтримки
 * акційних товарів зі старою ціною.
 * 
 * Поле old_price:
 * - Тип: float (число з плаваючою комою)
 * - Може бути null (для товарів без акції)
 * - Розташоване після поля price
 * - Призначення: відображення старої ціни для акційних товарів
 */
return new class extends Migration
{
    /**
     * Виконує міграцію - додає поле old_price до таблиці products
     * 
     * @return void
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Додаємо поле для старої ціни (розташовуємо після поточної ціни)
            $table->float('old_price')->nullable()->after('price');
        });
    }

    /**
     * Відкатує міграцію - видаляє поле old_price з таблиці products
     * 
     * @return void
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Видаляємо поле old_price
            $table->dropColumn('old_price');
        });
    }
};
