@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="toolbar">
        <h1>Products</h1>
        <a class="button" href="{{ route('products.create') }}">Add product</a>
    </div>

    <section class="panel">
        @if ($products->isEmpty())
            <p class="muted">No products yet. Add one to get started.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Category / subcategory</th>
                        <th>Prices</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>
                                <img src="{{ $product->image_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) : asset('images/product-placeholder.svg') }}"
                                    alt="{{ $product->name }}" width="64" height="64" style="object-fit: cover"
                                    onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}'">
                            </td>
                            <td>{{ $product->name }}<br><span class="muted">{{ $product->slug }}</span></td>
                            <td>{{ $product->subcategory->category->name }} / {{ $product->subcategory->name }}</td>
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
            <div>{{ $products->links() }}</div>
        @endif
    </section>
@endsection
