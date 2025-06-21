@extends('layouts.app')
@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-yellow-100 via-blue-100 to-white py-12">
    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl border-4 border-yellow-400 p-8 flex flex-col items-center">
        <div class="flex items-center gap-4 mb-4">
            <svg class="h-10 w-10 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="#fffbe6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
            <h1 class="text-3xl font-extrabold text-yellow-500">Доставка і оплата</h1>
        </div>
        <p class="text-lg text-gray-700 mb-6">Швидка доставка по всій Україні та зручні способи оплати!</p>
        <div class="w-full flex flex-col gap-4">
            <div class="bg-yellow-100 border-l-4 border-yellow-400 p-4 rounded-lg text-yellow-800 font-bold">Доставка</div>
            <p class="text-gray-700">Доставка здійснюється по всій Україні. Термін доставки - 1-3 дні.</p>
            <div class="bg-blue-100 border-l-4 border-blue-400 p-4 rounded-lg text-blue-800 font-bold">Оплата</div>
            <p class="text-gray-700">Оплата можлива готівкою при отриманні, або онлайн через банківську карту.</p>
        </div>
    </div>
</div>
@endsection 