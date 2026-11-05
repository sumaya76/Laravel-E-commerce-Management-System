<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $electronics = Category::query()->firstOrCreate(
            ['slug' => 'electronics'],
            ['name' => 'Electronics'],
        );

        $books = Category::query()->firstOrCreate(
            ['slug' => 'books'],
            ['name' => 'Books'],
        );

        $phones = Subcategory::query()->firstOrCreate(
            ['slug' => 'mobile-phones'],
            ['category_id' => $electronics->id, 'name' => 'Mobile Phones'],
        );

        $laptops = Subcategory::query()->firstOrCreate(
            ['slug' => 'laptops'],
            ['category_id' => $electronics->id, 'name' => 'Laptops'],
        );

        $fiction = Subcategory::query()->firstOrCreate(
            ['slug' => 'fiction'],
            ['category_id' => $books->id, 'name' => 'Fiction'],
        );

        Product::query()->firstOrCreate(
            ['slug' => 'sample-smartphone'],
            [
                'subcategory_id' => $phones->id,
                'name' => 'Sample Smartphone',
                'description' => 'A sample smartphone for catalog demonstrations.',
                'old_price' => 18000.00,
                'new_price' => 16500.00,
            ],
        );

        Product::query()->firstOrCreate(
            ['slug' => 'sample-laptop'],
            [
                'subcategory_id' => $laptops->id,
                'name' => 'Sample Laptop',
                'description' => 'A sample laptop for catalog demonstrations.',
                'old_price' => 72000.00,
                'new_price' => 69999.00,
            ],
        );

        Product::query()->firstOrCreate(
            ['slug' => 'sample-novel'],
            [
                'subcategory_id' => $fiction->id,
                'name' => 'Sample Novel',
                'description' => 'A sample novel for catalog demonstrations.',
                'old_price' => 650.00,
                'new_price' => 550.00,
            ],
        );
    }
}
