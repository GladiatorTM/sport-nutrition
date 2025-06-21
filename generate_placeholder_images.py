import os
import pymysql
from PIL import Image, ImageDraw, ImageFont
from dotenv import load_dotenv

# Завантажуємо змінні з .env
load_dotenv()

DB_HOST = os.getenv('DB_HOST', 'localhost')
DB_USER = os.getenv('DB_USERNAME', 'root')
DB_PASSWORD = os.getenv('DB_PASSWORD', '')
DB_NAME = os.getenv('DB_DATABASE', 'laravel')

output_dir = "public/images/products/other"
os.makedirs(output_dir, exist_ok=True)

# Підключення до БД
conn = pymysql.connect(host=DB_HOST, user=DB_USER, password=DB_PASSWORD, database=DB_NAME, charset='utf8mb4')
cursor = conn.cursor()

# Витягуємо продукти без картинки
cursor.execute("""
    SELECT id, name FROM products
    WHERE image_path IS NULL OR image_path = '' OR image_path = '/images/no-image.png'
""")
products = cursor.fetchall()

for prod_id, name in products:
    img = Image.new('RGB', (400, 400), color=(200, 200, 200))
    d = ImageDraw.Draw(img)
    try:
        font = ImageFont.truetype("arial.ttf", 28)
    except:
        font = ImageFont.load_default()
    text = name
    w, h = d.textsize(text, font=font)
    d.text(((400-w)/2, (400-h)/2), text, fill=(50, 50, 50), font=font)
    safe_name = "".join([c if c.isalnum() else "_" for c in name])
    filename = f"{safe_name}.png"
    img.save(os.path.join(output_dir, filename))

    # Оновлюємо image_path у базі
    new_path = f"images/products/other/{filename}"
    cursor.execute("UPDATE products SET image_path=%s WHERE id=%s", (new_path, prod_id))

conn.commit()
cursor.close()
conn.close()

print("Генерація та оновлення image_path завершено!") 