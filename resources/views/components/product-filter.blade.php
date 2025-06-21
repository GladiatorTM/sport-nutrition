<!-- Компонент фільтрації продуктів -->
<!-- Включає пошук, фільтр по ціні з слайдером, сортування та кнопки управління -->

<!-- Підключення CSS для слайдера цін -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css">

<!-- Форма фільтрації з Alpine.js для реактивності -->
<form method="GET" class="mb-8 flex flex-wrap gap-4 items-end justify-center bg-gradient-to-r from-yellow-100 via-blue-100 to-white bg-opacity-80 rounded-xl shadow p-4 border border-yellow-200" x-data="{
    minLimit: Number('{{ isset($minPrice) ? $minPrice : 0 }}'),
    maxLimit: Number('{{ isset($maxPrice) ? $maxPrice : 5000 }}'),
    min: Number('{{ request('price_from', isset($minPrice) ? $minPrice : 0) }}'),
    max: Number('{{ request('price_to', isset($maxPrice) ? $maxPrice : 1000) }}'),
    updateMin(e) { this.min = Math.min(Number(e.target.value), this.max); },
    updateMax(e) { this.max = Math.max(Number(e.target.value), this.min); }
}">
    
    <!-- Поле пошуку -->
    <div class="flex flex-col">
        <label for="search" class="block text-xs font-bold text-gray-600 mb-1">Пошук</label>
        <div class="relative flex items-center">
            <!-- Іконка пошуку -->
            <span class="absolute left-2 top-1/2 -translate-y-1/2 flex items-center">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2"/></svg>
            </span>
            <!-- Поле введення пошукового запиту -->
            <input type="text" name="search" id="search" value="{{ request('search') }}" class="border rounded pl-10 pr-2 py-2 focus:outline-none focus:border-yellow-500 w-44" placeholder="Назва товару">
        </div>
    </div>
    
    <!-- Фільтр по ціні з слайдером -->
    <div class="flex flex-col min-w-[240px]">
        <label class="block text-xs font-bold text-gray-600 mb-1">Ціна</label>
        <div class="flex flex-col gap-2">
            <!-- Слайдер цін (ініціалізується через JavaScript) -->
            <div id="price-slider" class="mb-2"></div>
            
            <!-- Поля введення мінімальної та максимальної ціни -->
            <div class="flex items-center gap-2 justify-between text-xs">
                <!-- Поле "від" -->
                <div class="flex items-center gap-1">
                    <span>від</span>
                    <input type="number" name="price_from" id="price_from" min="{{ $minPrice ?? 0 }}" max="{{ $maxPrice ?? 5000 }}" step="1" class="border rounded px-2 py-1 w-16 text-xs focus:outline-none focus:border-yellow-500" value="{{ request('price_from', $minPrice ?? 0) }}">
                </div>
                <!-- Поле "до" -->
                <div class="flex items-center gap-1">
                    <span>до</span>
                    <input type="number" name="price_to" id="price_to" min="{{ $minPrice ?? 0 }}" max="{{ $maxPrice ?? 5000 }}" step="1" class="border rounded px-2 py-1 w-16 text-xs focus:outline-none focus:border-yellow-500" value="{{ request('price_to', $maxPrice ?? 1000) }}">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Селект сортування -->
    <div class="flex flex-col">
        <label for="sort" class="block text-xs font-bold text-gray-600 mb-1">Сортувати</label>
        <select name="sort" id="sort" class="border rounded px-3 py-2 focus:outline-none focus:border-yellow-500 w-56 truncate">
            <option value="popular" @if(request('sort')=='popular') selected @endif>За популярністю</option>
            <option value="price_asc" @if(request('sort')=='price_asc') selected @endif>Ціна: спочатку дешеві</option>
            <option value="price_desc" @if(request('sort')=='price_desc') selected @endif>Ціна: спочатку дорогі</option>
            <option value="newest" @if(request('sort')=='newest') selected @endif>Нові спочатку</option>
            <option value="oldest" @if(request('sort')=='oldest') selected @endif>Старі спочатку</option>
        </select>
    </div>
    
    <!-- Кнопки управління -->
    <div class="flex flex-col mt-4 sm:mt-0">
        <!-- Кнопка застосування фільтрів -->
        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold px-4 py-2 rounded w-full">Застосувати</button>
        
        <!-- Кнопка скидання фільтрів (показується тільки якщо є активні фільтри) -->
        @if(request()->has('search') || request()->has('price_from') || request()->has('price_to') || request()->has('sort'))
            <a href="{{ url()->current() }}" class="mt-2 text-gray-500 underline text-center">Скинути</a>
        @endif
    </div>
</form>

<!-- Підключення JavaScript для слайдера цін -->
<script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js"></script>

<script>
/**
 * JavaScript для ініціалізації та роботи слайдера цін
 */
document.addEventListener('DOMContentLoaded', function () {
    // Отримуємо значення цін з PHP
    var minPrice = {{ $minPrice ?? 0 }};
    var maxPrice = {{ $maxPrice ?? 5000 }};
    
    // Елементи DOM
    var priceFrom = document.getElementById('price_from');
    var priceTo = document.getElementById('price_to');
    var slider = document.getElementById('price-slider');
    
    // Ініціалізація слайдера якщо він існує і бібліотека завантажена
    if (slider && typeof noUiSlider !== 'undefined') {
        // Створюємо слайдер з початковими значеннями
        noUiSlider.create(slider, {
            start: [Number(priceFrom.value) || minPrice, Number(priceTo.value) || 1000],
            connect: true,  // З'єднуємо ручки слайдера
            step: 1,        // Крок зміни значення
            range: {
                'min': minPrice,
                'max': maxPrice
            },
            tooltips: [true, true],  // Показуємо підказки з поточними значеннями
            format: {
                // Форматування значень для відображення
                to: function (value) { return Math.round(value) + ' грн'; },
                from: function (value) { return Number(value.replace(' грн','')); }
            }
        });
        
        // Обробник оновлення значень слайдера
        slider.noUiSlider.on('update', function (values, handle) {
            // Оновлюємо поля введення при зміні слайдера
            priceFrom.value = values[0].replace(' грн','');
            priceTo.value = values[1].replace(' грн','');
        });
        
        // Обробник зміни мінімальної ціни в полі введення
        priceFrom.addEventListener('change', function () {
            var from = Math.max(minPrice, Math.min(Number(priceFrom.value), Number(priceTo.value)));
            slider.noUiSlider.set([from, null]);
        });
        
        // Обробник зміни максимальної ціни в полі введення
        priceTo.addEventListener('change', function () {
            var to = Math.min(maxPrice, Math.max(Number(priceTo.value), Number(priceFrom.value)));
            slider.noUiSlider.set([null, to]);
        });
    }
});
</script>

<!-- Стилі для слайдера цін -->
<style>
/* Адаптивність для мобільних пристроїв */
@media (max-width: 640px) {
    form.flex.flex-wrap.gap-4 > div { width: 100%; }
    form.flex.flex-wrap.gap-4 { gap: 0.5rem; }
}

/* Базові стилі слайдера */
#price-slider { 
    margin-top: 8px; 
    margin-bottom: 8px; 
}

/* Стилізація ручок слайдера */
#price-slider .noUi-handle {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #facc15;  /* Жовтий колір */
    border: 2px solid #f59e42;
    box-shadow: 0 2px 8px #facc1540;
    cursor: grab;
    top: -7px;
}

/* Приховуємо внутрішні елементи ручок */
#price-slider .noUi-handle:after, 
#price-slider .noUi-handle:before { 
    display: none; 
}

/* Стилізація з'єднання між ручками */
#price-slider .noUi-connect {
    background: linear-gradient(90deg, #facc15 0%, #60a5fa 100%);
}

/* Стилізація підказок зі значеннями */
#price-slider .noUi-tooltip {
    background: #fffbea;
    color: #b45309;
    border: 1px solid #facc15;
    font-weight: bold;
    font-size: 13px;
    border-radius: 8px;
    padding: 2px 8px;
    box-shadow: 0 2px 8px #facc1540;
    top: -38px;
}
</style> 