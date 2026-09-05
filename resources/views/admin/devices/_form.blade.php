@csrf

<div class="row g-4">
    <div class="col-lg-8">
        <div class="bg-white border p-4 mb-4">
            <div class="mb-4">
                <h2 class="h5 mb-1">Device Information</h2>
                <p class="text-muted small mb-0">Set the basic model details and publication status.</p>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="brand_id" class="form-label">Brand</label>
                    <select name="brand_id" id="brand_id" class="form-select @error('brand_id') is-invalid @enderror" required>
                        <option value="">Select brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('brand_id', $device->brand_id ?? '') == $brand->id)>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('brand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        @foreach(['available' => 'Available', 'rumored' => 'Rumored', 'discontinued' => 'Discontinued'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $device->status ?? 'available') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-8">
                    <label for="name" class="form-label">Device Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $device->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="release_date" class="form-label">Release Date</label>
                    <input type="date" name="release_date" id="release_date" value="{{ old('release_date', isset($device) && $device->release_date ? $device->release_date->format('Y-m-d') : '') }}" class="form-control @error('release_date') is-invalid @enderror">
                    @error('release_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $device->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror" maxlength="255">
                    <div class="form-text">Leave empty to generate a unique slug from brand and device name.</div>
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="image" class="form-label">Device Image</label>
                    <input type="file" name="image" id="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                    <div class="form-text">JPG, PNG, or WEBP. Maximum 2 MB.</div>
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    @isset($device)
                        @if($device->image)
                            <div class="mt-3">
                                <img src="{{ asset('storage/' . $device->image) }}" alt="{{ $device->name }}" class="border bg-light p-2" style="width: 120px; height: 120px; object-fit: contain;">
                            </div>
                        @endif
                    @endisset
                </div>
            </div>
        </div>

        <div class="bg-white border p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <h2 class="h5 mb-1">Specifications</h2>
                    <p class="text-muted small mb-0">Add specifications in the order they should appear on the device page.</p>
                </div>
                <button type="button" class="btn btn-sm btn-outline-dark" id="add-spec">Add Specification</button>
            </div>

            <div id="spec-list">
                @php
                    $formSpecs = old('specs', isset($device) ? $device->specs->map(fn ($spec) => [
                        'category' => $spec->category,
                        'spec_key' => $spec->spec_key,
                        'spec_value' => $spec->spec_value,
                    ])->toArray() : []);
                @endphp

                @foreach($formSpecs as $index => $spec)
                    @include('admin.devices._spec-row', ['index' => $index, 'spec' => $spec])
                @endforeach
            </div>

            <div id="spec-empty" class="text-muted text-center border p-4 {{ count($formSpecs) ? 'd-none' : '' }}">
                No specifications added yet.
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="bg-white border p-4">
            <h2 class="h5 mb-1">Save Device</h2>
            <p class="text-muted small mb-4">Save the device after reviewing the information and specifications.</p>

            <button type="submit" class="btn btn-dark me-2">
                {{ isset($device) ? 'Update Device' : 'Create Device' }}
            </button>
            <a href="{{ route('admin.devices.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('spec-list');
    const empty = document.getElementById('spec-empty');
    const addButton = document.getElementById('add-spec');
    const brand = document.getElementById('brand_id');
    const name = document.getElementById('name');
    const slug = document.getElementById('slug');

    let specIndex = {{ count($formSpecs) }};
    let slugManual = slug?.value.trim() !== '';

    function slugify(value) {
        return value
            .toString()
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }

    function updateSlug() {
        if (!slug || slugManual) return;

        const brandName = brand?.options[brand.selectedIndex]?.text || '';
        slug.value = slugify(`${brandName} ${name?.value || ''}`);
    }

    slug?.addEventListener('input', () => {
        slugManual = slug.value.trim() !== '';
    });

    brand?.addEventListener('change', updateSlug);
    name?.addEventListener('input', updateSlug);

    function addRow() {
        const row = document.createElement('div');
        row.className = 'border p-3 mb-3 spec-row';
        row.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <strong class="small">Specification</strong>
                <button type="button" class="btn btn-sm btn-outline-danger remove-spec">Remove</button>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small">Category</label>
                    <select name="specs[${specIndex}][category]" class="form-select form-select-sm" required>
                        <option value="">Select category</option>
                        <option value="Display">Display</option>
                        <option value="Camera">Camera</option>
                        <option value="Battery">Battery</option>
                        <option value="Performance">Performance</option>
                        <option value="Body">Body</option>
                        <option value="Connectivity">Connectivity</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Spec Name</label>
                    <input type="text" name="specs[${specIndex}][spec_key]" class="form-control form-control-sm" placeholder="Screen Size" required maxlength="255">
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Value</label>
                    <input type="text" name="specs[${specIndex}][spec_value]" class="form-control form-control-sm" placeholder="6.3 inches" required maxlength="500">
                </div>
            </div>
        `;
        list.appendChild(row);
        specIndex++;
        empty.classList.add('d-none');
    }

    list.addEventListener('click', (event) => {
        if (!event.target.classList.contains('remove-spec')) return;
        event.target.closest('.spec-row')?.remove();
        if (!list.querySelector('.spec-row')) empty.classList.remove('d-none');
    });

    addButton?.addEventListener('click', addRow);
});
</script>
@endpush
