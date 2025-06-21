import os
import pymysql
from dotenv import load_dotenv
import difflib
import random

KEYWORDS = {
    "глютамін": "l-glutamine",
    "l-карнітин": "l-carnitine",
    "магній": "magnesium",
    "цинк": "zinc",
    "мелатонін": "metalonin",
    "колаген": "kolagen",
    "cla": "cla",
    "омега-3": "omega-3",
    "акційний товар": "akciyniy_tovar",
    "спортивна добавка": "sportivna_dobavka",
    "протеїн": "protein",
    "креатин": "creatine",
    "bcaa": "bcaa",
}

CATEGORY_DIRS = {
    "protein": "protein",
    "creatine": "creatine",
    "bcaa": "bcaa",
}

def normalize(s):
    return s.lower().replace(' ', '').replace('_', '').replace('-', '').replace('(', '').replace(')', '').replace('#', '')

load_dotenv()
DB_HOST = os.getenv('DB_HOST', 'localhost')
DB_USER = os.getenv('DB_USERNAME', 'root')
DB_PASSWORD = os.getenv('DB_PASSWORD', '')
DB_NAME = os.getenv('DB_DATABASE', 'laravel')

# Збираємо всі зображення з різних директорій
image_files = {}
base_dir = 'public/images/products'

# Спочатку збираємо зображення з основної директорії others
others_dir = os.path.join(base_dir, 'others')
if os.path.exists(others_dir):
    image_files['others'] = [f for f in os.listdir(others_dir) if f.lower().endswith(('.jfif', '.jpg', '.jpeg', '.png'))]

# Потім збираємо зображення з категорійних директорій
for category in CATEGORY_DIRS.values():
    category_dir = os.path.join(base_dir, category)
    if os.path.exists(category_dir):
        image_files[category] = [f for f in os.listdir(category_dir) if f.lower().endswith(('.jfif', '.jpg', '.jpeg', '.png'))]

conn = pymysql.connect(host=DB_HOST, user=DB_USER, password=DB_PASSWORD, database=DB_NAME, charset='utf8mb4')
cursor = conn.cursor()

cursor.execute("SELECT id, name FROM products WHERE image_path IS NULL OR image_path = '' OR image_path = '/images/no-image.png'")
products = cursor.fetchall()

assigned = 0
for prod_id, name in products:
    norm_name = normalize(name)
    
    # 1. Перевіряємо чи це продукт з окремої категорії
    category_match = None
    for keyword, category in CATEGORY_DIRS.items():
        if keyword in norm_name.lower():
            category_match = category
            break
    
    if category_match and category_match in image_files:
        # Шукаємо всі співпадіння в категорійній директорії
        matches = []
        best_ratio = 0.0
        for img in image_files[category_match]:
            img_base = os.path.splitext(img)[0]
            ratio = difflib.SequenceMatcher(None, norm_name, normalize(img_base)).ratio()
            if ratio > best_ratio:
                best_ratio = ratio
                matches = [img]
            elif ratio == best_ratio:
                matches.append(img)
        if matches:
            img = random.choice(matches)
            path = f'images/products/{category_match}/{img}'
            cursor.execute("UPDATE products SET image_path=%s WHERE id=%s", (path, prod_id))
            print(f'Product {prod_id} ({name}) -> {img} (category: {category_match}, score: {best_ratio:.2f})')
            assigned += 1
            continue
    
    # 2. Якщо не знайдено в категорії, шукаємо в others
    matches = []
    best_ratio = 0.0
    for img in image_files['others']:
        img_base = os.path.splitext(img)[0]
        ratio = difflib.SequenceMatcher(None, norm_name, normalize(img_base)).ratio()
        if ratio > best_ratio:
            best_ratio = ratio
            matches = [img]
        elif ratio == best_ratio:
            matches.append(img)
    # 3. Якщо збіг > 0.8 — використовуємо випадкову з найкращих
    if matches and best_ratio > 0.8:
        img = random.choice(matches)
        path = f'images/products/others/{img}'
        cursor.execute("UPDATE products SET image_path=%s WHERE id=%s", (path, prod_id))
        print(f'Product {prod_id} ({name}) -> {img} (score: {best_ratio:.2f})')
        assigned += 1
        continue
    # 4. Якщо ні — шукаємо по ключовому слову
    found = False
    for ukr, lat in KEYWORDS.items():
        if ukr in norm_name.lower() or lat in norm_name.lower():
            matching_images = [img for img in image_files['others'] if lat in img.lower()]
            if matching_images:
                img = random.choice(matching_images)
                path = f'images/products/others/{img}'
                cursor.execute("UPDATE products SET image_path=%s WHERE id=%s", (path, prod_id))
                print(f'Product {prod_id} ({name}) -> {img} (by keyword: {lat})')
                assigned += 1
                found = True
                break
    # 5. Якщо все ще не знайдено — призначаємо випадкову спортивна_добавка
    if not found:
        sport_imgs = [img for img in image_files['others'] if 'sportivna_dobavka' in img or 'спортивна_добавка' in img]
        if sport_imgs:
            img = random.choice(sport_imgs)
            path = f'images/products/others/{img}'
            cursor.execute("UPDATE products SET image_path=%s WHERE id=%s", (path, prod_id))
            print(f'Product {prod_id} ({name}) -> {img} (default: sportivna_dobavka)')
            assigned += 1
        else:
            print(f'Product {prod_id} ({name}) -> [NO GOOD MATCH]')

conn.commit()
cursor.close()
conn.close()

print(f'Готово! Картинки призначено для {assigned} продуктів.')