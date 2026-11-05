@extends('layouts.app')

@section('title', 'Edit product')

@section('content')
    <h1>Edit product</h1>
    <section class="panel">
        @include('products._form', ['product' => $product])
    </section>
@endsection
