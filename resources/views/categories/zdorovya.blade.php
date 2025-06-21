@extends('layouts.app')
@section('content')
<div class="min-h-screen flex flex-col items-center bg-gradient-to-br from-yellow-100 via-blue-100 to-white mt-5">
    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl border-4 border-yellow-400 p-8 flex flex-col items-center">
        <div class="flex items-center gap-4 mb-4">
            <svg class="h-10 w-10 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="#fffbe6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            <h1 class="text-3xl font-extrabold text-yellow-500">ЗДОРОВ'Я</h1>
        </div>
        <p class="text-lg text-gray-700 mb-6">Все для вашого здоров'я та гарного самопочуття!</p>
        <div class="w-full flex flex-col gap-4">
            <div class="bg-yellow-100 border-l-4 border-yellow-400 p-4 rounded-lg text-yellow-800 font-bold">Рекомендовані товари для здоров'я</div>
            <ul class="list-disc pl-6 text-gray-700">
                <li><b>Вітамін D3</b> — для імунітету та кісток</li>
                <li><b>Омега-3</b> — для мозку, серця та судин</li>
                <li><b>Вітамін B-комплекс</b> — для нервової системи та енергії</li>
                <li><b>Біотин</b> — для волосся, шкіри та нігтів</li>
                <li><b>Магній</b> — для зняття стресу та сну</li>
                <li><b>Цинк</b> — для імунітету та гормонального балансу</li>
                <li><b>Вітамін C</b> — для захисту від застуд</li>
                <li><b>Колаген</b> — для суглобів, шкіри та зв'язок</li>
                <li><b>Мелатонін</b> — для сну та відновлення</li>
            </ul>
        </div>
    </div>
</div>
@endsection 