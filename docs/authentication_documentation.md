# АУТЕНТИФІКАЦІЯ ТА АВТОРИЗАЦІЯ

## 2.1. Контролер системи авторизації

### 2.1.1. AuthenticatedSessionController

Контролер `AuthenticatedSessionController` відповідає за управління сесіями користувачів та обробку входу/виходу з системи.

**Основні методи:**

```php
class AuthenticatedSessionController extends Controller
{
    /**
     * Відображення форми входу
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Обробка запиту на аутентифікацію
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        return redirect('/')->with('success', 'Ви успішно увійшли!');
    }

    /**
     * Завершення сесії користувача
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
```

**Функціональність:**
- `create()` - відображає форму входу з валідацією
- `store()` - обробляє дані форми, аутентифікує користувача та створює сесію
- `destroy()` - безпечно завершує сесію користувача

### 2.1.2. RegisteredUserController

Контролер для реєстрації нових користувачів:

```php
class RegisteredUserController extends Controller
{
    /**
     * Відображення форми реєстрації
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Обробка запиту на реєстрацію
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect('/')->with('success', 'Ви успішно зареєструвалися!');
    }
}
```

### 2.1.3. GoogleController

Контролер для OAuth авторизації через Google:

```php
class GoogleController extends Controller
{
    /**
     * Перенаправлення на Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Обробка callback від Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = User::firstOrCreate([
                'email' => $googleUser->getEmail(),
            ], [
                'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Google User',
                'password' => bcrypt(str()->random(16)),
            ]);
            Auth::login($user, true);
            return redirect('/')->with('success', 'Ви успішно увійшли через Google!');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Не вдалося авторизуватися через Google.');
        }
    }
}
```

## 2.2. Модель сутності користувачів

### 2.2.1. User Model

Модель `User` представляє користувача в системі та реалізує інтерфейс `Authenticatable`:

```php
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Поля, доступні для масового заповнення
     */
    protected $fillable = [
        'name',      // Ім'я користувача
        'email',     // Email адреса (унікальна)
        'password',  // Хешований пароль
        'avatar',    // Шлях до аватара користувача
        'is_admin',  // Права адміністратора (true/false)
        'google_id', // ID користувача в Google
    ];

    /**
     * Приховані поля при серіалізації
     */
    protected $hidden = [
        'password',        // Пароль завжди приховується
        'remember_token',  // Токен "запам'ятати мене"
    ];

    /**
     * Приведення типів для атрибутів
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    /**
     * Перевірка прав адміністратора
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
}
```

### 2.2.2. Структура бази даних

**Таблиця `users`:**
- `id` - унікальний ідентифікатор
- `name` - ім'я користувача
- `email` - email адреса (унікальна)
- `email_verified_at` - дата підтвердження email
- `password` - хешований пароль
- `avatar` - шлях до аватара
- `is_admin` - права адміністратора (boolean)
- `google_id` - ID користувача в Google
- `remember_token` - токен для "запам'ятати мене"
- `created_at`, `updated_at` - часові мітки

### 2.2.3. Міграції

```php
// Створення таблиці користувачів
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('avatar')->nullable();
    $table->boolean('is_admin')->default(false);
    $table->string('google_id')->nullable();
    $table->rememberToken();
    $table->timestamps();
});

// Додавання поля is_admin
Schema::table('users', function (Blueprint $table) {
    $table->boolean('is_admin')->default(false)->after('password');
});

// Додавання поля google_id
Schema::table('users', function (Blueprint $table) {
    $table->string('google_id')->nullable()->after('is_admin');
});
```

## 2.3. Представлення виду

### 2.3.1. Форма входу (login.blade.php)

```html
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вхід | GYMZONE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border-4 border-yellow-400">
        <!-- Логотип -->
        <div class="flex items-center mb-6">
            <img src="{{ asset('images/logo_clean.png') }}" alt="Логотип" class="h-12 w-auto mr-2">
        </div>
        
        <h2 class="text-2xl font-bold mb-4">Вхід до акаунту</h2>
        
        <!-- Повідомлення про помилки -->
        @if(session('error'))
            <div class="w-full bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif
        
        <!-- Форма входу -->
        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4 w-full">
            @csrf
            <input type="email" name="email" placeholder="Email" required autofocus 
                   class="rounded-lg border-2 border-gray-200 px-4 py-2 focus:border-yellow-400">
            @error('email')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            
            <input type="password" name="password" placeholder="Пароль" required 
                   class="rounded-lg border-2 border-gray-200 px-4 py-2 focus:border-yellow-400">
            @error('password')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            
            <!-- Запам'ятати мене та відновлення пароля -->
            <div class="flex items-center justify-between w-full">
                <label class="flex items-center text-sm">
                    <input type="checkbox" name="remember" class="mr-2">
                    Запам'ятати мене
                </label>
                <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:underline">
                    Забули пароль?
                </a>
            </div>
            
            <button type="submit" class="bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold py-2 rounded-lg">
                Увійти
            </button>
        </form>
        
        <!-- Google OAuth -->
        <div class="w-full flex flex-col gap-2 mt-4">
            <a href="{{ route('google.login') }}" class="flex items-center justify-center gap-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold py-2 rounded-lg">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="h-6 w-6">
                Увійти через Google
            </a>
        </div>
        
        <!-- Посилання на реєстрацію -->
        <div class="mt-4 text-sm">
            Немає акаунту?
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline font-bold">
                Зареєструватися
            </a>
        </div>
    </div>
</body>
</html>
```

### 2.3.2. Форма реєстрації (register.blade.php)

```html
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Реєстрація | GYMZONE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border-4 border-yellow-400">
        <!-- Логотип -->
        <div class="flex items-center mb-6">
            <img src="{{ asset('images/logo_clean.png') }}" alt="Логотип" class="h-12 w-auto mr-2">
        </div>
        
        <h2 class="text-2xl font-bold mb-4">Реєстрація</h2>
        
        <!-- Форма реєстрації -->
        <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4 w-full">
            @csrf
            
            <input type="text" name="name" placeholder="Ім'я" required autofocus 
                   class="rounded-lg border-2 border-gray-200 px-4 py-2 focus:border-yellow-400">
            @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            
            <input type="email" name="email" placeholder="Email" required 
                   class="rounded-lg border-2 border-gray-200 px-4 py-2 focus:border-yellow-400">
            @error('email')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            
            <input type="password" name="password" placeholder="Пароль" required 
                   class="rounded-lg border-2 border-gray-200 px-4 py-2 focus:border-yellow-400">
            @error('password')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            
            <input type="password" name="password_confirmation" placeholder="Підтвердіть пароль" required 
                   class="rounded-lg border-2 border-gray-200 px-4 py-2 focus:border-yellow-400">
            
            <button type="submit" class="bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold py-2 rounded-lg">
                Зареєструватися
            </button>
        </form>
        
        <!-- Посилання на вхід -->
        <div class="mt-4 text-sm">
            Вже є акаунт?
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-bold">
                Увійти
            </a>
        </div>
    </div>
</body>
</html>
```

### 2.3.3. Middleware для авторизації

```php
class CheckAdmin
{
    /**
     * Обробляє вхідний запит
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Перевіряємо, чи авторизований користувач
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Перевіряємо, чи має користувач права адміністратора
        if (!auth()->user()->is_admin) {
            abort(403, 'Доступ заборонено. Необхідні права адміністратора.');
        }

        return $next($request);
    }
}
```

### 2.3.4. Маршрути авторизації

```php
// Маршрути аутентифікації
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});

// Google OAuth маршрути
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Вихід
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Адмін маршрути
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('products', ProductController::class);
    Route::resource('users', UserController::class);
});
```

## 2.4. Безпека та валідація

### 2.4.1. Валідація входу (LoginRequest)

```php
class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        if (!Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
    }
}
```

### 2.4.2. Захист від CSRF

Всі форми захищені від CSRF атак через токен `@csrf`:

```html
<form method="POST" action="{{ route('login') }}">
    @csrf
    <!-- поля форми -->
</form>
```

### 2.4.3. Хешування паролів

Паролі автоматично хешуються через Laravel's Hash facade:

```php
'password' => Hash::make($request->password),
```

### 2.4.4. Сесійна безпека

- Регенерація сесії після входу
- Інвалідація сесії при виході
- Регенерація CSRF токена

## 2.5. Інтеграція з Google OAuth

### 2.5.1. Налаштування

```php
// config/services.php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],
```

### 2.5.2. Обробка callback

```php
public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();
        
        // Пошук або створення користувача
        $user = User::firstOrCreate([
            'email' => $googleUser->getEmail(),
        ], [
            'name' => $googleUser->getName(),
            'password' => bcrypt(str()->random(16)),
        ]);
        
        Auth::login($user, true);
        return redirect('/')->with('success', 'Ви успішно увійшли через Google!');
    } catch (\Exception $e) {
        return redirect('/login')->with('error', 'Не вдалося авторизуватися через Google.');
    }
}
```

## 2.6. Висновки

Система аутентифікації та авторизації реалізована з використанням сучасних практик безпеки:

1. **Багаторівнева авторизація** - звичайні користувачі та адміністратори
2. **OAuth інтеграція** - підтримка Google авторизації
3. **Безпека сесій** - регенерація токенів, CSRF захист
4. **Валідація даних** - перевірка вхідних даних
5. **Middleware захист** - контроль доступу до адмін-панелі
6. **Адаптивний UI** - сучасний дизайн форм

Система забезпечує безпечний доступ до функціоналу додатку та ефективне управління правами користувачів. 