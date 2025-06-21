# ФІЛЬТРАЦІЯ ТА ПОШУК

## 5.1. Контролер системи фільтрації та пошуку товарів

### 5.1.1. Інтеграція фільтрації в ProductController

Фільтрація та пошук реалізовані безпосередньо в методах контролерів:

```php
class ProductController extends Controller
{
    /**
     * Відображення списку продуктів з розширеною фільтрацією
     */
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Пошук по назві та опису продукту
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }
        
        // Фільтрація по категорії
        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }
        
        // Фільтрація по діапазону цін
        if ($request->filled('price_from')) {
            $query->where('price', '>=', floatval($request->input('price_from')));
        }
        if ($request->filled('price_to')) {
            $query->where('price', '<=', floatval($request->input('price_to')));
        }
        
        // Сортування продуктів
        $sort = $request->input('sort', 'popular');
        switch($sort) {
            case 'price_asc': 
                $query->orderBy('price', 'asc'); 
                break;
            case 'price_desc': 
                $query->orderBy('price', 'desc'); 
                break;
            case 'newest': 
                $query->orderBy('created_at', 'desc'); 
                break;
            case 'oldest': 
                $query->orderBy('created_at', 'asc'); 
                break;
            default: 
                $query->orderBy('views', 'desc'); // popular
        }
        
        $products = $query->paginate(12)->appends($request->query());
        
        // Розрахунок діапазону цін для фільтрів
        $filteredMinPrice = (clone $query)->min('price');
        $filteredMaxPrice = (clone $query)->max('price');
        
        return view('products.index', [
            'products' => $products,
            'minPrice' => $filteredMinPrice,
            'maxPrice' => $filteredMaxPrice
        ]);
    }
}
```

### 5.1.2. Розширена фільтрація в CategoryController

```php
class CategoryController extends Controller
{
    /**
     * Відображення продуктів категорії з розширеним пошуком
     */
    public function showBySlug($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $query = Product::where('category_id', $category->id);
        
        // Розширений пошук по словах
        if (request('search')) {
            $search = request('search');
            $words = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
            $query->where(function($q) use ($words) {
                foreach ($words as $word) {
                    $q->where(function($sub) use ($word) {
                        $sub->where('name', 'like', "%$word%")
                             ->orWhere('description', 'like', "%$word%");
                    });
                }
            });
        }
        
        // Фільтрація по ціні
        if (request('price_from')) {
            $query->where('price', '>=', floatval(request('price_from')));
        }
        if (request('price_to')) {
            $query->where('price', '<=', floatval(request('price_to')));
        }
        
        // Спеціальна фільтрація для акційних товарів
        if ($slug === 'aktsii') {
            $query->whereNotNull('old_price')->where('old_price', '>', 0);
        }
        
        // Сортування
        $sort = request('sort', 'popular');
        switch($sort) {
            case 'price_asc': $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'newest': $query->orderBy('created_at', 'desc'); break;
            case 'oldest': $query->orderBy('created_at', 'asc'); break;
            default: $query->orderBy('views', 'desc');
        }
        
        $products = $query->paginate(12)->appends(request()->query());
        return view('categories.' . $slug, compact('products', 'category'));
    }
}
```

### 5.1.3. Алгоритми пошуку

**Простий пошук:**
```php
// Пошук по назві та опису
$query->where(function($q) use ($search) {
    $q->where('name', 'like', "%$search%")
      ->orWhere('description', 'like', "%$search%");
});
```

**Розширений пошук по словах:**
```php
// Розбиття пошукового запиту на слова
$words = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
$query->where(function($q) use ($words) {
    foreach ($words as $word) {
        $q->where(function($sub) use ($word) {
            $sub->where('name', 'like', "%$word%")
                 ->orWhere('description', 'like', "%$word%");
        });
    }
});
```

## 5.2. Модель сутності категорій для фільтрації

### 5.2.1. Розширена модель Category

```php
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    /**
     * Зв'язок з продуктами
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Отримання продуктів з фільтрацією
     */
    public function getFilteredProducts($filters = [])
    {
        $query = $this->products();

        // Фільтрація по пошуковому запиту
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $words = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
            $query->where(function($q) use ($words) {
                foreach ($words as $word) {
                    $q->where(function($sub) use ($word) {
                        $sub->where('name', 'like', "%$word%")
                             ->orWhere('description', 'like', "%$word%");
                    });
                }
            });
        }

        // Фільтрація по ціні
        if (isset($filters['price_from'])) {
            $query->where('price', '>=', floatval($filters['price_from']));
        }
        if (isset($filters['price_to'])) {
            $query->where('price', '<=', floatval($filters['price_to']));
        }

        // Сортування
        $sort = $filters['sort'] ?? 'popular';
        switch($sort) {
            case 'price_asc': $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'newest': $query->orderBy('created_at', 'desc'); break;
            case 'oldest': $query->orderBy('created_at', 'asc'); break;
            default: $query->orderBy('views', 'desc');
        }

        return $query;
    }

    /**
     * Статистика категорії
     */
    public function getStats()
    {
        return [
            'total_products' => $this->products()->count(),
            'min_price' => $this->products()->min('price'),
            'max_price' => $this->products()->max('price'),
            'avg_price' => $this->products()->avg('price'),
            'discount_products' => $this->products()->whereNotNull('old_price')->count()
        ];
    }
}
```

### 5.2.2. Розширена модель Product

```php
class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'old_price',
        'category_id',
        'image',
        'image_path',
        'views'
    ];

    /**
     * Зв'язок з категорією
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope для пошуку
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) return $query;

        $words = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
        return $query->where(function($q) use ($words) {
            foreach ($words as $word) {
                $q->where(function($sub) use ($word) {
                    $sub->where('name', 'like', "%$word%")
                         ->orWhere('description', 'like', "%$word%");
                });
            }
        });
    }

    /**
     * Scope для фільтрації по ціні
     */
    public function scopePriceRange($query, $min, $max)
    {
        if ($min !== null) {
            $query->where('price', '>=', floatval($min));
        }
        if ($max !== null) {
            $query->where('price', '<=', floatval($max));
        }
        return $query;
    }

    /**
     * Scope для акційних товарів
     */
    public function scopeOnSale($query)
    {
        return $query->whereNotNull('old_price')
                     ->where('old_price', '>', 0)
                     ->where('old_price', '>', 'price');
    }

    /**
     * Розрахунок відсотка знижки
     */
    public function getDiscountPercentAttribute()
    {
        if ($this->old_price && $this->old_price > $this->price) {
            return round((($this->old_price - $this->price) / $this->old_price) * 100);
        }
        return 0;
    }

    /**
     * Перевірка чи товар на акції
     */
    public function getIsOnSaleAttribute()
    {
        return $this->old_price && $this->old_price > $this->price;
    }
}
```

## 5.3. Представлення виду

### 5.3.1. Компонент фільтрації (product-filter.blade.php)

```html
<!-- Компонент фільтрації продуктів -->
<form method="GET" class="mb-8 flex flex-wrap gap-4 items-end justify-center bg-gradient-to-r from-yellow-100 via-blue-100 to-white bg-opacity-80 rounded-xl shadow p-4 border border-yellow-200" 
      x-data="{
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
            <span class="absolute left-2 top-1/2 -translate-y-1/2 flex items-center">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" stroke-width="2"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2"/>
                </svg>
            </span>
            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                   class="border rounded pl-10 pr-2 py-2 focus:outline-none focus:border-yellow-500 w-44" 
                   placeholder="Назва товару">
        </div>
    </div>
    
    <!-- Фільтр по ціні з слайдером -->
    <div class="flex flex-col min-w-[240px]">
        <label class="block text-xs font-bold text-gray-600 mb-1">Ціна</label>
        <div class="flex flex-col gap-2">
            <div id="price-slider" class="mb-2"></div>
            <div class="flex items-center gap-2 justify-between text-xs">
                <div class="flex items-center gap-1">
                    <span>від</span>
                    <input type="number" name="price_from" id="price_from" 
                           min="{{ $minPrice ?? 0 }}" max="{{ $maxPrice ?? 5000 }}" step="1" 
                           class="border rounded px-2 py-1 w-16 text-xs focus:outline-none focus:border-yellow-500" 
                           value="{{ request('price_from', $minPrice ?? 0) }}">
                </div>
                <div class="flex items-center gap-1">
                    <span>до</span>
                    <input type="number" name="price_to" id="price_to" 
                           min="{{ $minPrice ?? 0 }}" max="{{ $maxPrice ?? 5000 }}" step="1" 
                           class="border rounded px-2 py-1 w-16 text-xs focus:outline-none focus:border-yellow-500" 
                           value="{{ request('price_to', $maxPrice ?? 1000) }}">
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
        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold px-4 py-2 rounded w-full">
            Застосувати
        </button>
        
        @if(request()->has('search') || request()->has('price_from') || request()->has('price_to') || request()->has('sort'))
            <a href="{{ url()->current() }}" class="mt-2 text-gray-500 underline text-center">Скинути</a>
        @endif
    </div>
</form>

<!-- JavaScript для слайдера цін -->
<script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var minPrice = {{ $minPrice ?? 0 }};
    var maxPrice = {{ $maxPrice ?? 5000 }};
    var priceFrom = document.getElementById('price_from');
    var priceTo = document.getElementById('price_to');
    var slider = document.getElementById('price-slider');
    
    if (slider && typeof noUiSlider !== 'undefined') {
        noUiSlider.create(slider, {
            start: [Number(priceFrom.value) || minPrice, Number(priceTo.value) || 1000],
            connect: true,
            step: 1,
            range: {
                'min': minPrice,
                'max': maxPrice
            },
            tooltips: [true, true],
            format: {
                to: function (value) { return Math.round(value) + ' грн'; },
                from: function (value) { return Number(value.replace(' грн','')); }
            }
        });
        
        slider.noUiSlider.on('update', function (values, handle) {
            priceFrom.value = values[0].replace(' грн','');
            priceTo.value = values[1].replace(' грн','');
        });
        
        priceFrom.addEventListener('change', function () {
            var from = Math.max(minPrice, Math.min(Number(priceFrom.value), Number(priceTo.value)));
            slider.noUiSlider.set([from, null]);
        });
        
        priceTo.addEventListener('change', function () {
            var to = Math.min(maxPrice, Math.max(Number(priceTo.value), Number(priceFrom.value)));
            slider.noUiSlider.set([null, to]);
        });
    }
});
</script>
```

### 5.3.2. Модальне вікно продукту (product-modal.blade.php)

```html
<!-- Компонент модального вікна для відображення детальної інформації -->
@props(['product'])

<div id="product-modal-{{ $product->id }}" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="min-h-screen px-4 text-center">
        <div class="fixed inset-0" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="inline-block h-screen align-middle" aria-hidden="true">&#8203;</span>

        <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
            <!-- Заголовок -->
            <div class="flex justify-between items-start">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $product->name }}</h3>
                <button onclick="closeModal({{ $product->id }})" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Контент -->
            <div class="mt-4">
                <!-- Зображення -->
                <div class="aspect-w-16 aspect-h-9 mb-4">
                    @if($product->image_path)
                        <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-64 object-cover rounded-lg">
                    @else
                        <div class="w-full h-64 bg-gradient-to-br from-yellow-100 via-blue-100 to-white flex items-center justify-center rounded-lg">
                            <span class="text-4xl text-yellow-400 font-bold">?</span>
                        </div>
                    @endif
                </div>

                <!-- Інформація -->
                <div class="space-y-4">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-700">Опис</h4>
                        <p class="mt-2 text-gray-600">{{ $product->description }}</p>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold text-gray-700">Категорія</h4>
                        <p class="mt-2 text-gray-600">{{ $product->category->name }}</p>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold text-gray-700">Ціна</h4>
                        <div class="flex items-center space-x-2">
                            <p class="mt-2 text-2xl font-bold text-yellow-500">{{ number_format($product->price, 2) }} грн</p>
                            @if($product->is_on_sale)
                                <span class="px-2 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                    -{{ $product->discount_percent }}%
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6">
                        <button onclick="addToCart({{ $product->id }})" 
                                class="w-full bg-gradient-to-r from-blue-400 to-yellow-400 hover:from-yellow-500 hover:to-blue-500 text-white font-bold py-2 px-4 rounded-lg shadow transition">
                            Додати в кошик
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openModal(productId) {
    document.getElementById('product-modal-' + productId).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(productId) {
    document.getElementById('product-modal-' + productId).classList.add('hidden');
    document.body.style.overflow = 'auto';
}

document.addEventListener('click', function(event) {
    const modals = document.querySelectorAll('[id^="product-modal-"]');
    modals.forEach(modal => {
        if (event.target === modal) {
            const productId = modal.id.split('-')[2];
            closeModal(productId);
        }
    });
});
</script>
```

### 5.3.3. Результати пошуку (products/index.blade.php)

```html
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Фільтри -->
    @include('components.product-filter')

    <!-- Результати пошуку -->
    @if(request('search'))
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">
                Результати пошуку для "{{ request('search') }}"
            </h2>
            <p class="text-gray-600">Знайдено {{ $products->total() }} товарів</p>
        </div>
    @endif

    <!-- Сітка товарів -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <!-- Зображення -->
                <div class="aspect-w-1 aspect-h-1 w-full">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                
                <!-- Інформація -->
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                    <p class="text-gray-600 text-sm mb-3">{{ Str::limit($product->description, 100) }}</p>
                    
                    <!-- Ціни -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <span class="text-lg font-bold text-green-600">{{ number_format($product->price, 2) }} ₴</span>
                            @if($product->is_on_sale)
                                <span class="text-sm text-gray-500 line-through">{{ number_format($product->old_price, 2) }} ₴</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    -{{ $product->discount_percent }}%
                                </span>
                            @endif
                        </div>
                        
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $product->category->name }}
                        </span>
                    </div>
                    
                    <!-- Кнопки -->
                    <div class="flex space-x-2">
                        <button onclick="openModal({{ $product->id }})" 
                                class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Детальніше
                        </button>
                        <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Модальне вікно -->
            @include('components.product-modal', ['product' => $product])
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Товари не знайдено</h3>
                <p class="mt-1 text-sm text-gray-500">Спробуйте змінити параметри пошуку</p>
            </div>
        @endforelse
    </div>

    <!-- Пагінація -->
    <div class="mt-8">
        {{ $products->links() }}
    </div>
</div>
@endsection
```

## 5.4. JavaScript функціональність

### 5.4.1. Інтерактивний слайдер цін

```javascript
// Ініціалізація слайдера цін
function initPriceSlider() {
    const slider = document.getElementById('price-slider');
    const priceFrom = document.getElementById('price_from');
    const priceTo = document.getElementById('price_to');
    
    if (slider && typeof noUiSlider !== 'undefined') {
        noUiSlider.create(slider, {
            start: [Number(priceFrom.value) || 0, Number(priceTo.value) || 1000],
            connect: true,
            step: 1,
            range: {
                'min': 0,
                'max': 5000
            },
            tooltips: [true, true],
            format: {
                to: function (value) { return Math.round(value) + ' грн'; },
                from: function (value) { return Number(value.replace(' грн','')); }
            }
        });
        
        // Синхронізація з полями введення
        slider.noUiSlider.on('update', function (values, handle) {
            priceFrom.value = values[0].replace(' грн','');
            priceTo.value = values[1].replace(' грн','');
        });
    }
}

// Автозаповнення пошуку
function initSearchAutocomplete() {
    const searchInput = document.getElementById('search');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            // Тут можна додати AJAX запит для автозаповнення
            console.log('Пошуковий запит:', this.value);
        }, 300);
    });
}
```

### 5.4.2. AJAX пошук (опціонально)

```javascript
// AJAX пошук без перезавантаження сторінки
function performAjaxSearch() {
    const form = document.querySelector('form[method="GET"]');
    const formData = new FormData(form);
    
    fetch(window.location.pathname + '?' + new URLSearchParams(formData), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Оновлюємо тільки контент з товарами
        document.getElementById('products-grid').innerHTML = html;
        // Оновлюємо URL без перезавантаження
        window.history.pushState({}, '', window.location.pathname + '?' + new URLSearchParams(formData));
    });
}
```

## 5.5. Оптимізація пошуку

### 5.5.1. Індекси бази даних

```sql
-- Індекси для швидкого пошуку
CREATE INDEX idx_products_name ON products(name);
CREATE INDEX idx_products_description ON products(description);
CREATE INDEX idx_products_price ON products(price);
CREATE INDEX idx_products_category_id ON products(category_id);
CREATE INDEX idx_products_views ON products(views);
CREATE INDEX idx_products_created_at ON products(created_at);

-- Складений індекс для пошуку по назві та опису
CREATE INDEX idx_products_search ON products(name, description);
```

### 5.5.2. Кешування результатів

```php
// Кешування результатів пошуку
public function index(Request $request)
{
    $cacheKey = 'products_search_' . md5($request->fullUrl());
    
    return Cache::remember($cacheKey, 300, function() use ($request) {
        $query = Product::with('category');
        
        // Логіка фільтрації...
        
        return $query->paginate(12)->appends($request->query());
    });
}
```

## 5.6. Висновки

Система фільтрації та пошуку реалізована з повним функціоналом:

1. **Розширений пошук** - по назві, опису, по словах
2. **Фільтрація по ціні** - з інтерактивним слайдером
3. **Сортування** - за популярністю, ціною, датою
4. **Спеціальні фільтри** - для акційних товарів
5. **Адаптивний дизайн** - для всіх пристроїв
6. **Модальні вікна** - детальна інформація про товари
7. **Оптимізація** - індекси БД, кешування
8. **UX/UI** - інтуїтивний інтерфейс з підказками

Система забезпечує швидкий та зручний пошук товарів з розширеними можливостями фільтрації. 