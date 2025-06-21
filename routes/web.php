<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\ProfileAvatarController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Middleware\CheckAdmin;

/**
 * Головна сторінка сайту
 * Відображає головну сторінку з категоріями продуктів та слайдером
 */
Route::get('/', function () {
    return view('home');
})->name('home');

/**
 * Особистий кабінет користувача
 * Доступний тільки авторизованим та верифікованим користувачам
 */
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/**
 * Група маршрутів для управління профілем користувача
 * Всі маршрути потребують авторизації
 */
Route::middleware('auth')->group(function () {
    // Редагування профілю
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Завантаження аватара
    Route::post('/profile/avatar', [ProfileAvatarController::class, 'update'])->name('profile.avatar');
});

/**
 * Маршрути для Google OAuth аутентифікації
 */
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

/**
 * Маршрути для категорій продуктів
 * Кожна категорія має свій slug та відповідний view
 */
Route::get('/aktsii', [CategoryController::class, 'showBySlug'])->defaults('slug', 'aktsii')->name('categories.aktsii');
Route::get('/sport', [CategoryController::class, 'showBySlug'])->defaults('slug', 'sport')->name('categories.sport');
Route::get('/dobavky', [CategoryController::class, 'showBySlug'])->defaults('slug', 'dobavky')->name('categories.dobavky');
Route::get('/vitaminy', [CategoryController::class, 'showBySlug'])->defaults('slug', 'vitaminy')->name('categories.vitaminy');

/**
 * Статичні сторінки категорій
 * Відображають інформаційні сторінки без динамічного контенту
 */
Route::view('/zdorovya', 'categories.zdorovya')->name('categories.zdorovya');
Route::view('/brendy', 'categories.brendy')->name('categories.brendy');
Route::view('/blog', 'categories.blog')->name('categories.blog');
Route::view('/kontakty', 'categories.kontakty')->name('categories.kontakty');
Route::view('/delivery', 'categories.delivery')->name('categories.delivery');
Route::view('/jobs', 'categories.jobs')->name('categories.jobs');

/**
 * Ресурсні маршрути для продуктів
 * Автоматично створює маршрути для CRUD операцій з продуктами
 */
Route::resource('products', ProductController::class);

/**
 * Адміністративні маршрути
 * Всі маршрути потребують авторизації та прав адміністратора
 * Префікс 'admin' та префікс імен 'admin.'
 */
Route::middleware(['auth', CheckAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    // Головна сторінка адмін-панелі
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Сторінка аналітики
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    
    /**
     * Маршрути для управління продуктами
     * Повний CRUD функціонал для адміністраторів
     */
    Route::get('/products', [DashboardController::class, 'products'])->name('products');
    Route::get('/products/create', [DashboardController::class, 'create'])->name('products.create');
    Route::post('/products', [DashboardController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [DashboardController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [DashboardController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [DashboardController::class, 'destroy'])->name('products.destroy');

    /**
     * Маршрути для управління користувачами
     * Дозволяють адміністраторам керувати користувачами системи
     */
    Route::get('/users', [DashboardController::class, 'users'])->name('users');
    Route::put('/users/{user}/toggle-admin', [DashboardController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::delete('/users/{user}', [DashboardController::class, 'destroyUser'])->name('users.destroy');
});

/**
 * Підключення маршрутів аутентифікації
 * Включає маршрути для входу, реєстрації, скидання пароля тощо
 */
require __DIR__.'/auth.php';
