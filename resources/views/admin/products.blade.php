@extends('layouts.app')

@section('content')
<div class="p-8">
    <h1 class="text-2xl font-bold mb-4">Товари</h1>
    <a href="{{ route('admin.products.create') }}" class="inline-block mb-4 bg-yellow-400 hover:bg-yellow-500 text-white font-bold py-2 px-4 rounded">+ Додати продукт</a>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow">
            <thead>
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Назва</th>
                    <th class="px-4 py-2">Ціна</th>
                    <th class="px-4 py-2">Категорія</th>
                    <th class="px-4 py-2">Картинка</th>
                    <th class="px-4 py-2">Дії</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td class="px-4 py-2">{{ $product->id }}</td>
                    <td class="px-4 py-2">{{ $product->name }}</td>
                    <td class="px-4 py-2">{{ $product->price }} грн</td>
                    <td class="px-4 py-2">{{ $product->category }}</td>
                    <td class="px-4 py-2"><img src="{{ asset($product->image) }}" class="h-10 w-10 object-cover rounded" alt="{{ $product->name }}"></td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-500">Редагувати</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection 