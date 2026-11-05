<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubcategoryRequest;
use App\Http\Requests\UpdateSubcategoryRequest;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubcategoryController extends Controller
{
    public function index(): View
    {
        $subcategories = Subcategory::query()
            ->with('category')
            ->withCount('products')
            ->orderBy('name')
            ->paginate(10);

        return view('subcategories.index', compact('subcategories'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('subcategories.create', compact('categories'));
    }

    public function store(StoreSubcategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Subcategory::query()->create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
        ]);

        return redirect()->route('subcategories.index')->with('success', 'Subcategory created successfully.');
    }

    public function edit(Subcategory $subcategory): View
    {
        $categories = Category::query()->orderBy('name')->get();
        $subcategory->loadCount('products');

        return view('subcategories.edit', compact('categories', 'subcategory'));
    }

    public function update(UpdateSubcategoryRequest $request, Subcategory $subcategory): RedirectResponse
    {
        $validated = $request->validated();

        if (
            $subcategory->category_id !== (int) $validated['category_id']
            && $subcategory->products()->exists()
        ) {
            return redirect()->route('subcategories.edit', $subcategory)
                ->withInput()
                ->with('error', 'This subcategory cannot be moved while it contains products.');
        }

        $attributes = [
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
        ];

        if ($subcategory->name !== $validated['name']) {
            $attributes['slug'] = $this->uniqueSlug($validated['name'], $subcategory->id);
        }

        $subcategory->update($attributes);

        return redirect()->route('subcategories.index')->with('success', 'Subcategory updated successfully.');
    }

    public function destroy(Subcategory $subcategory): RedirectResponse
    {
        if ($subcategory->products()->exists()) {
            return redirect()->route('subcategories.index')
                ->with('error', 'This subcategory cannot be deleted while it has products.');
        }

        $subcategory->delete();

        return redirect()->route('subcategories.index')->with('success', 'Subcategory deleted successfully.');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name) ?: 'subcategory';
        $slug = $baseSlug;
        $suffix = 2;

        while (Subcategory::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        return $slug;
    }
}
