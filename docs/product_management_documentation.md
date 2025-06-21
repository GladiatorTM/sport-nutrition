# УПРАВЛІННЯ ТОВАРАМИ

## 3.1. Контролер системи керування та адміністрування товарів

### 3.1.1. ProductController

Основний контролер для управління продуктами в публічній частині сайту:

```php
class ProductController extends Controller
{
    /**
     * Відображає список продуктів з фільтрацією та сортуванням
     */
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Фільтрація по пошуковому запиту
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
        
        // Фільтрація по ціні
        if ($request->filled('price_from')) {
            $query->where('price', '>=', floatval($request->input('price_from')));
        }
        if ($request->filled('price_to')) {
            $query->where('price', '<=', floatval($request->input('price_to')));
        }
        
        // Сортування
        $sort = $request->input('sort', 'popular');
        switch($sort) {
            case 'price_asc': $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'newest': $query->orderBy('created_at', 'desc'); break;
            case 'oldest': $query->orderBy('created_at', 'asc'); break;
            default: $query->orderBy('views', 'desc'); // popular
        }
        
        $products = $query->paginate(12)->appends($request->query());
        
        return view('products.index', [
            'products' => $products,
            'minPrice' => (clone $query)->min('price'),
            'maxPrice' => (clone $query)->max('price')
        ]);
    }

    /**
     * Створення нового продукту
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,jfif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        Product::create($validated);
        return redirect()->route('products.index')->with('success', 'Товар створено успішно');
    }

    /**
     * Оновлення продукту
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,jfif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $product->update($validated);
        return redirect()->route('products.index')->with('success', 'Товар оновлено успішно');
    }

    /**
     * Видалення продукту
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Товар видалено успішно');
    }
}
```

### 3.1.2. DashboardController (Admin)

Контролер для адміністративної панелі з розширеним функціоналом:

```php
class DashboardController extends Controller
{
    /**
     * Головна сторінка адмін-панелі зі статистикою
     */
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_users' => User::where('is_admin', false)->count(),
            'total_categories' => Category::count(),
            'total_sales' => DB::table('orders')->sum('total_amount') ?? 0,
        ];

        $monthlySales = DB::table('orders')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total_amount) as total'))
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->get();

        $topCategories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'monthlySales', 'topCategories'));
    }

    /**
     * Управління продуктами в адмін-панелі
     */
    public function products()
    {
        $products = Product::with('category')->paginate(10);
        $categories = Category::all();
        return view('admin.products.index', compact('products', 'categories'));
    }
}
```

## 3.2. Модель сутності товарів

### 3.2.1. Product Model

```php
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',        // Назва продукту
        'description', // Опис продукту
        'price',       // Поточна ціна
        'old_price',   // Стара ціна (для акцій)
        'category_id', // ID категорії
        'image',       // Шлях до зображення
        'image_path',  // Додатковий шлях
        'views',       // Кількість переглядів
    ];

    /**
     * Зв'язок з категорією
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Аксесор для автоматичного визначення зображення
     */
    public function getImagePathAttribute($value)
    {
        if ($value) {
            return '/' . ltrim($value, '/');
        }

        $name = strtolower($this->name);
        $productTypes = [
            'protein' => ['protein', 'протеїн', 'протеин'],
            'creatine' => ['creatine', 'креатин'],
            'bcaa' => ['bcaa', 'бцаа', 'бсаа'],
            'vitamins' => ['vitamin', 'вітамін', 'витамин'],
            'preworkout' => ['pre-workout', 'preworkout', 'предтрен'],
            'fatburner' => ['fat burner', 'fatburner', 'жиросжигатель']
        ];

        foreach ($productTypes as $type => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($name, $keyword)) {
                    $path = public_path("products/{$type}");
                    if (is_dir($path)) {
                        $files = glob("{$path}/*.{jpg,jpeg,png,jfif}", GLOB_BRACE);
                        if (!empty($files)) {
                            $randomFile = $files[array_rand($files)];
                            return '/products/' . $type . '/' . basename($randomFile);
                        }
                    }
                }
            }
        }

        return '/images/no-image.png';
    }
}
```

### 3.2.2. Структура бази даних

**Таблиця `products`:**
```sql
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    old_price DECIMAL(10,2) NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    image VARCHAR(255) NULL,
    image_path VARCHAR(255) NULL,
    views INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

### 3.2.3. Міграції

```php
// Створення таблиці продуктів
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description');
    $table->decimal('price', 10, 2);
    $table->decimal('old_price', 10, 2)->nullable();
    $table->foreignId('category_id')->constrained()->onDelete('cascade');
    $table->string('image')->nullable();
    $table->string('image_path')->nullable();
    $table->integer('views')->default(0);
    $table->timestamps();
});

// Додавання поля old_price
Schema::table('products', function (Blueprint $table) {
    $table->decimal('old_price', 10, 2)->nullable()->after('price');
});

// Додавання поля views
Schema::table('products', function (Blueprint $table) {
    $table->integer('views')->default(0)->after('image_path');
});
```

## 3.3. Представлення виду

### 3.3.1. Адмін-панель (admin/products/index.blade.php)

```html
@extends('admin.layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Статистика -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Всього товарів</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $products->total() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Пошук та фільтри -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('admin.products') }}" method="GET" class="flex space-x-4">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" 
                               placeholder="Пошук товарів...">
                    </div>
                    <div class="w-full sm:w-48">
                        <select name="category" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Всі категорії</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                        Пошук
                    </button>
                </form>
            </div>
        </div>

        <!-- Таблиця товарів -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Зображення</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Назва</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Категорія</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ціна</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дії</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex-shrink-0 h-16 w-16">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" 
                                             alt="{{ $product->name }}" 
                                             class="h-16 w-16 object-cover rounded-lg">
                                    @else
                                        <div class="h-16 w-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($product->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $product->category->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($product->price, 2) }} ₴
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-900">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Ви впевнені?')">
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
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
```

### 3.3.2. Форма створення/редагування (admin/products/create.blade.php)

```html
@extends('admin.layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    {{ isset($product) ? 'Редагувати товар' : 'Створити новий товар' }}
                </h3>

                <form action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}" 
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($product))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Назва товару -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Назва товару</label>
                            <input type="text" name="name" id="name" 
                                   value="{{ old('name', $product->name ?? '') }}"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Категорія -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Категорія</label>
                            <select name="category_id" id="category_id" 
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    required>
                                <option value="">Оберіть категорію</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ціна -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">Ціна (₴)</label>
                            <input type="number" name="price" id="price" step="0.01" min="0"
                                   value="{{ old('price', $product->price ?? '') }}"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   required>
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Стара ціна -->
                        <div>
                            <label for="old_price" class="block text-sm font-medium text-gray-700">Стара ціна (₴)</label>
                            <input type="number" name="old_price" id="old_price" step="0.01" min="0"
                                   value="{{ old('old_price', $product->old_price ?? '') }}"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('old_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Зображення -->
                        <div class="sm:col-span-2">
                            <label for="image" class="block text-sm font-medium text-gray-700">Зображення товару</label>
                            <input type="file" name="image" id="image" accept="image/*"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            @if(isset($product) && $product->image)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($product->image) }}" 
                                         alt="{{ $product->name }}" 
                                         class="h-32 w-32 object-cover rounded-lg">
                                </div>
                            @endif
                        </div>

                        <!-- Опис -->
                        <div class="sm:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Опис товару</label>
                            <textarea name="description" id="description" rows="4"
                                      class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                      required>{{ old('description', $product->description ?? '') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('admin.products') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Скасувати
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ isset($product) ? 'Оновити' : 'Створити' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
```

### 3.3.3. Публічна сторінка товарів (products/index.blade.php)

```html
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Фільтри та пошук -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form action="{{ route('products.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Пошук товарів..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Всі категорії</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
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
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Пошук
                </button>
            </div>
        </form>
    </div>

    <!-- Сітка товарів -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <div class="aspect-w-1 aspect-h-1 w-full">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-48 object-cover">
                    @elseif($product->image_path)
                        <img src="{{ asset($product->image_path) }}" 
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
                
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                    <p class="text-gray-600 text-sm mb-3">{{ Str::limit($product->description, 100) }}</p>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            @if($product->old_price && $product->old_price > $product->price)
                                <span class="text-lg font-bold text-green-600">{{ number_format($product->price, 2) }} ₴</span>
                                <span class="text-sm text-gray-500 line-through">{{ number_format($product->old_price, 2) }} ₴</span>
                            @else
                                <span class="text-lg font-bold text-gray-900">{{ number_format($product->price, 2) }} ₴</span>
                            @endif
                        </div>
                        
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $product->category->name }}
                        </span>
                    </div>
                    
                    <div class="mt-4 flex space-x-2">
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
        @endforeach
    </div>

    <!-- Пагінація -->
    <div class="mt-8">
        {{ $products->links() }}
    </div>
</div>
@endsection
```

## 3.4. Маршрути управління товарами

```php
// Публічні маршрути
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

// Адмін маршрути
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/products', [DashboardController::class, 'products'])->name('products');
    Route::get('/products/create', [DashboardController::class, 'create'])->name('products.create');
    Route::post('/products', [DashboardController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [DashboardController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [DashboardController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [DashboardController::class, 'destroy'])->name('products.destroy');
});
```

## 3.5. Валідація та безпека

### 3.5.1. Валідація продуктів

```php
$validated = $request->validate([
    'name' => 'required|string|max:255',
    'description' => 'required|string|min:10',
    'price' => 'required|numeric|min:0|max:999999.99',
    'old_price' => 'nullable|numeric|min:0|max:999999.99|gt:price',
    'category_id' => 'required|exists:categories,id',
    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,jfif|max:2048'
]);
```

### 3.5.2. Обробка зображень

```php
if ($request->hasFile('image')) {
    // Видаляємо старе зображення
    if ($product->image) {
        Storage::disk('public')->delete($product->image);
    }
    
    // Завантажуємо нове зображення
    $path = $request->file('image')->store('products', 'public');
    $validated['image'] = $path;
}
```

## 3.6. Висновки

Система управління товарами реалізована з повним функціоналом:

1. **CRUD операції** - створення, читання, оновлення, видалення товарів
2. **Фільтрація та пошук** - по назві, категорії, ціні
3. **Сортування** - за популярністю, ціною, датою
4. **Управління зображеннями** - завантаження, видалення, автоматичне призначення
5. **Адаптивний дизайн** - для адмін-панелі та публічної частини
6. **Валідація даних** - перевірка вхідних даних
7. **Безпека** - захист від несанкціонованого доступу

Система забезпечує ефективне управління товарами як для адміністраторів, так і для користувачів. 