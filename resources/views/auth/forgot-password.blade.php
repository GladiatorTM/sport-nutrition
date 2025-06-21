<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Відновлення паролю | GYMZONE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border-4 border-yellow-400 flex flex-col items-center">
        <div class="flex items-center mb-6">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('images/logo_clean.png') }}" alt="Логотип" class="h-12 w-auto mr-2">
            </a>
        </div>
        <h2 class="text-2xl font-bold mb-4">Відновлення паролю</h2>
        <div class="mb-4 text-sm text-gray-600 text-center">
            Вкажіть ваш email, і ми надішлемо посилання для відновлення паролю.
        </div>
        @if (session('status'))
            <div class="w-full bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4 text-center font-bold">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->has('email') && str_contains($errors->first('email'), 'сек.'))
            <div class="w-full bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-2 rounded mb-4 text-center font-bold animate-pulse">
                {{ $errors->first('email') }}
            </div>
        @endif
        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-4 w-full">
            @csrf
            <input type="email" name="email" placeholder="Email" required autofocus class="rounded-lg border-2 border-gray-200 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-200 text-lg">
            @error('email')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 rounded-lg text-lg transition">Надіслати посилання</button>
        </form>
        <div class="mt-4 text-sm">
            Згадали пароль?
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-bold">Увійти</a>
        </div>
    </div>
</body>
</html>
