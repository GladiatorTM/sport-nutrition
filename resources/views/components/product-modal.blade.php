<!-- Компонент модального вікна для відображення детальної інформації про продукт -->
<!-- Приймає параметр $product - об'єкт продукту для відображення -->
@props(['product'])

<!-- Перевірка авторизації користувача -->
@php $isAuth = auth()->check(); @endphp

<!-- Модальне вікно продукту -->
<div id="product-modal-{{ $product->id }}" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="min-h-screen px-4 text-center">
        <!-- Затемнення фону -->
        <div class="fixed inset-0" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Вертикальне центрування -->
        <span class="inline-block h-screen align-middle" aria-hidden="true">&#8203;</span>

        <!-- Основний контент модального вікна -->
        <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
            <!-- Заголовок з кнопкою закриття -->
            <div class="flex justify-between items-start">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $product->name }}</h3>
                <!-- Кнопка закриття модального вікна -->
                <button onclick="closeModal({{ $product->id }})" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Контент модального вікна -->
            <div class="mt-4">
                <!-- Зображення продукту -->
                <div class="aspect-w-16 aspect-h-9 mb-4">
                    @if($product->image_path)
                        <!-- Зображення продукту якщо воно є -->
                        <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-64 object-cover rounded-lg">
                    @else
                        <!-- Заглушка якщо зображення відсутнє -->
                        <div class="w-full h-64 bg-gradient-to-br from-yellow-100 via-blue-100 to-white flex items-center justify-center rounded-lg">
                            <span class="text-4xl text-yellow-400 font-bold">?</span>
                        </div>
                    @endif
                </div>

                <!-- Детальна інформація про продукт -->
                <div class="space-y-4">
                    <!-- Опис продукту -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-700">Опис</h4>
                        <p class="mt-2 text-gray-600">{{ $product->description }}</p>
                    </div>

                    <!-- Категорія продукту -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-700">Категорія</h4>
                        <p class="mt-2 text-gray-600">{{ $product->category->name }}</p>
                    </div>

                    <!-- Ціна продукту -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-700">Ціна</h4>
                        <p class="mt-2 text-2xl font-bold text-yellow-500">{{ number_format($product->price, 2) }} грн</p>
                    </div>

                    <!-- Кнопка додавання в кошик -->
                    <div class="mt-6">
                        <button onclick="@if($isAuth) addToCart({id: {{ $product->id }}, name: '{{ addslashes($product->name) }}', price: {{ $product->price }}, image_path: '{{ $product->image_path }}'}); closeModal({{ $product->id }}); @else showAuthModal({{ $product->id }}); closeModal({{ $product->id }}); @endif" class="w-full bg-gradient-to-r from-blue-400 to-yellow-400 hover:from-yellow-500 hover:to-blue-500 text-white font-bold py-2 px-4 rounded-lg shadow transition">
                            Додати в кошик
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * JavaScript функції для управління модальним вікном продукту
 */

/**
 * Відкриває модальне вікно продукту
 * @param {number} productId - ID продукту для відображення
 */
function openModal(productId) {
    // Показуємо модальне вікно
    document.getElementById('product-modal-' + productId).classList.remove('hidden');
    // Блокуємо прокрутку сторінки
    document.body.style.overflow = 'hidden';
}

/**
 * Закриває модальне вікно продукту
 * @param {number} productId - ID продукту
 */
function closeModal(productId) {
    // Приховуємо модальне вікно
    document.getElementById('product-modal-' + productId).classList.add('hidden');
    // Відновлюємо прокрутку сторінки
    document.body.style.overflow = 'auto';
}

/**
 * Обробник кліку для закриття модального вікна при кліку поза ним
 * Знаходить всі модальні вікна продуктів і закриває їх при кліку на затемнення
 */
document.addEventListener('click', function(event) {
    // Знаходимо всі модальні вікна продуктів
    const modals = document.querySelectorAll('[id^="product-modal-"]');
    
    modals.forEach(modal => {
        // Якщо клік був на самому модальному вікні (затемненні), закриваємо його
        if (event.target === modal) {
            const productId = modal.id.split('-')[2];
            closeModal(productId);
        }
    });
});
</script> 