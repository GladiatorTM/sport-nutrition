# Sport Nutrition

Laravel-проєкт для інтернет-магазину спортивного харчування з сучасною адмін-панеллю.

## Можливості
- Управління продуктами (додавання, редагування, видалення, пошук, фільтрація за категоріями)
- Управління користувачами (адміністратори та звичайні користувачі)
- Аналітика продажів та переглядів
- Завантаження та попередній перегляд зображень продуктів (підтримка jpeg, png, gif, jfif)
- Адаптивний дизайн для адмін-панелі
- Авторизація, реєстрація, Google OAuth

## Встановлення

1. Клонувати репозиторій:
   ```bash
   git clone https://github.com/GladiatorTM/sport-nutrition.git
   cd sport-nutrition
   ```
2. Встановити залежності:
   ```bash
   composer install
   npm install && npm run build
   ```
3. Створити файл налаштувань:
   ```bash
   cp .env.example .env
   ```
4. Згенерувати ключ додатку:
   ```bash
   php artisan key:generate
   ```
5. Налаштувати підключення до бази даних у `.env`
6. Виконати міграції та сидери:
   ```bash
   php artisan migrate --seed
   ```
7. Створити симлінк для зображень:
   ```bash
   php artisan storage:link
   ```

## Доступ до адмін-панелі
- Увійдіть під користувачем з правами адміністратора (`is_admin = 1` у таблиці users)
- Адмін-панель доступна за `/admin`

## Технології
- Laravel 10+
- PHP 8+
- MySQL
- Tailwind CSS
- JavaScript (Blade компоненти)

## Скрипти для роботи з зображеннями
У папці `scripts/` є Python-скрипти для автоматичного призначення зображень продуктам.

## Ліцензія
MIT

---

# Sport Nutrition (EN)

A Laravel-based web application for a sport nutrition e-commerce store with a modern admin panel.

## Features
- Product management (CRUD, search, category filter)
- User management (admins & users)
- Sales & views analytics
- Product image upload & preview (jpeg, png, gif, jfif supported)
- Responsive admin panel UI
- Authentication, registration, Google OAuth

## Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/GladiatorTM/sport-nutrition.git
   cd sport-nutrition
   ```
2. Install dependencies:
   ```bash
   composer install
   npm install && npm run build
   ```
3. Copy environment file:
   ```bash
   cp .env.example .env
   ```
4. Generate app key:
   ```bash
   php artisan key:generate
   ```
5. Configure your database in `.env`
6. Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```
7. Create storage symlink:
   ```bash
   php artisan storage:link
   ```

## Admin Panel Access
- Login as a user with `is_admin = 1` in the users table
- Admin panel is available at `/admin`

## Technologies
- Laravel 10+
- PHP 8+
- MySQL
- Tailwind CSS
- JavaScript (Blade components)

## Image Scripts
Python scripts for product image assignment are in the `scripts/` folder.

## License
MIT
