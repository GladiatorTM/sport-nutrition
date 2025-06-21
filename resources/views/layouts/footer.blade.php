<!-- Підвал сайту з інформацією про компанію та корисними посиланнями -->
<footer class="bg-gray-800 text-white py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Сітка з трьома колонками інформації -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Колонка 1: Інформація про компанію -->
            <div>
                <h3 class="text-xl font-bold mb-4">Про компанію</h3>
                <p class="text-gray-300">Belok.ua - ваш надійний партнер у світі спортивного харчування та здорового способу життя.</p>
            </div>
            
            <!-- Колонка 2: Корисні посилання -->
            <div>
                <h3 class="text-xl font-bold mb-4">Корисні посилання</h3>
                <ul class="space-y-2">
                    <!-- Посилання на сторінку доставки -->
                    <li><a href="{{ route('categories.delivery') }}" class="text-gray-300 hover:text-yellow-400 transition">Доставка і оплата</a></li>
                    <!-- Посилання на сторінку вакансій -->
                    <li><a href="{{ route('categories.jobs') }}" class="text-gray-300 hover:text-yellow-400 transition">Робота у нас</a></li>
                </ul>
            </div>
            
            <!-- Колонка 3: Контактна інформація -->
            <div>
                <h3 class="text-xl font-bold mb-4">Контакти</h3>
                <!-- Адреса компанії -->
                <p class="text-gray-300">Київ, вул. Спортивна, 1</p>
                <!-- Номер телефону -->
                <p class="text-gray-300">Телефон: +380 95 777 5927</p>
                <!-- Email адреса -->
                <p class="text-gray-300">Email: info@sport-nutrition.ua</p>
            </div>
        </div>
        
        <!-- Копірайт з динамічним роком -->
        <div class="mt-8 text-center text-gray-400">
            <p>&copy; {{ date('Y') }} Sport Nutrition. Всі права захищені.</p>
        </div>
    </div>
</footer> 