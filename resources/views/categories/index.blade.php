@extends('layouts.app')

@section('title', 'Categories')

@section('content')
    <div class="toolbar">
        <h1>Categories</h1>
        <a class="button" href="{{ route('categories.create') }}">Add category</a>
    </div>

    <section class="panel">
        @if ($categories->isEmpty())
            <p class="muted">No categories yet. Add one to get started.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Subcategories</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->slug }}</td>
                            <td>{{ $category->subcategories_count }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('categories.edit', $category) }}">Edit</a>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                        onsubmit="return confirm('Delete this category? Categories with subcategories cannot be deleted.');">
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
            <div>{{ $categories->links() }}</div>
        @endif
    </section>
@endsection
