@csrf

<div class="row g-4">
    <div class="col-lg-8">
        <div class="bg-white border p-4 mb-4">
            <div class="mb-4">
                <h2 class="h5 mb-1">Device Information</h2>
                <p class="text-muted small mb-0">Set the model identity, release information, status, and main image.</p>
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
                    <div class="form-text">Available = public catalog, Rumored = upcoming, Discontinued = legacy.</div>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-8">
                    <label for="name" class="form-label">Device Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $device->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255" autocomplete="off">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="release_date" class="form-label">Release Date</label>
                    <input type="date" name="release_date" id="release_date" value="{{ old('release_date', isset($device) && $device->release_date ? $device->release_date->format('Y-m-d') : '') }}" class="form-control @error('release_date') is-invalid @enderror">
                    @error('release_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $device->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror" maxlength="255" autocomplete="off">
                    <div class="form-text">Leave empty to keep/generate the URL slug.</div>
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="image" class="form-label">Device Image</label>
                    <input type="file" name="image" id="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                    <div class="form-text">Use a clean device product image. Maximum 2 MB.</div>
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    <div id="image-preview" class="mt-3 {{ isset($device) && $device->image ? '' : 'd-none' }}">
                        <div class="text-muted small mb-2">Image preview</div>
                        <img id="image-preview-img" src="{{ isset($device) && $device->image ? asset('storage/' . $device->image) : '' }}" alt="{{ isset($device) ? $device->name : 'Device preview' }}" class="border bg-light p-2" style="width:140px;height:140px;object-fit:contain;">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <h2 class="h5 mb-1">Canonical Specifications</h2>
                    <p class="text-muted small mb-0">Choose a canonical field, then enter its value. Category and label are managed by the specification definition.</p>
                </div>
                <button type="button" class="btn btn-sm btn-outline-dark" id="add-spec">Add Specification</button>
            </div>

            @php
                $formSpecs = old('specs', isset($device) ? $device->specs->map(fn ($spec) => [
                    'definition_id' => $spec->spec_definition_id,
                    'category' => $spec->definition?->category ?? $spec->category,
                    'spec_key' => $spec->definition?->label ?? $spec->spec_key,
                    'spec_value' => $spec->spec_value,
                ])->toArray() : []);
            @endphp

            <div id="spec-list">
                @foreach($formSpecs as $index => $spec)
                    @include('admin.devices._spec-row', ['index' => $index, 'spec' => $spec, 'specDefinitions' => $specDefinitions])
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
            <p class="text-muted small mb-4">Save when the core information and specifications are complete.</p>
            <button type="submit" class="btn btn-dark me-2">{{ isset($device) ? 'Update Device' : 'Create Device' }}</button>
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
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const imagePreviewImg = document.getElementById('image-preview-img');

    let specIndex = {{ count($formSpecs) }};
    let slugManual = slug?.value.trim() !== '';

    const definitions = @json($specDefinitions->map(fn ($definition) => [
        'id' => $definition->id,
        'category' => $definition->category,
        'label' => $definition->label,
        'value_type' => $definition->value_type,
        'unit' => $definition->unit,
    ])->values());

    function slugify(value) {
        return value.toString().toLowerCase().trim().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
    }

    function updateSlug() {
        if (!slug || slugManual) return;
        const brandName = brand?.options[brand.selectedIndex]?.text || '';
        slug.value = slugify(`${brandName} ${name?.value || ''}`);
    }

    slug?.addEventListener('input', () => { slugManual = slug.value.trim() !== ''; });
    brand?.addEventListener('change', updateSlug);
    name?.addEventListener('input', updateSlug);

    imageInput?.addEventListener('change', () => {
        const file = imageInput.files?.[0];
        if (!file) {
            imagePreview?.classList.add('d-none');
            return;
        }
        imagePreviewImg.src = URL.createObjectURL(file);
        imagePreview?.classList.remove('d-none');
    });

    function definitionOptions() {
        return definitions.map((definition) => `<option value="${definition.id}">${definition.category} — ${definition.label}${definition.unit ? ` (${definition.unit})` : ''}</option>`).join('');
    }

    function addRow() {
        const row = document.createElement('div');
        row.className = 'border p-3 mb-3 spec-row';
        row.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <strong class="small">Specification</strong>
                <button type="button" class="btn btn-sm btn-outline-danger remove-spec">Remove</button>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small">Specification</label>
                    <select name="specs[${specIndex}][definition_id]" class="form-select form-select-sm spec-definition" required>
                        <option value="">Select canonical specification</option>
                        ${definitionOptions()}
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Value</label>
                    <input type="text" name="specs[${specIndex}][spec_value]" class="form-control form-control-sm" placeholder="Enter verified value" required maxlength="500">
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
