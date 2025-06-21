import os
import pymysql
from dotenv import load_dotenv

# Завантажуємо змінні з .env
load_dotenv()

DB_HOST = os.getenv('DB_HOST', 'localhost')
DB_USER = os.getenv('DB_USERNAME', 'root')
DB_PASSWORD = os.getenv('DB_PASSWORD', '')
DB_NAME = os.getenv('DB_DATABASE', 'laravel')

images_dir = 'public/images/products/others'
image_files = set(f.lower() for f in os.listdir(images_dir) if f.lower().endswith(('.jfif', '.jpg', '.jpeg', '.png')))

conn = pymysql.connect(host=DB_HOST, user=DB_USER, password=DB_PASSWORD, database=DB_NAME, charset='utf8mb4')
cursor = conn.cursor()

cursor.execute("SELECT id, name FROM products WHERE image_path IS NULL OR image_path = '' OR image_path = '/images/no-image.png'")
products = cursor.fetchall()

assigned = 0
for prod_id, name in products:
    # Формуємо можливу назву файлу: наприклад, 'CLA #9' -> 'cla(9).jfif'
    base = name.lower().replace(' ', '_').replace('#', '(').replace('№', '(')
    if '(' in base:
        base = base.replace('(', '(')  # залишаємо дужку
    if ')' not in base:
        base += ')'  # додаємо закриваючу дужку, якщо є номер
    found = False
    for ext in ['.jfif', '.jpg', '.jpeg', '.png']:
        fname = f"{base}{ext}"
        if fname in image_files:
            path = f'images/products/others/{fname}'
            cursor.execute("UPDATE products SET image_path=%s WHERE id=%s", (path, prod_id))
            print(f'Product {prod_id} ({name}) -> {fname}')
            assigned += 1
            found = True
            break
    if not found:
        print(f'Product {prod_id} ({name}) -> [NO IMAGE FOUND]')

conn.commit()
cursor.close()
conn.close()

print(f'Готово! Картинки призначено для {assigned} продуктів.') 