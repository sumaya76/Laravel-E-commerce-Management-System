@extends('layouts.app')

@section('title', 'Edit category')

@section('content')
    <h1>Edit category</h1>
    <section class="panel">
        @include('categories._form', ['category' => $category])
    </section>
@endsection
