@extends('layouts.app')
@section('content')
<div class="min-h-screen flex flex-col items-center bg-gradient-to-br from-yellow-100 via-blue-100 to-white mt-5">
    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl border-4 border-yellow-400 p-8 flex flex-col items-center">
        <div class="flex items-center gap-4 mb-4">
            <svg class="h-10 w-10 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
            <h1 class="text-3xl font-extrabold text-yellow-500">БРЕНДИ</h1>
        </div>
        <p class="text-lg text-gray-700 mb-6">Найкращі бренди спортивного харчування та добавок!</p>
        <div class="w-full flex flex-col gap-4">
            <div class="bg-yellow-100 border-l-4 border-yellow-400 p-4 rounded-lg text-yellow-800 font-bold">Наші партнери</div>
            <ul class="list-disc pl-6 text-gray-700">
                <li><b>Optimum Nutrition</b> — лідер у світі спортивного харчування</li>
                <li><b>MyProtein</b> — якісні протеїни та добавки</li>
                <li><b>BSN</b> — інноваційні формули для спортсменів</li>
                <li><b>Dymatize</b> — преміум якість та ефективність</li>
                <li><b>Universal Nutrition</b> — перевірені роками формули</li>
                <li><b>Scitec Nutrition</b> — доступні ціни та висока якість</li>
                <li><b>BioTech USA</b> — європейська якість</li>
                <li><b>Olimp</b> — фармацевтичні стандарти</li>
                <li><b>Weider</b> — легендарний бренд</li>
            </ul>
        </div>
    </div>
</div>
@endsection 