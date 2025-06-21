<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF токен для захисту від міжсайтових запитів -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Динамічний заголовок сторінки -->
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Підключення шрифтів Figtree -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Підключення стилів та скриптів через Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <!-- Основний контейнер сторінки -->
        <div class="min-h-screen bg-gray-100">
            <!-- Підключення компонентів навігації -->
            @include('layouts.header')          <!-- Головне меню навігації -->
            @include('layouts.categories-menu') <!-- Меню категорій продуктів -->

            <!-- Заголовок сторінки (опціональний) -->
            @hasSection('header')
                <!-- Заголовок з секції @section('header') -->
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        @yield('header')
                    </div>
                </header>
            @elseif(isset($header))
                <!-- Заголовок з змінної $header -->
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash повідомлення про успішні операції -->
            @if (session('success'))
                <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Flash повідомлення про помилки -->
            @if (session('error'))
                <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Основний контент сторінки -->
            <main>
                @yield('content')
            </main>

            <!-- Компонент чат-асистента -->
            <x-chat-assistant />

            <!-- Підвал сайту -->
            <footer class="bg-gray-800 text-white py-8">
                <!-- Тут можна додати контент підвалу -->
            </footer>
        </div>
    </body>
</html>
