<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вхід | GYMZONE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border-4 border-yellow-400 flex flex-col items-center">
        <div class="flex items-center mb-6">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('images/logo_clean.png') }}" alt="Логотип" class="h-12 w-auto mr-2">
            </a>
        </div>
        <h2 class="text-2xl font-bold mb-4">Вхід до акаунту</h2>
        @if(session('error'))
            <div class="w-full bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-center font-bold">
                {{ session('error') }}
            </div>
        @endif
        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4 w-full">
            @csrf
            <input type="email" name="email" placeholder="Email" required autofocus class="rounded-lg border-2 border-gray-200 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-200 text-lg">
            @error('email')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            <input type="password" name="password" placeholder="Пароль" required class="rounded-lg border-2 border-gray-200 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-200 text-lg">
            @error('password')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            <div class="flex items-center justify-between w-full">
                <label class="flex items-center text-sm">
                    <input type="checkbox" name="remember" class="mr-2 rounded border-gray-300 text-yellow-500 focus:ring-yellow-400">
                    Запам'ятати мене
                </label>
                <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:underline ml-2">Забули пароль?</a>
            </div>
            <button type="submit" class="flex items-center justify-center gap-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold py-2 rounded-lg transition">Увійти</button>
        </form>
        <div class="w-full flex flex-col gap-2 mt-4">
            <a href="{{ route('google.login') }}" class="flex items-center justify-center gap-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold py-2 rounded-lg transition">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="h-6 w-6"> Увійти через Google
            </a>
        </div>
        <div class="mt-4 text-sm">
            Немає акаунту?
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline font-bold">Зареєструватися</a>
        </div>
    </div>
</body>
</html>
