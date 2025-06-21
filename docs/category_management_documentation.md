# УПРАВЛІННЯ КАТЕГОРІЯМИ

## 4.1. Контролер системи керування та адміністрування категорій

### 4.1.1. CategoryController

Основний контролер для управління категоріями продуктів:

```php
class CategoryController extends Controller
{
    /**
     * Відображає список всіх категорій з кількістю продуктів
     */
    public function index()
    {
        $categories = Category::withCount('products')->paginate(10);
        return view('categories.index', compact('categories'));
    }

    /**
     * Створення нової категорії
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string'
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Категорію створено успішно.');
    }

    /**
     * Оновлення існуючої категорії
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string'
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Категорію оновлено успішно.');
    }

    /**
     * Видалення категорії з перевіркою
     */
    public function destroy(Category $category)
    {
        // Перевіряємо, чи немає продуктів в категорії
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Неможливо видалити категорію з товарами.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Категорію видалено успішно.');
    }

    /**
     * Відображення продуктів категорії з фільтрацією
     */
    public function showBySlug($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $query = Product::where('category_id', $category->id);
        
        // Пошук по словах
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
        
        // Спеціальна фільтрація для акцій
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

### 4.1.2. Функціональність контролера

**Основні методи:**
- `index()` - список категорій з кількістю товарів
- `create()` - форма створення категорії
- `store()` - збереження нової категорії
- `edit()` - форма редагування
- `update()` - оновлення категорії
- `destroy()` - видалення з перевіркою
- `showBySlug()` - відображення товарів категорії

**Особливості:**
- Валідація унікальності назви
- Перевірка перед видаленням
- Розширена фільтрація товарів
- Пошук по словах
- Спеціальна логіка для акцій

## 4.2. Модель сутності категорій

### 4.2.1. Category Model

```php
class Category extends Model
{
    use HasFactory;

    /**
     * Поля для масового заповнення
     */
    protected $fillable = [
        'name',        // Назва категорії
        'slug',        // URL-дружній ідентифікатор
        'description'  // Опис категорії
    ];

    /**
     * Зв'язок з продуктами (один до багатьох)
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Аксесор для отримання кількості продуктів
     */
    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }

    /**
     * Мутатор для автоматичного створення slug
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }
}
```

### 4.2.2. Структура бази даних

**Таблиця `categories`:**
```sql
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### 4.2.3. Міграція

```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->timestamps();
});
```

### 4.2.4. Зв'язки з іншими моделями

```php
// В моделі Product
public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}

// В моделі Category
public function products(): HasMany
{
    return $this->hasMany(Product::class);
}
```

## 4.3. Представлення виду

### 4.3.1. Список категорій (categories/index.blade.php)

```html
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Заголовок та кнопка додавання -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold">Категорії товарів</h2>
                        <a href="{{ route('categories.create') }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Додати категорію
                        </a>
                    </div>

                    <!-- Повідомлення -->
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Таблиця категорій -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Назва
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Опис
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Кількість товарів
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Дії
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($categories as $category)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $category->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                /{{ $category->slug }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-500">
                                                {{ Str::limit($category->description, 100) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $category->products_count }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-3">
                                                <a href="{{ route('categories.edit', $category) }}" 
                                                   class="text-blue-600 hover:text-blue-900">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('Ви впевнені, що хочете видалити цю категорію?')">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Пагінація -->
                    <div class="mt-6">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

### 4.3.2. Форма створення/редагування (categories/create.blade.php)

```html
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-6">
                        {{ isset($category) ? 'Редагувати категорію' : 'Створити нову категорію' }}
                    </h2>

                    <form action="{{ isset($category) ? route('categories.update', $category) : route('categories.store') }}" 
                          method="POST" class="space-y-6">
                        @csrf
                        @if(isset($category))
                            @method('PUT')
                        @endif

                        <!-- Назва категорії -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Назва категорії
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $category->name ?? '') }}"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Опис категорії -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Опис категорії
                            </label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="4"
                                      class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('description', $category->description ?? '') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Кнопки -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('categories.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Скасувати
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ isset($category) ? 'Оновити' : 'Створити' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

### 4.3.3. Сторінка категорії з товарами (categories/aktsii.blade.php)

```html
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Заголовок категорії -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Акційні товари</h1>
        <p class="text-gray-600">Знайдіть найкращі пропозиції та знижки на спортивне харчування</p>
    </div>

    <!-- Фільтри та пошук -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form action="{{ route('categories.show', 'aktsii') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Пошук товарів..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="sort" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Популярні</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Ціна ↑</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Ціна ↓</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Нові</option>
                </select>
            </div>
            <div>
                <input type="number" name="price_from" value="{{ request('price_from') }}"
                       placeholder="Ціна від" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Пошук
                </button>
            </div>
        </form>
    </div>

    <!-- Сітка товарів -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <!-- Зображення товару -->
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
                
                <!-- Інформація про товар -->
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                    <p class="text-gray-600 text-sm mb-3">{{ Str::limit($product->description, 100) }}</p>
                    
                    <!-- Ціни -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <span class="text-lg font-bold text-green-600">{{ number_format($product->price, 2) }} ₴</span>
                            @if($product->old_price && $product->old_price > $product->price)
                                <span class="text-sm text-gray-500 line-through">{{ number_format($product->old_price, 2) }} ₴</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    -{{ number_format((($product->old_price - $product->price) / $product->old_price) * 100, 0) }}%
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Кнопки дій -->
                    <div class="flex space-x-2">
                        <button class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Додати в кошик
                        </button>
                        <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
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

## 4.4. Маршрути управління категоріями

```php
// Базові маршрути категорій
Route::resource('categories', CategoryController::class);

// Маршрут для відображення товарів категорії
Route::get('/categories/{slug}', [CategoryController::class, 'showBySlug'])->name('categories.show');

// Адмін маршрути (якщо потрібно)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('categories', CategoryController::class);
});
```

## 4.5. Валідація та безпека

### 4.5.1. Валідація категорій

```php
$validated = $request->validate([
    'name' => 'required|string|max:255|unique:categories',
    'description' => 'nullable|string|max:1000'
]);
```

### 4.5.2. Перевірка перед видаленням

```php
public function destroy(Category $category)
{
    if ($category->products()->count() > 0) {
        return redirect()->route('categories.index')
            ->with('error', 'Неможливо видалити категорію з товарами.');
    }
    
    $category->delete();
    return redirect()->route('categories.index')
        ->with('success', 'Категорію видалено успішно.');
}
```

## 4.6. Додаткові функції

### 4.6.1. Автоматичне створення slug

```php
public function setNameAttribute($value)
{
    $this->attributes['name'] = $value;
    $this->attributes['slug'] = Str::slug($value);
}
```

### 4.6.2. Статистика категорій

```php
// Кількість товарів в категорії
$category->products()->count();

// Топ категорій за кількістю товарів
Category::withCount('products')
    ->orderBy('products_count', 'desc')
    ->take(5)
    ->get();
```

## 4.7. Висновки

Система управління категоріями реалізована з повним функціоналом:

1. **CRUD операції** - створення, читання, оновлення, видалення категорій
2. **Валідація даних** - перевірка унікальності назви
3. **Безпека** - перевірка перед видаленням категорій з товарами
4. **URL-дружність** - автоматичне створення slug
5. **Фільтрація товарів** - пошук, сортування, фільтрація по ціні
6. **Спеціальна логіка** - для акційних товарів
7. **Адаптивний дизайн** - для всіх пристроїв
8. **Статистика** - кількість товарів в категоріях

Система забезпечує ефективну організацію товарів по категоріях та зручну навігацію для користувачів. 