@csrf

<div class="row g-4">
    <div class="col-lg-8">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" id="title" name="title" value="{{ old('title', $newsPost->title ?? '') }}" class="form-control @error('title') is-invalid @enderror" required maxlength="255">
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" id="slug" name="slug" value="{{ old('slug', $newsPost->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror">
            <div class="form-text">Leave empty to generate automatically.</div>
            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="body" class="form-label">Content</label>
            <textarea id="body" name="body" rows="18" class="form-control @error('body') is-invalid @enderror" required>{{ old('body', $newsPost->body ?? '') }}</textarea>
            @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-lg-4">
        <div class="mb-4">
            <label for="image" class="form-label">Featured Image</label>

            @if(isset($newsPost) && $newsPost->image)
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $newsPost->image) }}" alt="{{ $newsPost->title }}" class="img-fluid rounded border" style="width:100%;max-height:240px;object-fit:cover;">
                </div>
            @endif

            <input type="file" id="image" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
            <div class="form-text">JPG, PNG, or WEBP. Maximum 4 MB.</div>
            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label for="published_at" class="form-label">Publish Date</label>
            <input
                type="datetime-local"
                id="published_at"
                name="published_at"
                value="{{ old('published_at', isset($newsPost) && $newsPost->published_at ? $newsPost->published_at->format('Y-m-d\TH:i') : '') }}"
                class="form-control @error('published_at') is-invalid @enderror"
            >
            <div class="form-text">Leave empty for a draft.</div>
            @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-dark">
            {{ isset($newsPost) ? 'Update News' : 'Create News' }}
        </button>

        <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">
            Cancel
        </a>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const title = document.getElementById('title');
    const slug = document.getElementById('slug');

    if (!title || !slug) return;

    let manual = slug.value.trim() !== '';

    slug.addEventListener('input', function () {
        manual = true;
    });

    title.addEventListener('input', function () {
        if (manual) return;

        slug.value = title.value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    });
});
</script>
@endpush
