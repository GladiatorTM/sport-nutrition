<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Модель User - представляє користувача в системі
 * 
 * Ця модель відповідає за:
 * - Аутентифікацію користувачів
 * - Зберігання профілю користувача (ім'я, email, аватар)
 * - Управління правами доступу (звичайний користувач / адміністратор)
 * - Інтеграцію з Google OAuth (google_id)
 * - Відправку сповіщень користувачам
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Масив полів, які можна масово заповнювати
     * 
     * @var list<string>
     */
    protected $fillable = [
        'name',      // Ім'я користувача
        'email',     // Email адреса (унікальна)
        'password',  // Хешований пароль
        'avatar',    // Шлях до аватара користувача
        'is_admin',  // Права адміністратора (true/false)
    ];

    /**
     * Масив полів, які приховуються при серіалізації
     * 
     * Ці поля не будуть включені в JSON/масиви при серіалізації
     * об'єкта користувача для безпеки.
     * 
     * @var list<string>
     */
    protected $hidden = [
        'password',        // Пароль завжди приховується
        'remember_token',  // Токен "запам'ятати мене"
    ];

    /**
     * Масив приведень типів для атрибутів
     * 
     * Визначає, як Laravel повинен обробляти різні типи даних
     * при збереженні та отриманні з бази даних.
     * 
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',  // Дата підтвердження email як DateTime
        'password' => 'hashed',             // Пароль автоматично хешується
        'is_admin' => 'boolean',            // Права адміністратора як boolean
    ];

    /**
     * Перевіряє, чи є користувач адміністратором
     * 
     * Зручний метод для перевірки прав доступу користувача.
     * 
     * @return bool true якщо користувач є адміністратором, false інакше
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
}
