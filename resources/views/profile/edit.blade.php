@extends('layouts.app')
@section('content')
<div class="py-12 min-h-screen" style="background: linear-gradient(135deg, #fffbe6 0%, #e0f2fe 100%);">
    <div class="max-w-4xl mx-auto flex flex-col md:flex-row gap-8 items-start">
        <!-- Ліва колонка: Аватар і дані -->
        <div class="w-full md:w-1/3 flex flex-col items-center bg-white rounded-2xl border-4 border-yellow-400 shadow-xl p-6 justify-center">
            <div class="flex flex-col items-center w-full">
                <div class="relative mb-4 flex flex-col items-center">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('avatars/' . Auth::user()->avatar) }}" alt="Аватар" class="w-32 h-32 rounded-full object-cover border-4 border-blue-400 mx-auto">
                    @else
                        <div class="w-32 h-32 rounded-full bg-yellow-200 flex items-center justify-center text-5xl text-yellow-600 font-bold border-4 border-blue-400 mx-auto">
                            {{ strtoupper(mb_substr(Auth::user()->name,0,1)) }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="flex flex-col items-center gap-2 mt-2">
                        @csrf
                        <label class="flex flex-col items-center cursor-pointer">
                            <span class="bg-yellow-400 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded-lg transition mb-2">Завантажити фото</span>
                            <input type="file" name="avatar" accept="image/*" class="hidden" onchange="this.form.submit()">
                        </label>
                    </form>
                </div>
                <div class="text-center">
                    <div class="text-xl font-bold text-gray-900">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-gray-500 mb-2">{{ Auth::user()->email }}</div>
                </div>
            </div>
        </div>
        <!-- Права колонка: Інформація профілю -->
        <div class="flex-1 flex flex-col gap-8 w-full">
            <div class="w-full bg-white border-4 border-yellow-400 rounded-2xl shadow-xl flex flex-col items-center p-8">
                <h3 class="text-xl font-bold text-yellow-500 mb-2">👤 Інформація профілю</h3>
                <div class="w-full">
                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                        @csrf
                        @method('patch')
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Ім'я</label>
                            <input id="name" name="name" type="text" value="{{ old('name', Auth::user()->name) }}" required autofocus autocomplete="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-400 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', Auth::user()->email) }}" required autocomplete="username" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-400 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                        </div>
                        <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 px-6 rounded-lg transition">Зберегти</button>
                    </form>
                </div>
            </div>
            <div class="w-full bg-white border-4 border-blue-500 rounded-2xl shadow-xl flex flex-col items-center p-8">
                <h3 class="text-xl font-bold text-blue-500 mb-2">🔒 Оновлення пароля</h3>
                <div class="w-full">
                    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                        @csrf
                        @method('put')
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">Поточний пароль</label>
                            <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Новий пароль</label>
                            <input id="password" name="password" type="password" required autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Підтвердіть новий пароль</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg transition">Зберегти</button>
                    </form>
                </div>
            </div>
            <div class="w-full bg-white border-4 border-yellow-400 rounded-2xl shadow-xl flex flex-col items-center p-8">
                <h3 class="text-xl font-bold text-yellow-500 mb-2">❌ Видалення акаунту</h3>
                <div class="w-full">
                    <form id="delete-account-form" method="POST" action="{{ route('profile.destroy') }}" class="space-y-6">
                        @csrf
                        @method('delete')
                        <p class="text-gray-700 mb-2">Після видалення акаунту всі ваші дані буде безповоротно видалено. Перед цим збережіть важливу інформацію.</p>
                        <button type="button" onclick="openDeleteModal()" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-lg transition">Видалити акаунт</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Модальне вікно підтвердження видалення акаунту -->
<div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full border-4 border-yellow-400 flex flex-col items-center">
        <div class="flex flex-col items-center mb-4">
            <svg class="h-16 w-16 text-yellow-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="4" fill="#fffbe6"/>
                <path d="M24 16v10" stroke="red" stroke-width="4" stroke-linecap="round"/>
                <circle cx="24" cy="32" r="2" fill="red"/>
            </svg>
            <h4 class="text-xl font-bold text-yellow-600 mb-2">Видалення акаунту</h4>
            <p class="text-gray-700 text-center">Ви впевнені, що хочете <span class="text-red-500 font-bold">видалити акаунт</span>?<br>Цю дію не можна скасувати!</p>
        </div>
        <div class="flex gap-4 mt-4">
            <button onclick="closeDeleteModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition">Скасувати</button>
            <button onclick="confirmDeleteAccount()" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition">Видалити акаунт</button>
        </div>
    </div>
</div>
<script>
    function openDeleteModal() {
        document.getElementById('delete-modal').classList.remove('hidden');
    }
    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
    }
    function confirmDeleteAccount() {
        document.getElementById('delete-account-form').submit();
    }
</script>
@endsection
