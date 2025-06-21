<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * Контролер для управління категоріями продуктів
 * 
 * Цей контролер обробляє всі операції з категоріями:
 * - Відображення списку категорій
 * - Створення нових категорій
 * - Редагування існуючих категорій
 * - Видалення категорій
 * - Відображення продуктів по категоріях з фільтрацією
 */
class CategoryController extends Controller
{
    /**
     * Відображає список всіх категорій з кількістю продуктів
     * 
     * Цей метод повертає пагінований список категорій з підрахунком
     * кількості продуктів в кожній категорії.
     * 
     * @return \Illuminate\View\View Сторінка зі списком категорій
     */
    public function index()
    {
        $categories = Category::withCount('products')->paginate(10);
        return view('categories.index', compact('categories'));
    }

    /**
     * Відображає форму створення нової категорії
     * 
     * @return \Illuminate\View\View Форма створення категорії
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Зберігає нову категорію в базі даних
     * 
     * Валідує вхідні дані та створює нову категорію.
     * Назва категорії повинна бути унікальною.
     * 
     * @param Request $request HTTP запит з даними категорії
     * @return \Illuminate\Http\RedirectResponse Перенаправлення на список категорій
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string'
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Відображає форму редагування категорії
     * 
     * @param Category $category Категорія для редагування (Route Model Binding)
     * @return \Illuminate\View\View Форма редагування категорії
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Оновлює існуючу категорію в базі даних
     * 
     * Валідує вхідні дані та оновлює категорію.
     * Назва категорії повинна бути унікальною (крім поточної категорії).
     * 
     * @param Request $request HTTP запит з даними для оновлення
     * @param Category $category Категорія для оновлення (Route Model Binding)
     * @return \Illuminate\Http\RedirectResponse Перенаправлення на список категорій
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string'
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Видаляє категорію з бази даних
     * 
     * Перевіряє, чи немає продуктів в категорії перед видаленням.
     * Якщо в категорії є продукти, видалення блокується.
     * 
     * @param Category $category Категорія для видалення (Route Model Binding)
     * @return \Illuminate\Http\RedirectResponse Перенаправлення на список категорій
     */
    public function destroy(Category $category)
    {
        // Перевіряємо, чи немає продуктів в категорії
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Cannot delete category with associated products.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Відображає продукти конкретної категорії з фільтрацією та сортуванням
     * 
     * Цей метод обробляє відображення продуктів по slug категорії.
     * Підтримує:
     * - Пошук по назві та опису продукту (розбиває по словах)
     * - Фільтрацію по діапазону цін
     * - Спеціальну фільтрацію для акційних товарів (категорія 'aktsii')
     * - Сортування (популярність, ціна, дата створення)
     * - Пагінацію (12 продуктів на сторінку)
     * 
     * @param string $slug Slug категорії для відображення
     * @return \Illuminate\View\View Сторінка з продуктами категорії
     */
    public function showBySlug($slug)
    {
        // Знаходимо категорію по slug
        $category = \App\Models\Category::where('slug', $slug)->firstOrFail();
        $query = Product::where('category_id', $category->id);
        
        // Фільтр по назві та опису (розбиває пошуковий запит на слова)
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
        
        // Фільтр по мінімальній ціні
        if (request('price_from')) {
            $query->where('price', '>=', floatval(request('price_from')));
        }
        
        // Фільтр по максимальній ціні
        if (request('price_to')) {
            $query->where('price', '<=', floatval(request('price_to')));
        }
        
        // Спеціальна фільтрація для акційних товарів
        if ($slug === 'aktsii') {
            $query->whereNotNull('old_price')->where('old_price', '>', 0);
        }
        
        // Сортування продуктів
        $sort = request('sort', 'popular');
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
        $products = $query->paginate(12)->appends(request()->query());
        return view('categories.' . $slug, compact('products', 'category'));
    }
} 