<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Міграція для додавання поля is_admin до таблиці користувачів
 * 
 * Ця міграція додає поле is_admin до таблиці users для розділення
 * користувачів на звичайних користувачів та адміністраторів.
 * 
 * Поле is_admin:
 * - Тип: boolean (true/false)
 * - За замовчуванням: false (звичайний користувач)
 * - Призначення: визначає права доступу до адміністративної панелі
 */
return new class extends Migration
{
    /**
     * Виконує міграцію - додає поле is_admin до таблиці users
     * 
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Додаємо поле для визначення прав адміністратора
            $table->boolean('is_admin')->default(false);
        });
    }

    /**
     * Відкатує міграцію - видаляє поле is_admin з таблиці users
     * 
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Видаляємо поле is_admin
            $table->dropColumn('is_admin');
        });
    }
}; 