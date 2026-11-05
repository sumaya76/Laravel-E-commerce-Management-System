# E-commerce Management System

A Laravel application for managing an e-commerce product catalog.

## Requirements

- PHP 8.2+
- Composer
- MySQL 8+ or MariaDB
- Node.js and npm for frontend assets

## Local setup

1. Install PHP dependencies:

   ```bash
   composer install
   ```

2. Create the local environment file and application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   On Windows PowerShell, use `Copy-Item .env.example .env` instead of `cp`.

3. Create a MySQL database named `ecommerce_management_system` using `utf8mb4` and `utf8mb4_unicode_ci`. Configure `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env` to match your local MySQL account. The example values target a default XAMPP MySQL installation; change the username/password if your MySQL account uses different credentials.

4. Run migrations and load sample catalog data:

   ```bash
   php artisan migrate --seed
   ```

5. Install and build frontend assets:

   ```bash
   npm install
   npm run build
   ```

6. Start the development server:

   ```bash
   php artisan serve
   ```

## Catalog data model

- A **Category** has many **Subcategories**.
- A **Subcategory** belongs to a Category and has many **Products**.
- A **Product** belongs to a Subcategory; its Category is obtained through that Subcategory. This avoids storing a second category foreign key on products that could disagree with the subcategory.
- Category, Subcategory, and Product slugs are unique. Subcategory names are unique within their category.
- Prices use `DECIMAL(10,2)`. The old price is required in the database; product image paths are nullable.

Foreign keys use restrictive deletion: a category with subcategories cannot be deleted, and a subcategory with products cannot be deleted. This prevents accidental loss of related catalog records. The application must also prevent changing a subcategory's category while it has products.

## Project structure

- `app/Models` — Eloquent catalog models and relationships
- `app/Http/Controllers` — HTTP controllers
- `database/migrations` — database schema
- `database/seeders` — demo catalog data
- `routes/web.php` — web routes
- `resources/views` — Blade templates
- `tests` — automated tests

## Database and tests

- Reset the local database and reload demo data: `php artisan migrate:fresh --seed`
- Run the test suite: `php artisan test`

The test suite uses an in-memory SQLite database. Local MySQL data and `.env` are not committed. Use `.env.example` as the environment configuration template; never commit real credentials.
