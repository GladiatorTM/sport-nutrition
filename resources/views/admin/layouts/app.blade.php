<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF токен для захисту від міжсайтових запитів -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Заголовок адмін панелі -->
    <title>{{ config('app.name', 'Laravel') }} - Адмін панель</title>

    <!-- Підключення шрифтів Figtree -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Підключення стилів та скриптів через Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Chart.js для графіків в аналітиці -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="font-sans antialiased">
    <!-- Основний контейнер адмін панелі -->
    <div class="min-h-screen bg-gray-100">
        <!-- Бокове меню навігації (фіксоване зліва) -->
        <div class="fixed inset-y-0 left-0 w-64 bg-gray-800">
            <!-- Заголовок бокового меню -->
            <div class="flex items-center justify-center h-16 bg-gray-900">
                <span class="text-white text-lg font-semibold">Адмін панель</span>
            </div>
            
            <!-- Навігаційне меню -->
            <nav class="mt-5 px-2">
                <!-- Пункт меню: Дашборд -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="mr-4 h-6 w-6 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Дашборд
                </a>

                <!-- Пункт меню: Управління товарами -->
                <a href="{{ route('admin.products') }}" 
                   class="mt-1 group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md {{ request()->routeIs('admin.products*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="mr-4 h-6 w-6 {{ request()->routeIs('admin.products*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                        </path>
                    </svg>
                    Товари
                </a>

                <!-- Пункт меню: Управління користувачами -->
                <a href="{{ route('admin.users') }}" 
                   class="mt-1 group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md {{ request()->routeIs('admin.users*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="mr-4 h-6 w-6 {{ request()->routeIs('admin.users*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    Користувачі
                </a>

                <!-- Пункт меню: Аналітика та звіти -->
                <a href="{{ route('admin.analytics') }}" 
                   class="mt-1 group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md {{ request()->routeIs('admin.analytics') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="mr-4 h-6 w-6 {{ request()->routeIs('admin.analytics') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Аналітика
                </a>

                <!-- Кнопка повернення на головну сторінку (внизу меню) -->
                <div class="mt-auto">
                    <a href="{{ route('home') }}" 
                       class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md">
                        <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        На головну
                    </a>
                </div>
            </nav>
        </div>

        <!-- Основний контент (з відступом зліва для бокового меню) -->
        <div class="pl-64">
            <!-- Верхня панель з заголовком та інформацією про користувача -->
            <div class="bg-white shadow">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <!-- Заголовок сторінки -->
                        <div class="flex">
                            <div class="flex-shrink-0 flex items-center">
                                <h1 class="text-2xl font-semibold text-gray-900">
                                    @yield('header', 'Адмін панель')
                                </h1>
                            </div>
                        </div>
                        <!-- Інформація про поточного користувача -->
                        <div class="flex items-center">
                            <span class="text-gray-700">{{ Auth::user()->name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Основний контент сторінки -->
            <main class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html> 