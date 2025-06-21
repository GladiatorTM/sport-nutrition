<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для перевірки прав адміністратора
 * 
 * Цей middleware перевіряє, чи має користувач права адміністратора
 * для доступу до захищених маршрутів адміністративної панелі.
 * 
 * Логіка роботи:
 * 1. Перевіряє, чи авторизований користувач
 * 2. Якщо не авторизований - перенаправляє на сторінку входу
 * 3. Якщо авторизований, але не адміністратор - повертає помилку 403
 * 4. Якщо адміністратор - дозволяє доступ до захищеного маршруту
 */
class CheckAdmin
{
    /**
     * Обробляє вхідний запит
     * 
     * Виконує перевірку прав доступу користувача до адміністративних
     * функцій системи.
     * 
     * @param Request $request Вхідний HTTP запит
     * @param Closure $next Наступний middleware або контролер
     * @return Response HTTP відповідь
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Перевіряємо, чи авторизований користувач
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Перевіряємо, чи має користувач права адміністратора
        if (!auth()->user()->is_admin) {
            abort(403, 'Доступ заборонено. Необхідні права адміністратора.');
        }

        // Якщо всі перевірки пройдені - передаємо управління далі
        return $next($request);
    }
} 