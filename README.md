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
   php artisan storage:link
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
- Prices use `DECIMAL(10,2)`. The old price is required; new price must be positive and no greater than the old price. Both accept up to two decimal places.
- Product images are optional. Accepted formats are JPG, PNG, and WebP, up to 2 MB; files are stored on Laravel's public disk and require `php artisan storage:link`.
- Replacing or removing a product image deletes the previous file. Deleting a product removes its image; products with no image or a missing image display a local placeholder.

Foreign keys use restrictive deletion: a category with subcategories cannot be deleted, and a subcategory with products cannot be deleted. This prevents accidental loss of related catalog records. The application must also prevent changing a subcategory's category while it has products.

## Project structure

- `app/Models` — Eloquent catalog models and relationships
- `app/Http/Controllers` — catalog resource controllers
- `app/Http/Requests` — catalog form validation
- `database/migrations` — database schema
- `database/seeders` — demo catalog data
- `routes/web.php` — web routes
- `resources/views` — catalog list and form Blade templates
- `tests` — automated tests

## Category and subcategory management

- Categories and subcategories support list, create, edit, and delete operations at `/categories` and `/subcategories`.
- Names are validated; slugs are generated from names and made unique. Subcategory names must be unique within their category.
- Subcategory forms list existing categories only and validate the selected category.
- A category with subcategories and a subcategory with products cannot be deleted. A subcategory with products cannot be moved to another category.
- Delete actions require browser confirmation, and successful or blocked actions display a message.

## Product management

- Products support list, create, edit, and delete operations at `/products`.
- The product form captures category, subcategory, name, description, image, old price, and new price.
- The subcategory selector is filtered by category in the form, and the server validates that the selected subcategory belongs to the submitted category.
- Product slugs are unique and remain stable when the product name is edited.
- The product list is grouped by category and subcategory and eager-loads related products to avoid N+1 queries.
- Product details use the SEO-friendly `/products/{slug}` URL. The slug stays stable when a product name changes.

## Route reference

| HTTP method | URI | Route name | Purpose |
|---|---|---|---|
| GET | `/categories` | `categories.index` | List categories |
| GET | `/categories/create` | `categories.create` | Show category form |
| POST | `/categories` | `categories.store` | Create category |
| GET | `/categories/{category}/edit` | `categories.edit` | Show category edit form |
| PUT/PATCH | `/categories/{category}` | `categories.update` | Update category |
| DELETE | `/categories/{category}` | `categories.destroy` | Delete category |
| GET | `/subcategories` | `subcategories.index` | List subcategories |
| GET | `/subcategories/create` | `subcategories.create` | Show subcategory form |
| POST | `/subcategories` | `subcategories.store` | Create subcategory |
| GET | `/subcategories/{subcategory}/edit` | `subcategories.edit` | Show subcategory edit form |
| PUT/PATCH | `/subcategories/{subcategory}` | `subcategories.update` | Update subcategory |
| DELETE | `/subcategories/{subcategory}` | `subcategories.destroy` | Delete subcategory |
| GET | `/products` | `products.index` | List products grouped by category/subcategory |
| GET | `/products/create` | `products.create` | Show product form |
| POST | `/products` | `products.store` | Create product |
| GET | `/products/{product}/edit` | `products.edit` | Show product edit form |
| PUT/PATCH | `/products/{product}` | `products.update` | Update product |
| DELETE | `/products/{product}` | `products.destroy` | Delete product |
| GET | `/products/{slug}` | `products.show` | Show product details by slug |

## Database and tests

- Reset the local database and reload demo data: `php artisan migrate:fresh --seed`
- Run the test suite: `php artisan test`

The test suite uses an in-memory SQLite database. Local MySQL data and `.env` are not committed. Use `.env.example` as the environment configuration template; never commit real credentials.
