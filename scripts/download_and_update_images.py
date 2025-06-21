import os
import requests
import pymysql
from dotenv import load_dotenv

# Завантажуємо змінні з .env
load_dotenv()

DB_HOST = os.getenv('DB_HOST', 'localhost')
DB_USER = os.getenv('DB_USERNAME', 'root')
DB_PASSWORD = os.getenv('DB_PASSWORD', '')
DB_NAME = os.getenv('DB_DATABASE', 'laravel')

output_dir = "public/images/products"
os.makedirs(output_dir, exist_ok=True)

# Групи та посилання на фото
group_images = {
    "Акційний товар": "https://images.pexels.com/photos/375904/pexels-photo-375904.jpeg",
    "Спортивна добавка": "https://images.pexels.com/photos/1552242/pexels-photo-1552242.jpeg",
    "Глютамін": "https://images.pexels.com/photos/5938360/pexels-photo-5938360.jpeg",
    "L-карнітин": "https://images.pexels.com/photos/4046992/pexels-photo-4046992.jpeg",
    "Магній": "https://images.pexels.com/photos/4046993/pexels-photo-4046993.jpeg",
    "Цинк": "https://images.pexels.com/photos/4046994/pexels-photo-4046994.jpeg",
    "Мелатонін": "https://images.pexels.com/photos/4046995/pexels-photo-4046995.jpeg",
    "Колаген": "https://images.pexels.com/photos/4046996/pexels-photo-4046996.jpeg",
    "CLA": "https://images.pexels.com/photos/4046997/pexels-photo-4046997.jpeg",
    "Омега-3": "https://images.pexels.com/photos/4046998/pexels-photo-4046998.jpeg",
    "Вітамін": "https://images.pexels.com/photos/3683077/pexels-photo-3683077.jpeg",
    "Мультивітаміни": "https://images.pexels.com/photos/3683077/pexels-photo-3683077.jpeg",
    "Комплекс": "https://images.pexels.com/photos/3683077/pexels-photo-3683077.jpeg",
}

# Завантаження фото
for group, url in group_images.items():
    filename = f"{group.replace(' ', '_').replace('-', '').lower()}.jpg"
    filepath = os.path.join(output_dir, filename)
    if not os.path.exists(filepath):
        r = requests.get(url)
        with open(filepath, "wb") as f:
            f.write(r.content)
        print(f"Downloaded {filename}")

# Підключення до БД
conn = pymysql.connect(host=DB_HOST, user=DB_USER, password=DB_PASSWORD, database=DB_NAME, charset='utf8mb4')
cursor = conn.cursor()

def update_group(group_keywords, filename):
    for keyword in group_keywords:
        cursor.execute(
            "UPDATE products SET image_path=%s WHERE (image_path IS NULL OR image_path = '' OR image_path = '/images/no-image.png') AND name LIKE %s",
            (f"images/products/{filename}", f"%{keyword}%")
        )
        print(f"Updated products with keyword '{keyword}' to {filename}")

# Групи та ключові слова
groups = {
    "Акційний товар": ["Акційний товар"],
    "Спортивна добавка": ["Спортивна добавка"],
    "Глютамін": ["Глютамін"],
    "L-карнітин": ["L-карнітин"],
    "Магній": ["Магній"],
    "Цинк": ["Цинк"],
    "Мелатонін": ["Мелатонін"],
    "Колаген": ["Колаген"],
    "CLA": ["CLA"],
    "Омега-3": ["Омега-3"],
    "Вітамін": ["Вітамін", "Фолієва кислота"],
    "Мультивітаміни": ["Мультивітаміни"],
    "Комплекс": ["Комплекс"],
}

for group, keywords in groups.items():
    filename = f"{group.replace(' ', '_').replace('-', '').lower()}.jpg"
    update_group(keywords, filename)

conn.commit()
cursor.close()
conn.close()

print("Готово! Фото завантажені та image_path оновлено.") 