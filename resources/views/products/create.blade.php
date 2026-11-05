@extends('layouts.app')

@section('title', 'Add product')

@section('content')
    <h1>Add product</h1>
    <section class="panel">
        @if ($categories->isEmpty())
            <p class="muted">Create a category before adding a product.</p>
            <a class="button" href="{{ route('categories.create') }}">Add category</a>
        @elseif ($categories->every(fn ($category) => $category->subcategories->isEmpty()))
            <p class="muted">Create a subcategory before adding a product.</p>
            <a class="button" href="{{ route('subcategories.create') }}">Add subcategory</a>
        @else
            @include('products._form', ['product' => null])
        @endif
    </section>
@endsection
