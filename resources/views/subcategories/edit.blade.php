@extends('layouts.app')

@section('title', 'Edit subcategory')

@section('content')
    <h1>Edit subcategory</h1>
    <section class="panel">
        @include('subcategories._form', ['subcategory' => $subcategory])
    </section>
@endsection
