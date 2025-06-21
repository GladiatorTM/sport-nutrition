<div class="bg-gray-100 border-b border-gray-200">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between py-4 px-6">
        <!-- Логотип і слоган -->
        <div class="flex items-center w-full md:w-auto mb-4 md:mb-0">
            <div class="flex items-center space-x-4">
                <a href="/">
                    <img src="{{ asset('images/logo_clean.png') }}" alt="Логотип" class="h-14 w-auto hover:scale-110 transition">
                </a>
                <div class="leading-tight">
                    <div class="font-bold text-lg text-black flex flex-col">
                        <span class="text-black flex items-center">
                            <span class="text-black">|</span>
                            <span class="text-yellow-500 ml-1">ВСЕ</span>
                            <span class="text-blue-600 ml-1">БУДЕ</span>
                            <span class="text-black ml-1">УКРАЇНА</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Навігаційне меню -->
        <nav class="flex items-center space-x-8 ml-8">
            <a href="{{ route('home') }}" 
               class="text-gray-700 hover:text-gray-900 font-medium transition-colors duration-200 {{ request()->routeIs('home') ? 'text-gray-900 border-b-2 border-yellow-400' : '' }}">
                Головна
            </a>
            <a href="{{ route('products.index') }}" 
               class="text-gray-700 hover:text-gray-900 font-medium transition-colors duration-200 {{ request()->routeIs('products.*') ? 'text-gray-900 border-b-2 border-yellow-400' : '' }}">
                Товари
            </a>
        </nav>
        <!-- Пошук праворуч -->
        <form action="{{ route('products.index') }}" method="GET" class="flex w-full max-w-xs bg-white border border-gray-300 rounded-lg overflow-hidden shadow-md ml-8 md:ml-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Пошук товарів" class="w-full border-0 px-4 py-2 focus:outline-none text-lg placeholder-gray-200">
            <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 px-4 py-2 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" fill="none"/>
                  <line x1="16.5" y1="16.5" x2="21" y2="21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </form>
        <!-- Контакти, корзина, профіль -->
        <div class="flex flex-col items-end space-y-2 w-full md:w-auto mt-4 md:mt-0 md:ml-8">
            <div class="flex items-center space-x-6">
                <div class="text-right">
                    <div class="text-2xl font-bold text-black leading-tight">+380 95 777 5927</div>
                    <div class="text-xs text-gray-700">Безкоштовно по Україні</div>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- Корзина -->
                    <a href="#" onclick="showCartModal(); return false;" class="relative flex items-center group">
                        <svg class="h-8 w-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="cart-count absolute -top-2 -right-2 bg-yellow-400 text-xs font-bold rounded-full px-1">0</span>
                    </a>
                    @guest
                    <a href="{{ route('login') }}" class="px-3 py-1 text-sm font-bold text-white bg-yellow-400 rounded-md hover:bg-yellow-500 transition ml-4">Вхід</a>
                    @endguest
                    @auth
                    <!-- Профіль-аватар -->
                    <div class="relative ml-4" onmouseenter="showProfileMenu()" onmouseleave="delayedHideProfileMenu()">
                        <a href="{{ route('profile.edit') }}" class="flex items-center">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('avatars/' . Auth::user()->avatar) }}" alt="Аватар" class="h-8 w-8 rounded-full object-cover border-2 border-blue-400">
                            @else
                                <div class="h-8 w-8 rounded-full bg-yellow-200 flex items-center justify-center text-lg text-yellow-600 font-bold border-2 border-blue-400">
                                    {{ strtoupper(mb_substr(Auth::user()->name,0,1)) }}
                                </div>
                            @endif
                        </a>
                        <!-- Меню -->
                        <div id="profile-menu" class="absolute right-0 mt-2 w-32 bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-50 hidden">
                            @if(Auth::user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}"
                                   class="block w-full text-left px-4 py-2 text-blue-700 font-bold hover:bg-gray-100">
                                    Адмін-панель
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">Вийти</button>
                            </form>
                        </div>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальне вікно кошика -->
<div id="cart-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 relative flex flex-col items-center border-4 border-yellow-400 animate__animated animate__fadeInDown">
        <button onclick="closeCartModal()" class="absolute top-3 right-3 text-gray-400 hover:text-black text-2xl">&times;</button>
        <h2 class="text-3xl font-extrabold text-yellow-500 mb-4 flex items-center gap-2">
            <svg class="h-8 w-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            Кошик
        </h2>
        <div id="cart-items" class="w-full max-h-72 overflow-y-auto mb-4"></div>
        <div class="w-full flex justify-between items-center text-lg font-bold mb-4">
            <span>Сума:</span>
            <span id="cart-total" class="text-yellow-500">0 грн</span>
        </div>
        <div class="w-full flex gap-4">
            <button onclick="checkoutCart()" class="flex-1 bg-gradient-to-r from-yellow-400 to-blue-400 hover:from-blue-500 hover:to-yellow-500 text-white font-bold py-2 rounded-lg shadow transition">Оформити замовлення</button>
            <button onclick="clearCart()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 rounded-lg shadow transition">Очистити</button>
        </div>
    </div>
</div>

<script>
let profileMenuTimeout;
function showProfileMenu() {
    clearTimeout(profileMenuTimeout);
    document.getElementById('profile-menu').classList.remove('hidden');
}
function hideProfileMenu() {
    document.getElementById('profile-menu').classList.add('hidden');
}
function delayedHideProfileMenu() {
    profileMenuTimeout = setTimeout(hideProfileMenu, 200);
}

// --- CART LOGIC ---
function getCart() {
    return JSON.parse(localStorage.getItem('cart') || '[]');
}
function setCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartUI();
}
function addToCart(product) {
    let cart = getCart();
    const idx = cart.findIndex(item => item.id === product.id);
    if (idx > -1) {
        cart[idx].qty += 1;
    } else {
        cart.push({...product, qty: 1});
    }
    setCart(cart);
    showCartModal();
}
function removeFromCart(id) {
    let cart = getCart().filter(item => item.id !== id);
    setCart(cart);
}
function changeQty(id, delta) {
    let cart = getCart();
    const idx = cart.findIndex(item => item.id === id);
    if (idx > -1) {
        cart[idx].qty += delta;
        if (cart[idx].qty < 1) cart[idx].qty = 1;
        setCart(cart);
    }
}
function clearCart() {
    setCart([]);
}
function updateCartUI() {
    const cart = getCart();
    const cartItems = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    const cartCount = document.querySelectorAll('.cart-count');
    let sum = 0;
    cartItems.innerHTML = cart.length ? cart.map(item => `
        <div class='flex items-center justify-between gap-2 border-b py-2'>
            <div class='flex items-center gap-3'>
                <img src='${item.image_path ? item.image_path : '/images/no-image.png'}' class='h-12 w-12 rounded-lg object-cover border'>
                <div>
                    <div class='font-bold text-gray-900'>${item.name}</div>
                    <div class='text-sm text-gray-500'>${item.price} грн</div>
                </div>
            </div>
            <div class='flex items-center gap-2'>
                <button onclick='changeQty(${item.id},-1)' class='px-2 py-1 bg-gray-200 rounded hover:bg-gray-300'>-</button>
                <span class='font-bold'>${item.qty}</span>
                <button onclick='changeQty(${item.id},1)' class='px-2 py-1 bg-gray-200 rounded hover:bg-gray-300'>+</button>
                <button onclick='removeFromCart(${item.id})' class='ml-2 text-red-500 hover:text-red-700'>&times;</button>
            </div>
        </div>
    `).join('') : "<div class='text-gray-500 text-center py-8'>Кошик порожній</div>";
    sum = cart.reduce((acc, item) => acc + item.price * item.qty, 0);
    cartTotal.textContent = sum.toLocaleString() + ' грн';
    document.querySelectorAll('.cart-count').forEach(el => el.textContent = cart.length);
}
function showCartModal() {
    document.getElementById('cart-modal').classList.remove('hidden');
    updateCartUI();
}
function closeCartModal() {
    document.getElementById('cart-modal').classList.add('hidden');
}
function checkoutCart() {
    alert('Оформлення замовлення поки що у розробці :)');
}
// --- INIT ---
document.addEventListener('DOMContentLoaded', updateCartUI);
</script>
