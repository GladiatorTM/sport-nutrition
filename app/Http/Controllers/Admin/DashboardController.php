<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Контролер для адміністративної панелі
 * 
 * Цей контролер обробляє всі операції адміністративної панелі:
 * - Відображення дашборду зі статистикою
 * - Управління продуктами (CRUD операції)
 * - Управління користувачами
 * - Аналітика та звіти
 * - Статистика продажів та відвідуваності
 */
class DashboardController extends Controller
{
    /**
     * Відображає головну сторінку адміністративної панелі зі статистикою
     * 
     * Цей метод збирає та відображає різноманітну статистику:
     * - Загальна кількість продуктів, користувачів, категорій
     * - Статистика продажів по місяцях
     * - Топ категорій за кількістю продуктів
     * - Останні зареєстровані користувачі
     * - Статистика відвідуваності за останні 30 днів
     * 
     * @return \Illuminate\View\View Сторінка адміністративного дашборду
     */
    public function index()
    {
        // Загальна статистика по системі
        $stats = [
            'total_products' => Product::count(),
            'total_users' => User::where('is_admin', false)->count(),
            'total_categories' => Category::count(),
            'total_sales' => DB::table('orders')->sum('total_amount') ?? 0,
        ];

        // Статистика продажів по місяцях поточного року
        $monthlySales = DB::table('orders')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total_amount) as total'))
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->get();

        // Топ-5 категорій за кількістю продуктів
        $topCategories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(5)
            ->get();

        // Останні 5 зареєстрованих користувачів (не адміністраторів)
        $recentUsers = User::where('is_admin', false)
            ->latest()
            ->take(5)
            ->get();

        // Статистика відвідуваності за останні 30 днів
        $pageViews = DB::table('page_views')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as views'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'monthlySales',
            'topCategories',
            'recentUsers',
            'pageViews'
        ));
    }

    /**
     * Відображає список всіх продуктів для адміністратора
     * 
     * @return \Illuminate\View\View Сторінка зі списком продуктів
     */
    public function products()
    {
        $products = Product::with('category')->paginate(10);
        $categories = Category::all();
        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Відображає форму створення нового продукту
     * 
     * @return \Illuminate\View\View Форма створення продукту
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Зберігає новий продукт в базі даних
     * 
     * Валідує вхідні дані, завантажує зображення (якщо надано)
     * та створює новий продукт в базі даних.
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,jfif|max:2048'
        ]);

        // Завантаження зображення, якщо воно надано
        if ($request->hasFile('image')) {
            // Зберігаємо зображення в public/storage/products
            $path = $request->file('image')->store('products', 'public');
            // Зберігаємо відносний шлях до зображення
            $validated['image'] = $path;
            $validated['image_path'] = $path;
        }

        Product::create($validated);

        return redirect()->route('admin.products')->with('success', 'Товар успішно створено');
    }

    /**
     * Відображає форму редагування продукту
     * 
     * @param Product $product Продукт для редагування (Route Model Binding)
     * @return \Illuminate\View\View Форма редагування продукту
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Оновлює існуючий продукт в базі даних
     * 
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,jfif|max:2048'
        ]);

        // Обробка нового зображення, якщо воно надано
        if ($request->hasFile('image')) {
            // Видаляємо старе зображення з диску
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            // Зберігаємо нове зображення
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
            $validated['image_path'] = $path;
        }

        $product->update($validated);

        return redirect()->route('admin.products')->with('success', 'Товар успішно оновлено');
    }

    /**
     * Видаляє продукт з бази даних
     * 
     * @param Product $product Продукт для видалення (Route Model Binding)
     * @return \Illuminate\Http\RedirectResponse Перенаправлення на список продуктів
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Товар успішно видалено');
    }

    /**
     * Відображає список всіх користувачів для адміністратора
     * 
     * @return \Illuminate\View\View Сторінка зі списком користувачів
     */
    public function users()
    {
        $users = User::paginate(10);
        return view('admin.users', compact('users'));
    }

    /**
     * Перемикає статус адміністратора для користувача
     * 
     * Якщо користувач є адміністратором - позбавляє прав,
     * якщо не є - надає права адміністратора.
     * 
     * @param User $user Користувач для зміни статусу (Route Model Binding)
     * @return \Illuminate\Http\RedirectResponse Перенаправлення на список користувачів
     */
    public function toggleAdmin(User $user)
    {
        $user->update(['is_admin' => !$user->is_admin]);
        
        $message = $user->is_admin 
            ? 'Користувача призначено адміністратором' 
            : 'Користувача позбавлено прав адміністратора';
            
        return redirect()->route('admin.users')->with('success', $message);
    }

    /**
     * Видаляє користувача з бази даних
     * 
     * Перевіряє, чи не є користувач адміністратором перед видаленням.
     * Адміністраторів видаляти заборонено.
     * 
     * @param User $user Користувач для видалення (Route Model Binding)
     * @return \Illuminate\Http\RedirectResponse Перенаправлення на список користувачів
     */
    public function destroyUser(User $user)
    {
        // Перевіряємо, чи не є користувач адміністратором
        if ($user->is_admin) {
            return redirect()->route('admin.users')
                ->with('error', 'Неможливо видалити адміністратора');
        }

        $user->delete();
        return redirect()->route('admin.users')
            ->with('success', 'Користувача видалено');
    }

    /**
     * Відображає сторінку аналітики з детальною статистикою
     * 
     * Збирає та відображає:
     * - Статистику по категоріях (кількість продуктів, загальна вартість)
     * - Загальну кількість продуктів, категорій та їх вартість
     * 
     * @return \Illuminate\View\View Сторінка аналітики
     */
    public function analytics()
    {
        // Статистика по категоріях з підрахунком продуктів та їх вартості
        $categoryStats = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name as category_name', 
                    DB::raw('COUNT(products.id) as products_count'),
                    DB::raw('SUM(products.price) as total_price'))
            ->groupBy('categories.id', 'categories.name')
            ->get();

        // Загальна статистика по системі
        $totalProducts = DB::table('products')->count();
        $totalValue = DB::table('products')->sum('price');
        $totalCategories = DB::table('categories')->count();

        return view('admin.analytics', compact(
            'categoryStats',
            'totalProducts',
            'totalValue',
            'totalCategories'
        ));
    }
} 