@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <p><a href="{{ route('products.index') }}">Back to products</a></p>
    <article class="panel">
        <div class="toolbar" style="align-items: flex-start">
            <div>
                <img src="{{ $product->image_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) : asset('images/product-placeholder.svg') }}"
                    alt="{{ $product->name }}" width="320" height="240" style="max-width: 100%; object-fit: contain"
                    onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}'">
            </div>
            <div style="flex: 1; min-width: 16rem">
                <h1>{{ $product->name }}</h1>
                <p class="muted">
                    {{ $product->subcategory->category->name }} / {{ $product->subcategory->name }}
                </p>
                <p><del class="muted">{{ number_format((float) $product->old_price, 2) }}</del></p>
                <p><strong>{{ number_format((float) $product->new_price, 2) }}</strong></p>
                <p>{{ $product->description }}</p>
                <div class="actions">
                    <a class="button" href="{{ route('products.edit', $product) }}">Edit product</a>
                    <a class="button secondary" href="{{ route('products.index') }}">Continue browsing</a>
                </div>
            </div>
        </div>
    </article>
@endsection
