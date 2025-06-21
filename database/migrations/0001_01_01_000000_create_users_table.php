<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Міграція для створення таблиць аутентифікації та користувачів
 * 
 * Ця міграція створює три основні таблиці для системи аутентифікації:
 * 1. users - основна таблиця користувачів
 * 2. password_reset_tokens - токени для скидання паролів
 * 3. sessions - сесії користувачів
 */
return new class extends Migration
{
    /**
     * Виконує міграцію - створює таблиці аутентифікації
     * 
     * @return void
     */
    public function up(): void
    {
        /**
         * Таблиця користувачів (users)
         * Зберігає основну інформацію про користувачів системи
         */
        Schema::create('users', function (Blueprint $table) {
            // Первинний ключ
            $table->id();
            
            // Ім'я користувача
            $table->string('name');
            
            // Email адреса (унікальна для кожного користувача)
            $table->string('email')->unique();
            
            // Дата підтвердження email (null якщо не підтверджено)
            $table->timestamp('email_verified_at')->nullable();
            
            // Хешований пароль користувача
            $table->string('password');
            
            // Токен "запам'ятати мене" для автоматичного входу
            $table->rememberToken();
            
            // Автоматичні поля часу створення та оновлення
            $table->timestamps();
        });

        /**
         * Таблиця токенів скидання паролів (password_reset_tokens)
         * Зберігає тимчасові токени для скидання забутих паролів
         */
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            // Email адреса як первинний ключ
            $table->string('email')->primary();
            
            // Унікальний токен для скидання пароля
            $table->string('token');
            
            // Час створення токена (для автоматичного видалення застарілих)
            $table->timestamp('created_at')->nullable();
        });

        /**
         * Таблиця сесій користувачів (sessions)
         * Зберігає активні сесії користувачів для підтримки входу
         */
        Schema::create('sessions', function (Blueprint $table) {
            // Унікальний ідентифікатор сесії
            $table->string('id')->primary();
            
            // Зовнішній ключ до користувача (може бути null для гостей)
            $table->foreignId('user_id')->nullable()->index();
            
            // IP адреса користувача (підтримує IPv6)
            $table->string('ip_address', 45)->nullable();
            
            // User-Agent браузера користувача
            $table->text('user_agent')->nullable();
            
            // Дані сесії (зашифровані)
            $table->longText('payload');
            
            // Час останньої активності (для очищення застарілих сесій)
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Відкатує міграцію - видаляє всі створені таблиці
     * 
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
