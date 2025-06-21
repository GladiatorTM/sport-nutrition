@extends('layouts.app')
@section('content')
<div class="min-h-screen py-12 bg-gradient-to-br from-yellow-100 via-blue-100 to-white flex flex-col items-center">
    <div class="max-w-6xl w-full">
        <h1 class="text-4xl font-extrabold text-yellow-500 mb-8 text-center drop-shadow">АКЦІЇ</h1>
        @include('components.product-filter')
        <div class="flex flex-wrap gap-2 justify-center mb-8">
            <a href="{{ route('categories.aktsii') }}" class="px-4 py-1 rounded-full font-bold text-xs transition {{ request('category') ? 'bg-yellow-100 text-yellow-600 border border-yellow-400 hover:bg-yellow-200' : 'bg-yellow-400 text-white shadow' }}">Всі</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @foreach($products as $product)
                <div class="rounded-2xl border-2 border-yellow-300 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col overflow-hidden cursor-pointer bg-gradient-to-br from-yellow-100 via-blue-100 to-white hover:from-yellow-200 hover:via-blue-200" onclick="openModal({{ $product->id }})">
                    @if($product->image_path)
                        <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover object-center bg-gray-100">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-yellow-100 via-blue-100 to-white flex items-center justify-center">
                            <span class="text-4xl text-yellow-400 font-bold">?</span>
                        </div>
                    @endif
                    <div class="p-5 flex flex-col flex-1">
                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $product->name }}</h3>
                        <div class="text-sm text-gray-500 mb-2">{{ Str::limit($product->description, 60) }}</div>
                        @if($product->old_price)
                            <div class="mb-1 flex items-baseline gap-2">
                                <span class="text-lg font-extrabold text-yellow-500">{{ number_format($product->price, 2) }} грн</span>
                                <span class="text-gray-400 line-through text-base">{{ number_format($product->old_price, 2) }} грн</span>
                            </div>
                        @else
                            <div class="text-lg font-extrabold text-yellow-500 mb-3">{{ number_format($product->price, 2) }} грн</div>
                        @endif
                        @php $isAuth = auth()->check(); @endphp
                        <button onclick="event.stopPropagation(); @if($isAuth) addToCart({id: {{ $product->id }}, name: '{{ addslashes($product->name) }}', price: {{ $product->price }}, image_path: '{{ $product->image_path }}'}) @else showAuthModal({{ $product->id }}) @endif" class="mt-auto bg-gradient-to-r from-yellow-400 to-blue-400 hover:from-blue-500 hover:to-yellow-500 text-white font-bold py-2 px-4 rounded-lg shadow transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A2 2 0 008.48 18h7.04a2 2 0 001.83-2.3L17 13M7 13V6h13" /></svg>
                            Додати в кошик
                        </button>
                    </div>
                </div>
                <x-product-modal :product="$product" />
            @endforeach
        </div>
        <div class="mt-8">
            {{ $products->appends(request()->except('page'))->links('pagination::tailwind', ['color' => 'yellow']) }}
        </div>
    </div>
</div>
@include('components.gym-auth-modal')
<script>
function showAuthModal(productId) {
    window.localStorage.setItem('pending_product_id', productId);
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'gym-auth' }));
}
</script>
@endsection 