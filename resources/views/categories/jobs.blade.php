@extends('layouts.app')
@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-blue-100 via-yellow-100 to-white py-12">
    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl border-4 border-blue-400 p-8 flex flex-col items-center">
        <div class="flex items-center gap-4 mb-4">
            <svg class="h-10 w-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="#e0f2fe"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
            <h1 class="text-3xl font-extrabold text-blue-500">Робота у нас</h1>
        </div>
        <p class="text-lg text-gray-700 mb-6">Приєднуйтесь до нашої команди та станьте частиною успішного проекту!</p>
        <div class="w-full flex flex-col gap-4">
            <div class="bg-blue-100 border-l-4 border-blue-400 p-4 rounded-lg text-blue-800 font-bold">Вакансії</div>
            <p class="text-gray-700">Ми завжди шукаємо талановитих та амбітних людей. Надішліть своє резюме на info@sport-nutrition.ua</p>
        </div>
    </div>
</div>
@endsection 