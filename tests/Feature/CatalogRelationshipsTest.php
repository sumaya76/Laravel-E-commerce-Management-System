<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_catalog_relationships_are_consistent(): void
    {
        $this->seed();

        $product = Product::query()
            ->with(['subcategory.category', 'category'])
            ->where('slug', 'sample-smartphone')
            ->firstOrFail();

        $this->assertSame('Mobile Phones', $product->subcategory->name);
        $this->assertSame('Electronics', $product->subcategory->category->name);
        $this->assertSame('Electronics', $product->category->name);
        $this->assertCount(2, Category::query()->where('slug', 'electronics')->firstOrFail()->products);
    }

    public function test_category_cannot_be_deleted_while_it_has_subcategories(): void
    {
        $this->seed();

        $category = Category::query()->where('slug', 'electronics')->firstOrFail();

        $this->expectException(QueryException::class);

        $category->delete();
    }

    public function test_subcategory_cannot_be_deleted_while_it_has_products(): void
    {
        $this->seed();

        $subcategory = Subcategory::query()->where('slug', 'mobile-phones')->firstOrFail();

        $this->expectException(QueryException::class);

        $subcategory->delete();
    }
}
