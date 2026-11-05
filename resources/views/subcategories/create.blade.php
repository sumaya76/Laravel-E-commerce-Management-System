@extends('layouts.app')

@section('title', 'Add subcategory')

@section('content')
    <h1>Add subcategory</h1>
    <section class="panel">
        @if ($categories->isEmpty())
            <p class="muted">Create a category before adding a subcategory.</p>
            <a class="button" href="{{ route('categories.create') }}">Add category</a>
        @else
            @include('subcategories._form', ['subcategory' => null])
        @endif
    </section>
@endsection
