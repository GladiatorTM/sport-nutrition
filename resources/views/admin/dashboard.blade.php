@extends('admin.layouts.app')

@section('content')
<!-- Головна сторінка адміністративної панелі -->
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Заголовок сторінки -->
                <h1 class="text-2xl font-semibold mb-6">Панель керування</h1>
                
                <!-- Секція статистики - картки з основними показниками -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <!-- Картка: Загальна кількість товарів -->
                    <div class="bg-blue-100 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-800">Товари</h3>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['total_products'] ?? 0 }}</p>
                    </div>
                    
                    <!-- Картка: Загальна кількість користувачів (не адміністраторів) -->
                    <div class="bg-green-100 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-800">Користувачі</h3>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['total_users'] ?? 0 }}</p>
                    </div>
                    
                    <!-- Картка: Загальна кількість категорій -->
                    <div class="bg-purple-100 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-800">Категорії</h3>
                        <p class="text-2xl font-bold text-purple-600">{{ $stats['total_categories'] ?? 0 }}</p>
                    </div>
                    
                    <!-- Картка: Загальна сума продажів -->
                    <div class="bg-yellow-100 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-yellow-800">Продажі</h3>
                        <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['total_sales'] ?? 0, 2) }} ₴</p>
                    </div>
                </div>

                <!-- Секція: Останні зареєстровані користувачі -->
                @if(isset($recentUsers) && $recentUsers->count() > 0)
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Останні користувачі</h2>
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <!-- Таблиця з останніми користувачами -->
                        <table class="min-w-full divide-y divide-gray-200">
                            <!-- Заголовки таблиці -->
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ім'я</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата реєстрації</th>
                                </tr>
                            </thead>
                            <!-- Тіло таблиці з даними користувачів -->
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentUsers as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Секція: Топ категорій за кількістю товарів -->
                @if(isset($topCategories) && $topCategories->count() > 0)
                <div>
                    <h2 class="text-xl font-semibold mb-4">Топ категорій</h2>
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <!-- Таблиця з топ категоріями -->
                        <table class="min-w-full divide-y divide-gray-200">
                            <!-- Заголовки таблиці -->
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Категорія</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Кількість товарів</th>
                                </tr>
                            </thead>
                            <!-- Тіло таблиці з даними категорій -->
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($topCategories as $category)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $category->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $category->products_count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 