@csrf

<div class="row g-4">
    <div class="col-lg-8">
        <div class="mb-3">
            <label for="name" class="form-label">Brand Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $brand->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" id="slug" name="slug" value="{{ old('slug', $brand->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror" maxlength="255">
            <div class="form-text">Leave empty to generate automatically.</div>
            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" rows="7" class="form-control @error('description') is-invalid @enderror">{{ old('description', $brand->description ?? '') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-lg-4">
        <div class="mb-3">
            <label for="logo" class="form-label">Logo</label>

            @if(isset($brand) && $brand->logo)
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" class="img-thumbnail" style="max-width:160px;max-height:160px;object-fit:contain;">
                </div>
            @endif

            <input type="file" id="logo" name="logo" accept="image/*" class="form-control @error('logo') is-invalid @enderror">
            <div class="form-text">JPG, PNG, or WEBP. Maximum 2 MB.</div>
            @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-dark">
            {{ isset($brand) ? 'Update Brand' : 'Create Brand' }}
        </button>

        <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">
            Cancel
        </a>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const name = document.getElementById('name');
    const slug = document.getElementById('slug');

    if (!name || !slug) return;

    let manual = slug.value.trim() !== '';

    slug.addEventListener('input', function () {
        manual = true;
    });

    name.addEventListener('input', function () {
        if (manual) return;

        slug.value = name.value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    });
});
</script>
@endpush
