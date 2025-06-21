<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Зміна паролю | GYMZONE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border-4 border-yellow-400 flex flex-col items-center">
        <div class="flex items-center mb-6">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('images/logo_clean.png') }}" alt="Логотип" class="h-12 w-auto mr-2">
            </a>
        </div>
        <h2 class="text-2xl font-bold mb-4">Зміна паролю</h2>
        <form method="POST" action="{{ route('password.store') }}" class="flex flex-col gap-4 w-full">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <input type="email" name="email" placeholder="Email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" class="rounded-lg border-2 border-gray-200 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-200 text-lg">
            @error('email')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            <input type="password" name="password" placeholder="Новий пароль" required autocomplete="new-password" class="rounded-lg border-2 border-gray-200 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-200 text-lg">
            @error('password')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            <input type="password" name="password_confirmation" placeholder="Підтвердіть новий пароль" required autocomplete="new-password" class="rounded-lg border-2 border-gray-200 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-200 text-lg">
            @error('password_confirmation')
                <span class="text-red-500 text-sm">Паролі не співпадають.</span>
            @enderror
            <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 rounded-lg text-lg transition">Змінити пароль</button>
        </form>
        <div class="mt-4 text-sm">
            Згадали пароль?
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-bold">Увійти</a>
        </div>
    </div>
</body>
</html>
