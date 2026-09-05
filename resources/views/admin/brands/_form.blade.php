@csrf

<div class="mb-4">
    <div class="sidebar-title mb-2">Brands</div>
    <h1 class="h3 mb-1">{{ isset($brand) ? 'Edit Brand' : 'Add Brand' }}</h1>
    <p class="text-muted mb-0">
        {{ isset($brand) ? 'Update brand information.' : 'Add a phone manufacturer.' }}
    </p>
</div>

<div class="row g-4">

    <div class="col-lg-8">

        <div class="bg-white border p-4 mb-4">

            <h2 class="h5 mb-1">Brand Information</h2>
            <p class="text-muted small mb-4">
                Search Brandfetch to find the official brand domain and logo.
            </p>

            <div class="row g-3">

                <div class="col-12">

                    <label for="name" class="form-label">
                        Brand Name
                    </label>

                    <div class="input-group">

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $brand->name ?? '') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Samsung"
                            required
                            maxlength="255"
                        >

                        <button
                            type="button"
                            class="btn btn-outline-dark"
                            id="brandfetch-search"
                        >
                            Find Brand
                        </button>

                    </div>

                    @error('name')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="col-12">

                    <div
                        id="brandfetch-results"
                        class="border d-none"
                    ></div>

                </div>

                <div class="col-12">

                    <label for="brand_domain" class="form-label">
                        Brand Domain
                    </label>

                    <input
                        type="text"
                        id="brand_domain"
                        name="brand_domain"
                        value="{{ old('brand_domain', $brand->brand_domain ?? '') }}"
                        class="form-control @error('brand_domain') is-invalid @enderror"
                        placeholder="samsung.com"
                        maxlength="255"
                    >

                    <div class="form-text">
                        This domain is used to load the official Brandfetch logo.
                    </div>

                    @error('brand_domain')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="col-12">

                    <label for="slug" class="form-label">
                        Slug
                    </label>

                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        value="{{ old('slug', $brand->slug ?? '') }}"
                        class="form-control @error('slug') is-invalid @enderror"
                        placeholder="samsung"
                        maxlength="255"
                    >

                    <div class="form-text">
                        Leave empty to generate automatically.
                    </div>

                    @error('slug')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="col-12">

                    <label for="description" class="form-label">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="7"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Short description about this brand..."
                    >{{ old('description', $brand->description ?? '') }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="bg-white border p-4">

            <h2 class="h5 mb-3">Brand Logo</h2>

            <div
                id="brandfetch-preview"
                class="border bg-light d-flex align-items-center justify-content-center mb-3"
                style="height: 160px;"
            >

                @if($brand->brandfetch_logo_url ?? null)
                    <img
                        src="{{ $brand->brandfetch_logo_url }}"
                        alt="{{ $brand->name }}"
                        style="max-width:120px;max-height:120px;object-fit:contain;"
                    >
                @elseif(isset($brand) && $brand->logo)
                    <img
                        src="{{ asset('storage/' . $brand->logo) }}"
                        alt="{{ $brand->name }}"
                        style="max-width:120px;max-height:120px;object-fit:contain;"
                    >
                @else
                    <span class="text-muted small">
                        No logo selected
                    </span>
                @endif

            </div>

            <div class="small text-muted mb-4">
                Brand logos are loaded directly from Brandfetch.
            </div>

            <button type="submit" class="btn btn-dark">
                {{ isset($brand) ? 'Update Brand' : 'Create Brand' }}
            </button>

            <a
                href="{{ route('admin.brands.index') }}"
                class="btn btn-outline-secondary"
            >
                Cancel
            </a>

        </div>

    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchButton = document.getElementById('brandfetch-search');
    const nameInput = document.getElementById('name');
    const domainInput = document.getElementById('brand_domain');
    const slugInput = document.getElementById('slug');
    const results = document.getElementById('brandfetch-results');
    const preview = document.getElementById('brandfetch-preview');

    if (!searchButton || !nameInput || !domainInput || !results) {
        return;
    }

    function slugify(value) {
        return value
            .toString()
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }

    function logoUrl(domain) {
        if (!domain) {
            return null;
        }

        const normalized = domain
            .replace(/^https?:\/\//i, '')
            .replace(/^www\./i, '')
            .replace(/\/+$/, '')
            .trim()
            .toLowerCase();

        return 'https://cdn.brandfetch.io/domain/' +
            encodeURIComponent(normalized) +
            '?c={{ config('services.brandfetch.client_id') }}';
    }

    function clearResults() {
        results.innerHTML = '';
        results.classList.add('d-none');
    }

    function showLogo(domain, name) {
        const url = logoUrl(domain);

        if (!preview || !url) {
            return;
        }

        preview.innerHTML = '';

        const image = document.createElement('img');
        image.src = url;
        image.alt = name || 'Brand logo';
        image.style.maxWidth = '120px';
        image.style.maxHeight = '120px';
        image.style.objectFit = 'contain';

        preview.appendChild(image);
    }

    function selectBrand(item) {
        nameInput.value = item.name || '';
        domainInput.value = item.domain || '';

        if (slugInput && slugInput.value.trim() === '') {
            slugInput.value = slugify(item.name || '');
        }

        showLogo(item.domain, item.name);
        clearResults();
    }

    function renderResults(items) {
        results.innerHTML = '';

        if (!items.length) {
            results.innerHTML =
                '<div class="p-3 text-muted small">No matching brands found.</div>';
            results.classList.remove('d-none');
            return;
        }

        items.forEach(function (item) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className =
                'w-100 border-0 border-bottom bg-white text-start p-3';

            const wrapper = document.createElement('div');
            wrapper.className = 'd-flex align-items-center gap-3';

            const image = document.createElement('img');
            image.src = item.logoUrl || logoUrl(item.domain) || '';
            image.alt = '';
            image.width = 40;
            image.height = 40;
            image.style.objectFit = 'contain';

            if (image.src) {
                wrapper.appendChild(image);
            }

            const text = document.createElement('div');
            const title = document.createElement('div');
            title.className = 'fw-semibold';
            title.textContent = item.name || '';

            const domain = document.createElement('div');
            domain.className = 'small text-muted';
            domain.textContent = item.domain || '';

            text.appendChild(title);
            text.appendChild(domain);
            wrapper.appendChild(text);
            button.appendChild(wrapper);

            button.addEventListener('click', function () {
                selectBrand(item);
            });

            results.appendChild(button);
        });

        results.classList.remove('d-none');
    }

    searchButton.addEventListener('click', async function () {
        const name = nameInput.value.trim();

        if (name.length < 2) {
            nameInput.focus();
            return;
        }

        searchButton.disabled = true;
        searchButton.textContent = 'Searching...';

        try {
            const response = await fetch(
                '{{ route('admin.brands.search') }}?name=' + encodeURIComponent(name),
                {
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Brandfetch search failed.');
            }

            renderResults(data.results || []);
        } catch (error) {
            results.innerHTML =
                '<div class="p-3 text-danger small">' +
                error.message +
                '</div>';
            results.classList.remove('d-none');
        } finally {
            searchButton.disabled = false;
            searchButton.textContent = 'Find Brand';
        }
    });
});
</script>
@endpush
