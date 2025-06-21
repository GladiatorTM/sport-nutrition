<!-- Компонент модального вікна авторизації -->
<!-- Використовується для входу та реєстрації користувачів -->
@props(['show' => false])

<!-- Модальне вікно з Alpine.js для управління станом -->
<div id="gym-auth-modal" x-data="{ show: false, tab: 'login' }" x-cloak x-on:open-modal.window="show = ($event.detail === 'gym-auth')">
    <!-- Затемнення фону та контейнер модального вікна -->
    <div x-show="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
        <!-- Основний контент модального вікна -->
        <div @click.away="show = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 relative flex flex-col items-center border-4 border-yellow-400">
            <!-- Кнопка закриття модального вікна -->
            <button @click="show = false" class="absolute top-3 right-3 text-gray-400 hover:text-black text-2xl">&times;</button>
            
            <!-- Логотип компанії -->
            <div class="flex flex-col items-center mb-6">
                <img src="{{ asset('images/logo_clean.png') }}" alt="Логотип" class="h-14 w-auto mb-2">
            </div>
            
            <!-- Перемикач між входом та реєстрацією -->
            <div class="flex w-full mb-6">
                <!-- Кнопка "Вхід" -->
                <button @click="tab = 'login'" :class="tab === 'login' ? 'bg-yellow-400 text-black' : 'bg-gray-100 text-gray-500'" class="w-1/2 py-2 rounded-l-lg font-bold transition">Вхід</button>
                <!-- Кнопка "Реєстрація" -->
                <button @click="tab = 'register'" :class="tab === 'register' ? 'bg-yellow-400 text-black' : 'bg-gray-100 text-gray-500'" class="w-1/2 py-2 rounded-r-lg font-bold transition">Реєстрація</button>
            </div>
            
            <!-- Форма входу (показується коли tab === 'login') -->
            <div x-show="tab === 'login'" class="w-full">
                <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
                    @csrf
                    <!-- Поле введення email -->
                    <input type="email" name="email" placeholder="Email" required autofocus class="rounded-lg border-2 border-gray-200 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-200 text-lg">
                    <!-- Поле введення пароля -->
                    <input type="password" name="password" placeholder="Пароль" required class="rounded-lg border-2 border-gray-200 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-200 text-lg">
                    
                    <!-- Додаткові опції входу -->
                    <div class="flex items-center justify-between">
                        <!-- Чекбокс "Запам'ятати мене" -->
                        <label class="flex items-center text-sm">
                            <input type="checkbox" name="remember" class="mr-2 rounded border-gray-300 text-yellow-500 focus:ring-yellow-400">
                            Запам'ятати мене
                        </label>
                        <!-- Посилання на відновлення пароля -->
                        <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:underline">Забули пароль?</a>
                    </div>
                    
                    <!-- Кнопка входу -->
                    <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 rounded-lg text-lg transition">Увійти</button>
                </form>
                
                <!-- Розділювач між звичайним входом та входом через Google -->
                <div class="my-4 flex items-center justify-center gap-2">
                    <span class="h-px w-10 bg-gray-300"></span>
                    <span class="text-gray-400 text-sm">або</span>
                    <span class="h-px w-10 bg-gray-300"></span>
                </div>
                
                <!-- Кнопка входу через Google -->
                <a href="{{ route('google.login') }}" class="flex items-center justify-center gap-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold py-2 rounded-lg text-lg transition shadow">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/google/google-original.svg" alt="Google" class="h-6 w-6">
                    Увійти через Google
                </a>
            </div>
            
            <!-- Форма реєстрації (показується коли tab === 'register') -->
            <div x-show="tab === 'register'" class="w-full">
                <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
                    @csrf
                    <!-- Поле введення імені -->
                    <input type="text" name="name" placeholder="Ім'я" required class="rounded-lg border-2 border-gray-200 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-200 text-lg">
                    <!-- Поле введення email -->
                    <input type="email" name="email" placeholder="Email" required class="rounded-lg border-2 border-gray-200 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-200 text-lg">
                    <!-- Поле введення пароля -->
                    <input type="password" name="password" placeholder="Пароль" required class="rounded-lg border-2 border-gray-200 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-200 text-lg">
                    <!-- Поле підтвердження пароля -->
                    <input type="password" name="password_confirmation" placeholder="Підтвердіть пароль" required class="rounded-lg border-2 border-gray-200 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-200 text-lg">
                    
                    <!-- Кнопка реєстрації -->
                    <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 rounded-lg text-lg transition">Зареєструватися</button>
                </form>
            </div>
        </div>
    </div>
</div> 