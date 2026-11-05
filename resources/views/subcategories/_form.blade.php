<form method="POST" action="{{ $subcategory ? route('subcategories.update', $subcategory) : route('subcategories.store') }}">
    @csrf
    @if ($subcategory)
        @method('PUT')
    @endif
    <label for="category_id">Category</label>
    <select id="category_id" name="category_id" required>
        <option value="">Choose a category</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}"
                @selected((string) old('category_id', $subcategory?->category_id) === (string) $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id') <p class="field-error">{{ $message }}</p> @enderror
    @if ($subcategory && $subcategory->products_count > 0)
        <p class="muted">This subcategory contains products, so its category cannot be changed.</p>
    @endif

    <label for="name">Subcategory name</label>
    <input id="name" name="name" type="text" value="{{ old('name', $subcategory?->name) }}" maxlength="255" required>
    @error('name') <p class="field-error">{{ $message }}</p> @enderror
    <p class="muted">The URL slug is generated from the name.</p>
    <div class="actions">
        <button type="submit">{{ $subcategory ? 'Save changes' : 'Create subcategory' }}</button>
        <a class="button secondary" href="{{ route('subcategories.index') }}">Cancel</a>
    </div>
</form>
