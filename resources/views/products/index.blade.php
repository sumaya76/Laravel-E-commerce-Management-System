@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="toolbar">
        <h1>Products</h1>
        <a class="button" href="{{ route('products.create') }}">Add product</a>
    </div>

    <section class="panel">
        @if ($categories->isEmpty())
            <p class="muted">No products or categories yet. Add a category and product to get started.</p>
        @else
            @foreach ($categories as $category)
                <h2>{{ $category->name }}</h2>
                @forelse ($category->subcategories as $subcategory)
                    <h3>{{ $subcategory->name }}</h3>
                    @if ($subcategory->products->isEmpty())
                        <p class="muted">No products in this subcategory.</p>
                    @else
                        <table>
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Product</th>
                                    <th>Prices</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subcategory->products as $product)
                                    <tr>
                                        <td>
                                            <a href="{{ route('products.show', $product->slug) }}">
                                                <img src="{{ $product->image_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) : asset('images/product-placeholder.svg') }}"
                                                    alt="{{ $product->name }}" width="64" height="64" style="object-fit: cover"
                                                    onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}'">
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                                            <br><span class="muted">{{ $product->slug }}</span>
                                        </td>
                                        <td>
                                            <del class="muted">{{ number_format((float) $product->old_price, 2) }}</del><br>
                                            {{ number_format((float) $product->new_price, 2) }}
                                        </td>
                                        <td>
                                            <div class="actions">
                                                <a href="{{ route('products.edit', $product) }}">Edit</a>
                                                <form method="POST" action="{{ route('products.destroy', $product) }}"
                                                    onsubmit="return confirm('Delete this product and its image?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="danger" type="submit">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @empty
                    <p class="muted">No subcategories in this category.</p>
                @endforelse
            @endforeach
            <div>{{ $categories->links() }}</div>
        @endif
    </section>
@endsection
