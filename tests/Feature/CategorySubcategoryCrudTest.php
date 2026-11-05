<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategorySubcategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_crud_generates_and_updates_unique_slugs(): void
    {
        $this->get(route('categories.index'))->assertOk();
        $this->get(route('categories.create'))->assertOk();

        $this->post(route('categories.store'), ['name' => 'Home Audio'])
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('success');

        $category = Category::query()->where('name', 'Home Audio')->firstOrFail();
        $this->assertSame('home-audio', $category->slug);

        $this->post(route('categories.store'), ['name' => 'Home Audio'])
            ->assertRedirect(route('categories.index'));

        $duplicateSlugCategory = Category::query()->whereKeyNot($category->id)->firstOrFail();
        $this->assertSame('home-audio-2', $duplicateSlugCategory->slug);

        $this->get(route('categories.edit', $category))->assertOk();
        $this->put(route('categories.update', $category), ['name' => 'Home Cinema'])
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'slug' => 'home-cinema']);

        $this->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_name_is_required(): void
    {
        $this->from(route('categories.create'))
            ->post(route('categories.store'), ['name' => ''])
            ->assertRedirect(route('categories.create'))
            ->assertSessionHasErrors('name');

        $this->assertDatabaseCount('categories', 0);
    }

    public function test_category_with_subcategories_cannot_be_deleted(): void
    {
        $this->seed();
        $category = Category::query()->where('slug', 'electronics')->firstOrFail();

        $this->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('error', 'This category cannot be deleted while it has subcategories.');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_subcategory_crud_validates_category_and_generates_slug(): void
    {
        $category = Category::query()->create(['name' => 'Accessories', 'slug' => 'accessories']);
        $otherCategory = Category::query()->create(['name' => 'Clothing', 'slug' => 'clothing']);

        $this->get(route('subcategories.index'))->assertOk();
        $this->get(route('subcategories.create'))->assertOk();

        $this->from(route('subcategories.create'))
            ->post(route('subcategories.store'), ['category_id' => 999, 'name' => 'Belts'])
            ->assertRedirect(route('subcategories.create'))
            ->assertSessionHasErrors('category_id');

        $this->post(route('subcategories.store'), [
            'category_id' => $category->id,
            'name' => 'Phone Cases',
        ])->assertRedirect(route('subcategories.index'))
            ->assertSessionHas('success');

        $subcategory = Subcategory::query()->where('name', 'Phone Cases')->firstOrFail();
        $this->assertSame('phone-cases', $subcategory->slug);
        $this->assertSame($category->id, $subcategory->category_id);

        $this->get(route('subcategories.edit', $subcategory))->assertOk();
        $this->put(route('subcategories.update', $subcategory), [
            'category_id' => $otherCategory->id,
            'name' => 'Belts',
        ])->assertRedirect(route('subcategories.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseHas('subcategories', [
            'id' => $subcategory->id,
            'category_id' => $otherCategory->id,
            'slug' => 'belts',
        ]);

        $this->delete(route('subcategories.destroy', $subcategory))
            ->assertRedirect(route('subcategories.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('subcategories', ['id' => $subcategory->id]);
    }

    public function test_subcategory_name_must_be_unique_within_its_category(): void
    {
        $this->seed();
        $category = Category::query()->where('slug', 'electronics')->firstOrFail();

        $this->from(route('subcategories.create'))
            ->post(route('subcategories.store'), [
                'category_id' => $category->id,
                'name' => 'Mobile Phones',
            ])
            ->assertRedirect(route('subcategories.create'))
            ->assertSessionHasErrors('name');
    }

    public function test_subcategory_with_products_cannot_be_deleted_or_moved(): void
    {
        $this->seed();
        $subcategory = Subcategory::query()->where('slug', 'mobile-phones')->firstOrFail();
        $otherCategory = Category::query()->where('slug', 'books')->firstOrFail();

        $this->delete(route('subcategories.destroy', $subcategory))
            ->assertRedirect(route('subcategories.index'))
            ->assertSessionHas('error', 'This subcategory cannot be deleted while it has products.');

        $this->put(route('subcategories.update', $subcategory), [
            'category_id' => $otherCategory->id,
            'name' => $subcategory->name,
        ])->assertRedirect(route('subcategories.edit', $subcategory))
            ->assertSessionHas('error', 'This subcategory cannot be moved while it contains products.');

        $this->assertSame(1, Product::query()->where('subcategory_id', $subcategory->id)->count());
        $this->assertDatabaseHas('subcategories', [
            'id' => $subcategory->id,
            'category_id' => $subcategory->category_id,
        ]);
    }
}
