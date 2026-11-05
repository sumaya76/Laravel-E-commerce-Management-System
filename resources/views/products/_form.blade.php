<form method="POST" enctype="multipart/form-data"
    action="{{ $product ? route('products.update', $product) : route('products.store') }}">
    @csrf
    @if ($product)
        @method('PUT')
    @endif

    <label for="category_id">Category</label>
    <select id="category_id" name="category_id" required>
        <option value="">Choose a category</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}"
                @selected((string) old('category_id', $product?->subcategory?->category_id) === (string) $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id') <p class="field-error">{{ $message }}</p> @enderror

    <label for="subcategory_id">Subcategory</label>
    <select id="subcategory_id" name="subcategory_id" required>
        <option value="">Choose a subcategory</option>
        @foreach ($categories as $category)
            @foreach ($category->subcategories as $subcategory)
                <option value="{{ $subcategory->id }}" data-category="{{ $category->id }}"
                    @selected((string) old('subcategory_id', $product?->subcategory_id) === (string) $subcategory->id)>
                    {{ $subcategory->name }}
                </option>
            @endforeach
        @endforeach
    </select>
    @error('subcategory_id') <p class="field-error">{{ $message }}</p> @enderror

    <label for="name">Product name</label>
    <input id="name" name="name" type="text" value="{{ old('name', $product?->name) }}" maxlength="255" required>
    @error('name') <p class="field-error">{{ $message }}</p> @enderror

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="5" maxlength="10000" required>{{ old('description', $product?->description) }}</textarea>
    @error('description') <p class="field-error">{{ $message }}</p> @enderror

    @if ($product?->image_path)
        <p>Current image</p>
        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) }}"
            alt="{{ $product->name }}" width="160" height="160" style="object-fit: cover"
            onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}'">
        <label>
            <input type="checkbox" name="remove_image" value="1" style="width: auto"
                @checked(old('remove_image'))>
            Remove current image
        </label>
    @endif

    <label for="image">{{ $product?->image_path ? 'Replace image' : 'Product image' }} (JPG, PNG, or WebP; up to 2 MB)</label>
    <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
    @error('image') <p class="field-error">{{ $message }}</p> @enderror

    <div class="toolbar">
        <div>
            <label for="old_price">Old price</label>
            <input id="old_price" name="old_price" type="number" min="0.01" max="99999999.99" step="0.01"
                value="{{ old('old_price', $product?->old_price) }}" required>
            @error('old_price') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="new_price">New price</label>
            <input id="new_price" name="new_price" type="number" min="0.01" max="99999999.99" step="0.01"
                value="{{ old('new_price', $product?->new_price) }}" required>
            @error('new_price') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    </div>
    <p class="muted">Old price is required. New price must be positive and no greater than the old price.</p>

    <div class="actions">
        <button type="submit">{{ $product ? 'Save changes' : 'Create product' }}</button>
        <a class="button secondary" href="{{ route('products.index') }}">Cancel</a>
    </div>
</form>

<script>
    (() => {
        const category = document.getElementById('category_id');
        const subcategory = document.getElementById('subcategory_id');
        const options = [...subcategory.options].filter(option => option.dataset.category);

        const filterSubcategories = (resetSelection = false) => {
            if (resetSelection) {
                subcategory.value = '';
            }

            options.forEach(option => {
                option.hidden = option.dataset.category !== category.value;
            });

            const selected = subcategory.selectedOptions[0];
            if (selected?.dataset.category && selected.dataset.category !== category.value) {
                subcategory.value = '';
            }
        };

        category.addEventListener('change', () => filterSubcategories(true));
        filterSubcategories();
    })();
</script>
