@extends('layouts.app')

@section('title', 'Add category')

@section('content')
    <h1>Add category</h1>
    <section class="panel">
        @include('categories._form', ['category' => null])
    </section>
@endsection
