@extends('layouts.app')
@section('content')
<!-- Сторінка каталогу продуктів з фільтрацією та пошуком -->
<div class="min-h-screen py-12 bg-gradient-to-br from-yellow-100 via-blue-100 to-white flex flex-col items-center">
    <div class="max-w-7xl w-full">
        <!-- Заголовок сторінки -->
        <h1 class="text-4xl font-extrabold text-yellow-500 mb-8 text-center drop-shadow">Всі товари магазину</h1>
        
        <!-- Компонент фільтрації продуктів (пошук, ціна, сортування) -->
        @include('components.product-filter', ['minPrice' => $minPrice, 'maxPrice' => $maxPrice])
        
        <!-- Фільтр по категоріях - кольорові теги -->
        <div class="flex flex-wrap gap-2 justify-center mb-8">
            <!-- Кнопка "Всі" - скидає фільтр категорії -->
            <a href="{{ route('products.index') }}" class="px-4 py-1 rounded-full font-bold text-xs transition {{ request('category') ? 'bg-yellow-100 text-yellow-600 border border-yellow-400 hover:bg-yellow-200' : 'bg-yellow-400 text-white shadow' }}">Всі</a>
            
            <!-- Динамічні кнопки категорій -->
            @foreach(\App\Models\Category::all() as $cat)
                <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $cat->id])) }}" class="px-4 py-1 rounded-full font-bold text-xs transition {{ request('category') == $cat->id ? 'bg-yellow-400 text-white shadow' : 'bg-yellow-100 text-yellow-600 border border-yellow-400 hover:bg-yellow-200' }}">{{ $cat->name }}</a>
            @endforeach
        </div>
        
        <!-- Перевірка авторизації користувача -->
        @php $isAuth = auth()->check(); @endphp
        
        <!-- Сітка продуктів -->
        @if($products->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @foreach($products as $product)
                <!-- Картка продукту -->
                <div class="rounded-2xl border-2 border-yellow-300 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col overflow-hidden cursor-pointer bg-gradient-to-br from-yellow-100 via-blue-100 to-white hover:from-yellow-200 hover:via-blue-200" onclick="openModal({{ $product->id }})">
                    <!-- Зображення продукту -->
                    @if($product->image_path)
                        <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover object-center bg-gray-100">
                    @else
                        <!-- Заглушка якщо зображення відсутнє -->
                        <div class="w-full h-48 bg-gradient-to-br from-yellow-100 via-blue-100 to-white flex items-center justify-center">
                            <span class="text-4xl text-yellow-400 font-bold">?</span>
                        </div>
                    @endif
                    
                    <!-- Інформація про продукт -->
                    <div class="p-5 flex flex-col flex-1">
                        <!-- Назва продукту -->
                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $product->name }}</h3>
                        
                        <!-- Опис продукту (обрізаний до 60 символів) -->
                        <div class="text-sm text-gray-500 mb-2">{{ Str::limit($product->description, 60) }}</div>
                        
                        <!-- Ціна продукту -->
                        @if($product->old_price)
                            <!-- Акційна ціна зі старою ціною -->
                            <div class="mb-1 flex items-baseline gap-2">
                                <span class="text-lg font-extrabold text-yellow-500">{{ number_format($product->price, 2) }} грн</span>
                                <span class="text-gray-400 line-through text-base">{{ number_format($product->old_price, 2) }} грн</span>
                            </div>
                        @else
                            <!-- Звичайна ціна -->
                            <div class="text-lg font-extrabold text-yellow-500 mb-3">{{ number_format($product->price, 2) }} грн</div>
                        @endif
                        
                        <!-- Кнопка "Додати в кошик" -->
                        <button onclick="event.stopPropagation(); @if($isAuth) addToCart({id: {{ $product->id }}, name: '{{ addslashes($product->name) }}', price: {{ $product->price }}, image_path: '{{ $product->image_path }}'}) @else showAuthModal({{ $product->id }}) @endif" class="mt-auto bg-gradient-to-r from-yellow-400 to-blue-400 hover:from-blue-500 hover:to-yellow-500 text-white font-bold py-2 px-4 rounded-lg shadow transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A2 2 0 008.48 18h7.04a2 2 0 001.83-2.3L17 13M7 13V6h13" /></svg>
                            Додати в кошик
                        </button>
                    </div>
                </div>
                
                <!-- Модальне вікно з детальною інформацією про продукт -->
                <x-product-modal :product="$product" />
            @endforeach
        </div>
        
        <!-- Пагінація -->
        <div class="mt-8">
            {{ $products->appends(request()->except('page'))->links('pagination::tailwind', ['color' => 'yellow']) }}
        </div>
        @else
            <!-- Повідомлення якщо товарів не знайдено -->
            <div class="text-center text-gray-500 py-16 text-xl font-bold">Товарів не знайдено.</div>
        @endif
    </div>

    <!-- Компонент чат-асистента -->
    <x-chat-assistant />
</div>

<!-- Модальне вікно авторизації -->
@include('components.gym-auth-modal')

<script>
/**
 * JavaScript для функціональності сторінки продуктів
 */

// Масив всіх продуктів для живого пошуку (без AJAX)
let allProducts = [
    @foreach(\App\Models\Product::select('id','name','price','image_path')->get() as $p)
        {id: {{ $p->id }}, name: "{{ addslashes($p->name) }}", price: {{ $p->price }}, image_path: "{{ $p->image_path }}"},
    @endforeach
];

// Елементи для живого пошуку
const searchInput = document.getElementById('live-search');
const suggestions = document.getElementById('search-suggestions');

/**
 * Обробник введення в поле пошуку
 * Реалізує живий пошук без AJAX запитів
 */
searchInput.addEventListener('input', function() {
    const val = this.value.trim().toLowerCase();
    
    // Показуємо підказки тільки якщо введено 2+ символи
    if (val.length < 2) {
        suggestions.classList.add('hidden');
        return;
    }
    
    // Фільтруємо продукти по назві
    const found = allProducts.filter(p => p.name.toLowerCase().includes(val)).slice(0, 8);
    
    if (found.length === 0) {
        // Повідомлення якщо нічого не знайдено
        suggestions.innerHTML = '<li class="px-4 py-2 text-gray-400">Нічого не знайдено</li>';
    } else {
        // Формуємо список знайдених продуктів
        suggestions.innerHTML = found.map(p =>
            `<li class='px-4 py-2 hover:bg-yellow-100 cursor-pointer flex items-center gap-2' onclick='window.location="/products?search=${encodeURIComponent(p.name)}"'>
                <img src='${p.image_path ? p.image_path : '/images/no-image.png'}' class='h-8 w-8 rounded object-cover border'>
                <span class='font-bold text-gray-800'>${p.name}</span>
                <span class='ml-auto text-yellow-500 font-bold'>${p.price} грн</span>
            </li>`
        ).join('');
    }
    suggestions.classList.remove('hidden');
});

/**
 * Приховує підказки пошуку при кліку поза полем пошуку
 */
document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !suggestions.contains(e.target)) {
        suggestions.classList.add('hidden');
    }
});

/**
 * Показує модальне вікно авторизації для неавторизованих користувачів
 * Зберігає ID продукту для додавання в кошик після авторизації
 * @param {number} productId - ID продукту для додавання в кошик
 */
function showAuthModal(productId) {
    // Зберігаємо ID продукту в localStorage для подальшого використання
    window.localStorage.setItem('pending_product_id', productId);
    // Відкриваємо модальне вікно авторизації
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'gym-auth' }));
}
</script>
@endsection 