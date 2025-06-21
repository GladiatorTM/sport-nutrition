<nav class="bg-yellow-400 shadow">
    <div class="max-w-7xl mx-auto px-4">
        <ul class="flex flex-wrap items-center justify-between">
            <li>
                <a href="{{ route('categories.aktsii') }}" 
                   class="block px-4 py-3 font-bold {{ request()->routeIs('categories.aktsii') ? 'text-blue-600 bg-yellow-500 rounded-t' : 'text-gray-800 hover:bg-yellow-300' }} transition">
                    АКЦІЇ
                </a>
            </li>
            <li>
                <a href="{{ route('categories.sport') }}" 
                   class="block px-4 py-3 font-bold {{ request()->routeIs('categories.sport') ? 'text-blue-600 bg-yellow-500 rounded-t' : 'text-gray-800 hover:bg-yellow-300' }} transition">
                    СПОРТ
                </a>
            </li>
            <li>
                <a href="{{ route('categories.dobavky') }}" 
                   class="block px-4 py-3 font-bold {{ request()->routeIs('categories.dobavky') ? 'text-blue-600 bg-yellow-500 rounded-t' : 'text-gray-800 hover:bg-yellow-300' }} transition">
                    ДОБАВКИ
                </a>
            </li>
            <li>
                <a href="{{ route('categories.vitaminy') }}" 
                   class="block px-4 py-3 font-bold {{ request()->routeIs('categories.vitaminy') ? 'text-blue-600 bg-yellow-500 rounded-t' : 'text-gray-800 hover:bg-yellow-300' }} transition">
                    ВІТАМІНИ
                </a>
            </li>
            <li>
                <a href="{{ route('categories.zdorovya') }}" 
                   class="block px-4 py-3 font-bold {{ request()->routeIs('categories.zdorovya') ? 'text-blue-600 bg-yellow-500 rounded-t' : 'text-gray-800 hover:bg-yellow-300' }} transition">
                    ЗДОРОВ'Я
                </a>
            </li>
            <li>
                <a href="{{ route('categories.brendy') }}" 
                   class="block px-4 py-3 font-bold {{ request()->routeIs('categories.brendy') ? 'text-blue-600 bg-yellow-500 rounded-t' : 'text-gray-800 hover:bg-yellow-300' }} transition">
                    БРЕНДИ
                </a>
            </li>
            <li>
                <a href="{{ route('categories.blog') }}" 
                   class="block px-4 py-3 font-bold {{ request()->routeIs('categories.blog') ? 'text-blue-600 bg-yellow-500 rounded-t' : 'text-gray-800 hover:bg-yellow-300' }} transition">
                    БЛОГ
                </a>
            </li>
            <li>
                <a href="{{ route('categories.kontakty') }}" 
                   class="block px-4 py-3 font-bold {{ request()->routeIs('categories.kontakty') ? 'text-blue-600 bg-yellow-500 rounded-t' : 'text-gray-800 hover:bg-yellow-300' }} transition">
                    КОНТАКТИ
                </a>
            </li>
        </ul>
    </div>
</nav> 