<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sport Nutrition - Головна</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-100">
    @include('layouts.topbar')
    @include('layouts.header')
    @include('layouts.categories-menu')

    <!-- Далі йде основний контент сторінки -->

    <div class="flex flex-col min-h-screen">
        <!-- Hero Section -->
        <div class="relative bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center justify-between">
                <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-1/2 lg:pb-28 xl:pb-32">
                    <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                        <div class="sm:text-center lg:text-left">
                            <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                                <span class="block xl:inline">Досягніть своїх</span>
                                <span class="block text-indigo-600 xl:inline">спортивних цілей</span>
                            </h1>
                            <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                                Якісні спортивні добавки для максимальної продуктивності та швидкого відновлення. Наші продукти допоможуть вам досягти нових вершин у спорті.
                            </p>
                            <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                                <div class="rounded-md shadow">
                                    <a href="{{ route('products.index') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10">
                                        Переглянути продукти
                                    </a>
                                </div>
                                <div class="mt-3 sm:mt-0 sm:ml-3">
                                    <a href="{{ route('categories.blog') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 md:py-4 md:text-lg md:px-10">
                                        Дізнатися більше
                                    </a>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
                <!-- Слайдер картинок -->
                <div class="relative w-full lg:w-1/2 flex justify-center items-center mt-8 lg:mt-0 group">
                    <div id="hero-slider" class="relative w-[40rem] h-[40rem] bg-transparent rounded-2xl flex items-center justify-center overflow-hidden">
                        <img src="/images/Frame 44 ua.png" alt="Frame 44" class="absolute inset-0 w-full h-full object-contain transition-opacity duration-500 opacity-100 slider-img">
                        <img src="/images/sale25_ua.jpg" alt="Sale 25" class="absolute inset-0 w-full h-full object-contain transition-opacity duration-500 opacity-0 slider-img">
                        <img src="/images/shaker_sporter.jpg" alt="Shaker Sporter" class="absolute inset-0 w-full h-full object-contain transition-opacity duration-500 opacity-0 slider-img">
                        <img src="/images/ulta shot.png" alt="Ulta Shot" class="absolute inset-0 w-full h-full object-contain transition-opacity duration-500 opacity-0 slider-img">
                        <img src="/images/everbuildru.png" alt="Everbuild RU" class="absolute inset-0 w-full h-full object-contain transition-opacity duration-500 opacity-0 slider-img">
                        <!-- Стрілки -->
                        <button class="absolute left-2 top-1/2 -translate-y-1/2 bg-white bg-opacity-80 rounded-full p-2 shadow-lg opacity-0 group-hover:opacity-100 transition" onclick="prevSlide()">
                            <svg class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        </button>
                        <button class="absolute right-2 top-1/2 -translate-y-1/2 bg-white bg-opacity-80 rounded-full p-2 shadow-lg opacity-0 group-hover:opacity-100 transition" onclick="nextSlide()">
                            <svg class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            let currentSlide = 0;
            const slides = document.querySelectorAll('.slider-img');
            function showSlide(idx) {
                slides.forEach((img, i) => {
                    img.style.opacity = (i === idx) ? '1' : '0';
                });
                currentSlide = idx;
            }
            function nextSlide() {
                showSlide((currentSlide + 1) % slides.length);
            }
            function prevSlide() {
                showSlide((currentSlide - 1 + slides.length) % slides.length);
            }
            let autoSlide = setInterval(nextSlide, 4000);
            document.getElementById('hero-slider').addEventListener('mouseenter', () => clearInterval(autoSlide));
            document.getElementById('hero-slider').addEventListener('mouseleave', () => autoSlide = setInterval(nextSlide, 4000));
            // Ініціалізація
            showSlide(0);
        </script>

        <!-- Features Section -->
        <div class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:text-center">
                    <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Переваги</h2>
                    <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                        Чому обирають нас
                    </p>
                </div>

                <div class="mt-10">
                    <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                        <div class="relative">
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-16">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Якість продукції</h3>
                                <p class="mt-2 text-base text-gray-500">
                                    Всі наші продукти проходять строгий контроль якості та відповідають міжнародним стандартам.
                                </p>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-16">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Швидка доставка</h3>
                                <p class="mt-2 text-base text-gray-500">
                                    Доставляємо по всій Україні протягом 1-3 днів. Безкоштовна доставка від 2000 грн.
                                </p>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div class="ml-16">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Ефективність</h3>
                                <p class="mt-2 text-base text-gray-500">
                                    Наші добавки допоможуть вам досягти максимальних результатів у спорті.
                                </p>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="ml-16">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Професійна підтримка</h3>
                                <p class="mt-2 text-base text-gray-500">
                                    Наші експерти допоможуть вам обрати найкращі продукти для ваших цілей.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="bg-gray-100 flex-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                        Категорії продуктів
                    </h2>
                    <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
                        Оберіть категорію, яка відповідає вашим потребам
                    </p>
                </div>
                <div class="mt-12 grid gap-8 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Протеїни -->
                    <a href="{{ route('categories.dobavky') }}" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-yellow-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-yellow-400 transition group">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-yellow-500 group-hover:text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-lg font-bold text-gray-900">Протеїни</p>
                            <p class="text-sm text-gray-500">Для росту м'язів та відновлення</p>
                        </div>
                    </a>
                    <!-- Передтренувальні комплекси -->
                    <a href="{{ route('categories.sport') }}" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-blue-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-400 transition group">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-500 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-lg font-bold text-gray-900">Передтренувальні комплекси</p>
                            <p class="text-sm text-gray-500">Для максимальної енергії</p>
                        </div>
                    </a>
                    <!-- Вітаміни та мінерали -->
                    <a href="{{ route('categories.vitaminy') }}" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-green-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-400 transition group">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-500 group-hover:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-lg font-bold text-gray-900">Вітаміни та мінерали</p>
                            <p class="text-sm text-gray-500">Для здоров'я та імунітету</p>
                        </div>
                    </a>
                    <!-- Здоров'я -->
                    <a href="{{ route('categories.zdorovya') }}" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-pink-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-pink-400 transition group">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-pink-500 group-hover:text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-lg font-bold text-gray-900">Здоров'я</p>
                            <p class="text-sm text-gray-500">Вітаміни для імунітету, мозку, волосся, нігтів</p>
                        </div>
                    </a>
                    <!-- Бренди -->
                    <a href="{{ route('categories.brendy') }}" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-purple-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-400 transition group">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-purple-500 group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-lg font-bold text-gray-900">Бренди</p>
                            <p class="text-sm text-gray-500">Топ-виробники спортивного харчування</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-800 w-full">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <p class="text-base text-gray-400">
                        &copy; {{ date('Y') }} Belok.ua. Всі права захищені.
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <x-chat-assistant />

    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slider-img');
        function showSlide(idx) {
            slides.forEach((img, i) => {
                img.style.opacity = (i === idx) ? '1' : '0';
            });
            currentSlide = idx;
        }
        function nextSlide() {
            showSlide((currentSlide + 1) % slides.length);
        }
        function prevSlide() {
            showSlide((currentSlide - 1 + slides.length) % slides.length);
        }
        let autoSlide = setInterval(nextSlide, 4000);
        document.getElementById('hero-slider').addEventListener('mouseenter', () => clearInterval(autoSlide));
        document.getElementById('hero-slider').addEventListener('mouseleave', () => autoSlide = setInterval(nextSlide, 4000));
        // Ініціалізація
        showSlide(0);
    </script>
</body>
</html> 