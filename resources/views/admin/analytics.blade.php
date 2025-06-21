@extends('admin.layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-semibold mb-6">Аналітика</h1>

                <!-- Статистика по категоріях -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Статистика по категоріях</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Категорія</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Загальна вартість товарів</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Кількість товарів</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($categoryStats as $stat)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stat->category_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($stat->total_price, 2) }} ₴</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stat->products_count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Загальна статистика -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-800 mb-2">Загальна кількість товарів</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $totalProducts }}</p>
                    </div>
                    <div class="bg-green-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-800 mb-2">Загальна вартість товарів</h3>
                        <p class="text-3xl font-bold text-green-600">{{ number_format($totalValue, 2) }} ₴</p>
                    </div>
                    <div class="bg-purple-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-800 mb-2">Кількість категорій</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ $totalCategories }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 