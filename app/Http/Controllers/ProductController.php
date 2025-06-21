<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Контролер для управління продуктами
 * 
 * Цей контролер обробляє всі операції з продуктами:
 * - Відображення списку продуктів з фільтрацією та сортуванням
 * - Створення нових продуктів
 * - Редагування існуючих продуктів
 * - Видалення продуктів
 * - Завантаження та управління зображеннями продуктів
 */
class ProductController extends Controller
{
    /**
     * Відображає список продуктів з фільтрацією та сортуванням
     * 
     * Цей метод обробляє GET запит до /products і повертає відфільтрований
     * та відсортований список продуктів. Підтримує:
     * - Пошук по назві та опису продукту
     * - Фільтрацію по категорії
     * - Фільтрацію по діапазону цін
     * - Сортування (популярність, ціна, дата створення)
     * - Пагінацію (12 продуктів на сторінку)
     * 
     * @param Request $request HTTP запит з параметрами фільтрації
     * @return \Illuminate\View\View Сторінка зі списком продуктів
     */
    public function index(Request $request)
    {
        // Створюємо базовий запит з завантаженням зв'язку з категорією
        $query = Product::with('category');
        
        // Фільтрація по пошуковому запиту
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%") ;
            });
        }
        
        // Фільтрація по категорії
        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }
        
        // Фільтрація по мінімальній ціні
        if ($request->filled('price_from')) {
            $query->where('price', '>=', floatval($request->input('price_from')));
        }
        
        // Фільтрація по максимальній ціні
        if ($request->filled('price_to')) {
            $query->where('price', '<=', floatval($request->input('price_to')));
        }
        
        // Сортування продуктів
        $sort = $request->input('sort', 'popular');
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'newest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else { // popular - сортування по популярності (кількості переглядів)
            $query->orderBy('views', 'desc');
        }
        
        // Отримуємо відфільтровані продукти з пагінацією
        $products = $query->paginate(12)->appends($request->query());
        
        // Розраховуємо мінімальну та максимальну ціну для відфільтрованих продуктів
        $filteredMinPrice = (clone $query)->min('price');
        $filteredMaxPrice = (clone $query)->max('price');
        
        return view('products.index', [
            'products' => $products,
            'minPrice' => $filteredMinPrice,
            'maxPrice' => $filteredMaxPrice
        ]);
    }

    /**
     * Відображає форму створення нового продукту
     * 
     * Цей метод повертає view з формою для створення нового продукту.
     * Форма містить поля для назви, опису, ціни, категорії та зображення.
     * 
     * @return \Illuminate\View\View Форма створення продукту
     */
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Зберігає новий продукт в базі даних
     * 
     * Цей метод обробляє POST запит для створення нового продукту.
     * Валідує вхідні дані, завантажує зображення (якщо надано)
     * та зберігає продукт в базі даних.
     * 
     * @param Request $request HTTP запит з даними продукту
     * @return \Illuminate\Http\RedirectResponse Перенаправлення на список продуктів
     */
    public function store(Request $request)
    {
        // Валідація вхідних даних
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Завантаження зображення, якщо воно надано
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        // Створення нового продукту
        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Відображає форму редагування продукту
     * 
     * Цей метод повертає view з формою для редагування існуючого продукту.
     * Форма заповнена поточними даними продукту.
     * 
     * @param Product $product Продукт для редагування (Route Model Binding)
     * @return \Illuminate\View\View Форма редагування продукту
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Оновлює існуючий продукт в базі даних
     * 
     * Цей метод обробляє PUT/PATCH запит для оновлення продукту.
     * Валідує вхідні дані, замінює зображення (якщо надано нове)
     * та оновлює продукт в базі даних.
     * 
     * @param Request $request HTTP запит з даними для оновлення
     * @param Product $product Продукт для оновлення (Route Model Binding)
     * @return \Illuminate\Http\RedirectResponse Перенаправлення на список продуктів
     */
    public function update(Request $request, Product $product)
    {
        // Валідація вхідних даних
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Обробка нового зображення, якщо воно надано
        if ($request->hasFile('image')) {
            // Видаляємо старе зображення з диску
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            // Завантажуємо нове зображення
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        // Оновлення продукту
        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Видаляє продукт з бази даних
     * 
     * Цей метод обробляє DELETE запит для видалення продукту.
     * Видаляє зображення продукту з диску (якщо воно існує)
     * та видаляє запис з бази даних.
     * 
     * @param Product $product Продукт для видалення (Route Model Binding)
     * @return \Illuminate\Http\RedirectResponse Перенаправлення на список продуктів
     */
    public function destroy(Product $product)
    {
        // Видаляємо зображення з диску, якщо воно існує
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        // Видаляємо продукт з бази даних
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
} 