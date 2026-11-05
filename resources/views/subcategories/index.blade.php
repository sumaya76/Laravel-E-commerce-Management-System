@extends('layouts.app')

@section('title', 'Subcategories')

@section('content')
    <div class="toolbar">
        <h1>Subcategories</h1>
        <a class="button" href="{{ route('subcategories.create') }}">Add subcategory</a>
    </div>

    <section class="panel">
        @if ($subcategories->isEmpty())
            <p class="muted">No subcategories yet. Add one to get started.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Category</th>
                        <th>Products</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subcategories as $subcategory)
                        <tr>
                            <td>{{ $subcategory->name }}</td>
                            <td>{{ $subcategory->slug }}</td>
                            <td>{{ $subcategory->category->name }}</td>
                            <td>{{ $subcategory->products_count }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('subcategories.edit', $subcategory) }}">Edit</a>
                                    <form method="POST" action="{{ route('subcategories.destroy', $subcategory) }}"
                                        onsubmit="return confirm('Delete this subcategory? Subcategories with products cannot be deleted.');">
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
            <div>{{ $subcategories->links() }}</div>
        @endif
    </section>
@endsection
