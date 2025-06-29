<?php

use Illuminate\Support\Str;

/**
 * Конфігураційний файл бази даних Laravel
 * Містить налаштування підключень до різних баз даних та Redis
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Назва з'єднання з базою даних за замовчуванням
    |--------------------------------------------------------------------------
    |
    | Тут ви можете вказати, яке з з'єднань з базою даних нижче ви бажаєте
    | використовувати як з'єднання за замовчуванням для операцій з базою даних.
    | Це з'єднання буде використовуватися, якщо інше з'єднання не вказано
    | явно при виконанні запиту / оператора.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | З'єднання з базами даних
    |--------------------------------------------------------------------------
    |
    | Нижче наведені всі з'єднання з базами даних, визначені для вашого додатку.
    | Приклад конфігурації надається для кожної системи бази даних, яка
    | підтримується Laravel. Ви можете додавати / видаляти з'єднання.
    |
    */

    'connections' => [

        // З'єднання з MySQL базою даних
        'mysql' => [
            'driver' => 'mysql',                                    // Драйвер бази даних
            'url' => env('DB_URL'),                                 // URL з'єднання (опціонально)
            'host' => env('DB_HOST', '127.0.0.1'),                  // Хост бази даних
            'port' => env('DB_PORT', '3306'),                       // Порт бази даних
            'database' => env('DB_DATABASE', 'laravel'),            // Назва бази даних
            'username' => env('DB_USERNAME', 'root'),               // Ім'я користувача
            'password' => env('DB_PASSWORD', ''),                   // Пароль
            'unix_socket' => env('DB_SOCKET', ''),                  // Unix socket (опціонально)
            'charset' => env('DB_CHARSET', 'utf8mb4'),              // Кодування символів
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'), // Коллація
            'prefix' => '',                                         // Префікс таблиць
            'prefix_indexes' => true,                               // Префікс для індексів
            'strict' => true,                                       // Строгий режим
            'engine' => null,                                       // Двигун бази даних
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'), // SSL сертифікат
            ]) : [],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Таблиця репозиторію міграцій
    |--------------------------------------------------------------------------
    |
    | Ця таблиця відстежує всі міграції, які вже були виконані для
    | вашого додатку. Використовуючи цю інформацію, ми можемо визначити,
    | які з міграцій на диску фактично не були виконані в базі даних.
    |
    */

    'migrations' => [
        'table' => 'migrations',                                    // Назва таблиці міграцій
        'update_date_on_publish' => true,                          // Оновлювати дату при публікації
    ],

    /*
    |--------------------------------------------------------------------------
    | Бази даних Redis
    |--------------------------------------------------------------------------
    |
    | Redis - це відкритий, швидкий та просунутий key-value store, який також
    | надає багатший набір команд, ніж типова key-value система, така як
    | Memcached. Ви можете визначити налаштування з'єднання тут.
    |
    */

    'redis' => [

        // Клієнт Redis (phpredis або predis)
        'client' => env('REDIS_CLIENT', 'phpredis'),

        // Загальні опції Redis
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),             // Кластер Redis
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'), // Префікс ключів
            'persistent' => env('REDIS_PERSISTENT', false),         // Постійне з'єднання
        ],

        // З'єднання Redis за замовчуванням (для сесій, черг тощо)
        'default' => [
            'url' => env('REDIS_URL'),                              // URL з'єднання
            'host' => env('REDIS_HOST', '127.0.0.1'),              // Хост Redis
            'username' => env('REDIS_USERNAME'),                    // Ім'я користувача
            'password' => env('REDIS_PASSWORD'),                    // Пароль
            'port' => env('REDIS_PORT', '6379'),                    // Порт Redis
            'database' => env('REDIS_DB', '0'),                     // Номер бази даних
        ],

        // З'єднання Redis для кешування
        'cache' => [
            'url' => env('REDIS_URL'),                              // URL з'єднання
            'host' => env('REDIS_HOST', '127.0.0.1'),              // Хост Redis
            'username' => env('REDIS_USERNAME'),                    // Ім'я користувача
            'password' => env('REDIS_PASSWORD'),                    // Пароль
            'port' => env('REDIS_PORT', '6379'),                    // Порт Redis
            'database' => env('REDIS_CACHE_DB', '1'),              // Номер бази даних для кешу
        ],

    ],

];
