<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class ProductController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->with([
                'subcategories' => fn (HasMany $query) => $query->orderBy('name'),
                'subcategories.products' => fn (HasMany $query) => $query->orderBy('name'),
            ])
            ->orderBy('name')
            ->paginate(10);

        return view('products.index', compact('categories'));
    }

    public function create(): View
    {
        $categories = Category::query()->with('subcategories')->orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $imagePath = $this->storeImage($request->file('image'));

        try {
            Product::query()->create([
                'subcategory_id' => $validated['subcategory_id'],
                'name' => $validated['name'],
                'slug' => $this->uniqueSlug($validated['name']),
                'description' => $validated['description'],
                'image_path' => $imagePath,
                'old_price' => $validated['old_price'],
                'new_price' => $validated['new_price'],
            ]);
        } catch (Throwable $exception) {
            $this->deleteImage($imagePath);

            throw $exception;
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::query()->with('subcategories')->orderBy('name')->get();
        $product->load('subcategory');

        return view('products.edit', compact('categories', 'product'));
    }

    public function show(Product $product): View
    {
        $product->load(['subcategory.category']);

        return view('products.show', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();
        $oldImagePath = $product->image_path;
        $newImagePath = $request->hasFile('image')
            ? $this->storeImage($request->file('image'))
            : ($validated['remove_image'] ?? false ? null : $oldImagePath);

        try {
            $product->update([
                'subcategory_id' => $validated['subcategory_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'image_path' => $newImagePath,
                'old_price' => $validated['old_price'],
                'new_price' => $validated['new_price'],
            ]);
        } catch (Throwable $exception) {
            if ($newImagePath !== $oldImagePath) {
                $this->deleteImage($newImagePath);
            }

            throw $exception;
        }

        if ($newImagePath !== $oldImagePath) {
            $this->deleteImage($oldImagePath);
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $imagePath = $product->image_path;
        $product->delete();
        $this->deleteImage($imagePath);

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    private function uniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name) ?: 'product';
        $slug = $baseSlug;
        $suffix = 2;

        while (Product::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        return $slug;
    }

    private function storeImage(?UploadedFile $image): ?string
    {
        if ($image === null) {
            return null;
        }

        $path = $image->store('products', 'public');

        if ($path === false) {
            throw new RuntimeException('The product image could not be stored.');
        }

        return $path;
    }

    private function deleteImage(?string $path): void
    {
        if ($path !== null && Storage::disk('public')->exists($path)
            && ! Storage::disk('public')->delete($path)) {
            throw new RuntimeException('The product image could not be deleted.');
        }
    }
}
