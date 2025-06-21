@extends('admin.layouts.app')

@section('header', 'Редагування товару')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Редагування товару</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Відредагуйте інформацію про товар. Всі поля обов'язкові для заповнення.
                </p>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        <!-- Поточна картинка -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Поточне зображення</label>
                            <div class="flex items-center space-x-4">
                                @if($product->image)
                                    <div class="relative">
                                        <img src="{{ Storage::url($product->image) }}" 
                                             alt="{{ $product->name }}" 
                                             class="h-32 w-32 object-cover rounded-lg shadow-sm">
                                    </div>
                                @elseif($product->image_path)
                                    <div class="relative">
                                        <img src="{{ asset($product->image_path) }}" 
                                             alt="{{ $product->name }}" 
                                             class="h-32 w-32 object-cover rounded-lg shadow-sm">
                                    </div>
                                @else
                                    <div class="h-32 w-32 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Назва товару -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Назва товару</label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $product->name) }}"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Опис -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Опис</label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="3" 
                                      class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ціна -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">Ціна</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" 
                                       name="price" 
                                       id="price" 
                                       step="0.01"
                                       value="{{ old('price', $product->price) }}"
                                       class="focus:ring-blue-500 focus:border-blue-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₴</span>
                                </div>
                            </div>
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Категорія -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Категорія</label>
                            <select id="category_id" 
                                    name="category_id" 
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Зображення -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Зображення товару</label>
                            <div class="mt-1 flex items-center">
                                <input type="file" 
                                       name="image" 
                                       id="image" 
                                       accept="image/jpeg,image/png,image/jpg,image/gif,image/jfif"
                                       class="focus:ring-blue-500 focus:border-blue-500 block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700
                                              hover:file:bg-blue-100">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                Формати: JPEG, PNG, JPG, GIF, JFIF. Максимальний розмір: 2MB
                            </p>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <a href="{{ route('admin.products') }}" 
                           class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2">
                            Скасувати
                        </a>
                        <button type="submit" 
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Зберегти зміни
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 