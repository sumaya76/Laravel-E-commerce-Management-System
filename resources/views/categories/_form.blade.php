<form method="POST" action="{{ $category ? route('categories.update', $category) : route('categories.store') }}">
    @csrf
    @if ($category)
        @method('PUT')
    @endif
    <label for="name">Category name</label>
    <input id="name" name="name" type="text" value="{{ old('name', $category?->name) }}" maxlength="255" required autofocus>
    @error('name') <p class="field-error">{{ $message }}</p> @enderror
    <p class="muted">The URL slug is generated from the name.</p>
    <div class="actions">
        <button type="submit">{{ $category ? 'Save changes' : 'Create category' }}</button>
        <a class="button secondary" href="{{ route('categories.index') }}">Cancel</a>
    </div>
</form>
