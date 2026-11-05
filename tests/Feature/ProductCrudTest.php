<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created_with_an_image_and_unique_slug(): void
    {
        Storage::fake('public');
        $this->seed();
        $category = Category::query()->where('slug', 'electronics')->firstOrFail();
        $subcategory = Subcategory::query()->where('slug', 'mobile-phones')->firstOrFail();

        $this->get(route('products.index'))->assertOk();
        $this->get(route('products.create'))->assertOk();
        $this->post(route('products.store'), [
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'name' => 'Wireless Headphones',
            'description' => 'Wireless over-ear headphones.',
            'image' => UploadedFile::fake()->image('headphones.jpg'),
            'old_price' => '120.00',
            'new_price' => '99.99',
        ])->assertRedirect(route('products.index'))
            ->assertSessionHas('success');

        $product = Product::query()->where('name', 'Wireless Headphones')->firstOrFail();
        $this->assertSame('wireless-headphones', $product->slug);
        $this->assertSame($subcategory->id, $product->subcategory_id);
        $this->assertNotNull($product->image_path);
        Storage::disk('public')->assertExists($product->image_path);

        $this->post(route('products.store'), [
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'name' => 'Wireless Headphones',
            'description' => 'Another product with the same name.',
            'old_price' => '120.00',
            'new_price' => '99.99',
        ])->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', ['slug' => 'wireless-headphones-2']);
    }

    public function test_product_requires_a_subcategory_belonging_to_the_selected_category(): void
    {
        $this->seed();
        $books = Category::query()->where('slug', 'books')->firstOrFail();
        $phones = Subcategory::query()->where('slug', 'mobile-phones')->firstOrFail();

        $this->from(route('products.create'))
            ->post(route('products.store'), [
                'category_id' => $books->id,
                'subcategory_id' => $phones->id,
                'name' => 'Invalid Category Product',
                'description' => 'This category and subcategory do not match.',
                'old_price' => '100.00',
                'new_price' => '80.00',
            ])
            ->assertRedirect(route('products.create'))
            ->assertSessionHasErrors('subcategory_id');

        $this->assertDatabaseMissing('products', ['name' => 'Invalid Category Product']);
    }

    public function test_product_prices_must_be_positive_and_new_price_cannot_exceed_old_price(): void
    {
        $this->seed();
        $category = Category::query()->where('slug', 'electronics')->firstOrFail();
        $subcategory = Subcategory::query()->where('slug', 'mobile-phones')->firstOrFail();
        $attributes = [
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'name' => 'Invalid Price Product',
            'description' => 'Price validation test.',
            'old_price' => '100.00',
            'new_price' => '101.00',
        ];

        $this->from(route('products.create'))
            ->post(route('products.store'), $attributes)
            ->assertRedirect(route('products.create'))
            ->assertSessionHasErrors('new_price');

        $attributes['old_price'] = '0';
        $attributes['new_price'] = '0';
        $this->from(route('products.create'))
            ->post(route('products.store'), $attributes)
            ->assertRedirect(route('products.create'))
            ->assertSessionHasErrors(['old_price', 'new_price']);

        $attributes['old_price'] = '100.001';
        $attributes['new_price'] = '99.00';
        $this->from(route('products.create'))
            ->post(route('products.store'), $attributes)
            ->assertRedirect(route('products.create'))
            ->assertSessionHasErrors('old_price');
    }

    public function test_product_image_is_preserved_replaced_removed_and_deleted_with_product(): void
    {
        Storage::fake('public');
        $this->seed();
        $product = Product::query()->where('slug', 'sample-smartphone')->firstOrFail();
        $category = $product->subcategory->category;
        $subcategory = $product->subcategory;

        $product->update(['image_path' => 'products/original.jpg']);
        Storage::disk('public')->put('products/original.jpg', 'original image');

        $this->get(route('products.edit', $product))->assertOk();

        $baseAttributes = [
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'name' => $product->name,
            'description' => $product->description,
            'old_price' => $product->old_price,
            'new_price' => $product->new_price,
        ];

        $this->put(route('products.update', $product), $baseAttributes)
            ->assertRedirect(route('products.index'));
        $this->assertSame('products/original.jpg', $product->fresh()->image_path);
        Storage::disk('public')->assertExists('products/original.jpg');

        $this->put(route('products.update', $product), [
            ...$baseAttributes,
            'image' => UploadedFile::fake()->image('replacement.png'),
        ])->assertRedirect(route('products.index'));
        $replacementPath = $product->fresh()->image_path;
        $this->assertNotSame('products/original.jpg', $replacementPath);
        Storage::disk('public')->assertMissing('products/original.jpg');
        Storage::disk('public')->assertExists($replacementPath);

        $this->put(route('products.update', $product), [
            ...$baseAttributes,
            'remove_image' => '1',
        ])->assertRedirect(route('products.index'));
        $this->assertNull($product->fresh()->image_path);
        Storage::disk('public')->assertMissing($replacementPath);

        $this->put(route('products.update', $product), [
            ...$baseAttributes,
            'image' => UploadedFile::fake()->image('final.webp'),
        ])->assertRedirect(route('products.index'));
        $finalPath = $product->fresh()->image_path;

        $this->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing($finalPath);
    }

    public function test_product_rejects_non_image_uploads(): void
    {
        Storage::fake('public');
        $this->seed();
        $category = Category::query()->where('slug', 'electronics')->firstOrFail();
        $subcategory = Subcategory::query()->where('slug', 'mobile-phones')->firstOrFail();

        $this->from(route('products.create'))
            ->post(route('products.store'), [
                'category_id' => $category->id,
                'subcategory_id' => $subcategory->id,
                'name' => 'Text File Product',
                'description' => 'Invalid image upload.',
                'image' => UploadedFile::fake()->create('payload.txt', 10, 'text/plain'),
                'old_price' => '10.00',
                'new_price' => '9.00',
            ])
            ->assertRedirect(route('products.create'))
            ->assertSessionHasErrors('image');

        $this->assertDatabaseMissing('products', ['name' => 'Text File Product']);
        Storage::disk('public')->assertDirectoryEmpty('products');
    }

    public function test_product_slug_remains_stable_when_its_name_changes(): void
    {
        $this->seed();
        $product = Product::query()->where('slug', 'sample-smartphone')->firstOrFail();
        $originalSlug = $product->slug;

        $this->put(route('products.update', $product), [
            'category_id' => $product->subcategory->category_id,
            'subcategory_id' => $product->subcategory_id,
            'name' => 'Renamed Smartphone',
            'description' => $product->description,
            'old_price' => $product->old_price,
            'new_price' => $product->new_price,
        ])->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Renamed Smartphone',
            'slug' => $originalSlug,
        ]);
    }

    public function test_product_details_are_resolved_by_slug(): void
    {
        $this->seed();
        $product = Product::query()->where('slug', 'sample-smartphone')->firstOrFail();

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee($product->description)
            ->assertSee($product->subcategory->category->name)
            ->assertSee($product->subcategory->name);

        $this->get('/products/not-a-real-product')
            ->assertNotFound();
    }

    public function test_product_listing_is_grouped_and_eager_loads_products(): void
    {
        $this->seed();
        $productQueries = 0;

        DB::listen(function (QueryExecuted $query) use (&$productQueries): void {
            if (preg_match('/\bproducts\b/i', $query->sql) === 1) {
                $productQueries++;
            }
        });

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('Electronics')
            ->assertSee('Mobile Phones')
            ->assertSee('Sample Smartphone')
            ->assertSee('Books')
            ->assertSee('Fiction')
            ->assertSee('Sample Novel')
            ->assertSee(route('products.show', 'sample-smartphone'));

        $this->assertSame(1, $productQueries, 'The grouped catalog should eager-load products in one query.');
    }
}
