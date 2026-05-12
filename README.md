# Room Rentals

Веб додаток для оренди житла на Laravel + Livewire +  Filament.

## Опис проєкту

Це додаток для взаємодії орендарів і власників житла:
- орендарі переглядають оголошення, бронюють житло, та залишають оцінку
- власники керують запитами на бронювання
- адміністратори працюють через адмін панель

## Основні можливості

- Каталог житла та сторінка детального перегляду житла
- Реєстрація, вхід і особистий кабінет користувача
- Створення бронювання для авторизованих користувачів
- Real-time сповіщення 
- Кабінет орендаря з можливістю запиту на скасування та залишення відгуку
- Кабінет власника для підтвердження/відхилення бронювань
- Адмін панель для адміністрації проєкту

## Технології

- PHP 8.3+
- Laravel 12
- Livewire 3
- Filament 4
- MySQL
- Vite + Tailwind CSS
- Laravel Reverb (broadcast/websocket)

## Приклади роботи

### 1) Сценарій орендаря

1. Зареєструватись та увійти в акаунт
2. Відкрити головну сторінку (`/`) та обрати об’єкт
3. Перейти на сторінку об’єкта (`/properties/{property}`)
4. Забронювати об'єкт нерухомості на вільний час
5. Очікувати підтвердження або відмови від власника

### 2) Сценарій власника

1. Перейти в `/owner/booking-requests`
2. Підтвердити або відхилити запит на бронювання
3. Переглянути/відредагувати власні оголошення в `/my-properties`

## Вимоги

- PHP `^8.3`
- Composer
- Node.js `18+` 
- npm
- MySQL `8+`

## Встановлення та запуск

1. Клонувати репозиторій:
```bash
git clone <repo-url>
cd Room-Rentals
```

2. Встановити залежності:
```bash
composer install
npm install
```

3. Налаштувати середовище:
```bash
cp .env.example .env
php artisan key:generate
```

4. Вказати параметри БД в `.env` (за замовчуванням використовується MySQL, БД запускається через Docker):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=room-rentals
DB_USERNAME=room-rentals
DB_PASSWORD=room-rentals
```

5. Запустити міграції:
```bash
php artisan migrate
```

6. Запустити проєкт у dev-режимі:
```bash
php artisan reverb:start
php artisan queue:work
composer dev
```

Після запуску застосунок буде доступний за адресою `http://localhost:8000` 

## Запуск через Docker (Laravel Sail)

1. Встановити залежності та підготувати `.env`:
```bash
composer install
cp .env.example .env
php artisan key:generate
```

2. Запустити контейнери:
```bash
./vendor/bin/sail up -d
```

3. Виконати міграції:
```bash
./vendor/bin/sail artisan migrate
```

4. Запустити фронтенд:
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

Після запуску застосунок буде доступний за адресою `http://localhost`.

## Збирання 

```bash
npm run build
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Ліцензія

Проєкт розповсюджується за ліцензією [MIT](LICENSE).
