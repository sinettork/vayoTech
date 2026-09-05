@csrf

<div class="row g-4">
    <div class="col-lg-8">
        <div class="bg-white border p-4 mb-4">
            <div class="mb-4">
                <div class="sidebar-title mb-2">Content</div>
                <h2 class="h5 mb-1">Article</h2>
                <p class="text-muted small mb-0">Write the article content and keep the URL slug readable.</p>
            </div>

            <div class="mb-4">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title', $newsPost->title ?? '') }}" class="form-control @error('title') is-invalid @enderror" required maxlength="255" autofocus>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label for="slug" class="form-label">Slug</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $newsPost->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror" maxlength="255">
                <div class="form-text">Leave empty to generate it from the title.</div>
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div>
                <label for="body" class="form-label">Content</label>
                <textarea id="body" name="body" rows="20" class="form-control @error('body') is-invalid @enderror" required>{{ old('body', $newsPost->body ?? '') }}</textarea>
                <div class="form-text">Plain text is supported. Keep paragraphs separated by blank lines.</div>
                @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="bg-white border p-4 mb-4">
            <div class="sidebar-title mb-2">Publishing</div>
            <h2 class="h5 mb-1">Publication</h2>
            <p class="text-muted small mb-4">Choose when the article becomes visible on the website.</p>

            <div class="mb-4">
                <label for="published_at" class="form-label">Publish Date</label>
                <input
                    type="datetime-local"
                    id="published_at"
                    name="published_at"
                    value="{{ old('published_at', isset($newsPost) && $newsPost->published_at ? $newsPost->published_at->format('Y-m-d\TH:i') : '') }}"
                    class="form-control @error('published_at') is-invalid @enderror"
                >
                <div class="form-text">Empty = Draft. Future date/time = Scheduled. Current or past = Published.</div>
                @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            @if(isset($newsPost))
                @php
                    $currentStatus = !$newsPost->published_at
                        ? 'Draft'
                        : ($newsPost->published_at->isFuture() ? 'Scheduled' : 'Published');
                @endphp
                <div class="border p-3 mb-4 bg-light">
                    <div class="text-muted small mb-1">Current status</div>
                    <div class="fw-semibold">{{ $currentStatus }}</div>
                </div>
            @endif

            <button type="submit" class="btn btn-dark me-2">
                {{ isset($newsPost) ? 'Update News' : 'Create News' }}
            </button>
            <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>

        <div class="bg-white border p-4">
            <div class="sidebar-title mb-2">Media</div>
            <h2 class="h5 mb-1">Featured Image</h2>
            <p class="text-muted small mb-3">Use a clear landscape image for the article card and article page.</p>

            <div id="image-preview" class="border bg-light d-flex align-items-center justify-content-center mb-3" style="height:220px;overflow:hidden;">
                @if(isset($newsPost) && $newsPost->image)
                    <img src="{{ asset('storage/' . $newsPost->image) }}" alt="{{ $newsPost->title }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <span class="text-muted small">No image selected</span>
                @endif
            </div>

            <input type="file" id="image" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
            <div class="form-text">JPG, PNG, or WEBP. Maximum 4 MB.</div>
            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const title = document.getElementById('title');
    const slug = document.getElementById('slug');
    const image = document.getElementById('image');
    const preview = document.getElementById('image-preview');

    if (title && slug) {
        let manual = slug.value.trim() !== '';

        slug.addEventListener('input', function () {
            manual = slug.value.trim() !== '';
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
    }

    image?.addEventListener('change', function () {
        const file = image.files?.[0];

        if (!preview) return;

        if (!file) {
            preview.innerHTML = '<span class="text-muted small">No image selected</span>';
            return;
        }

        if (!file.type.startsWith('image/')) {
            preview.innerHTML = '<span class="text-danger small">Please select an image file.</span>';
            return;
        }

        const url = URL.createObjectURL(file);
        preview.innerHTML = '';

        const img = document.createElement('img');
        img.src = url;
        img.alt = 'Selected featured image';
        img.style.width = '100%';
        img.style.height = '100%';
        img.style.objectFit = 'cover';
        img.onload = () => URL.revokeObjectURL(url);

        preview.appendChild(img);
    });
});
</script>
@endpush
