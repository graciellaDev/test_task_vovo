## Тестовое задание для маркетплейса VOVO

Небольшое Laravel-приложение с витриной товаров и API-эндпоинтом для получения списка товаров с **фильтрацией, сортировкой и пагинацией**.

## Что реализовано

- **Эндпоинт**: `GET /products` возвращает JSON со списком товаров (формат Laravel paginator).
- **Фильтры**:
  - **по названию**: `name` (поиск `LIKE %...%`)
  - **по цене**: `price_from`, `price_to`
  - **в наличии**: `in_stock` (булево значение)
  - **по рейтингу**: `rating_from` (минимальный рейтинг)
  - **по категории**: `category_id`
- **Сортировка** (`sort`):
  - `newest` (по убыванию `id`, значение по умолчанию)
  - `price_asc`
  - `price_desc`
  - `rating_desc`
- **Пагинация**: `per_page` (ограничено диапазоном **1..100**, по умолчанию 10).

## Стек

- **PHP / Laravel** (папка `laravel-app`)
- **База данных**: совместимо с MySQL / PostgreSQL / SQLite (по настройке `.env`)

## Структура данных (миграции)

### Таблица `categories`

- `id` (PK)
- `name` (string)
- `created_at`, `updated_at`

### Таблица `products`

- `id` (PK)
- `name` (string)
- `price` (decimal(10,2))
- `category_id` (FK → `categories.id`)
- `in_stock` (boolean)
- `rating` (float(1,1))
- `created_at`, `updated_at`

Дополнительно есть миграция для **FULLTEXT-индекса по `products.name`** (актуально для MySQL). В текущей реализации в контроллере используется `LIKE`, а вариант с `MATCH ... AGAINST` оставлен закомментированным.

## Быстрый старт (локально)

### 1) Установить зависимости

Перейдите в папку приложения и установите зависимости Composer:

```bash
cd laravel-app
composer install
```

### 2) Настроить окружение

```bash
copy .env.example .env
php artisan key:generate
```

Далее в `.env` настройте подключение к БД (например, `DB_CONNECTION=mysql` + параметры доступа) или используйте SQLite.

### 3) Применить миграции

```bash
php artisan migrate
```

### 4) Запустить сервер

```bash
php artisan serve
```

По умолчанию приложение будет доступно на `http://127.0.0.1:8000`.

## Примеры запросов

- Получить список товаров (пагинация по 10):
  - `GET /products`

- Поиск по названию + сортировка по цене:
  - `GET /products?name=iphone&sort=price_asc`

- Фильтр по диапазону цен и наличию:
  - `GET /products?price_from=1000&price_to=5000&in_stock=1`

- Фильтр по категории и минимальному рейтингу:
  - `GET /products?category_id=2&rating_from=4`

- Изменить размер страницы:
  - `GET /products?per_page=50`

## Маршруты

- `GET /` — стандартная стартовая страница Laravel
- `GET /products` — список товаров с фильтрами/сортировкой/пагинацией

