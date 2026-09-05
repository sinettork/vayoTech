@extends('layouts.app')

@section('title', 'Compare Phones - PhoneSpecs')
@section('meta_description', 'Compare up to three smartphones side by side with quick specs and detailed specifications.')

@section('content')
<div class="compare-page">
    <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
        <div>
            <span class="text-primary small fw-semibold text-uppercase">Phone comparison</span>
            <h1 class="h2 mb-1">Compare specs</h1>
            <p class="text-muted mb-0">Search and compare up to three phones side by side.</p>
        </div>
        <a href="{{ route('devices.index') }}" class="btn btn-outline-secondary btn-sm">Browse phones</a>
    </div>

    <div class="compare-workspace mb-4">
        <div class="compare-slots">
            @for ($slot = 0; $slot < 3; $slot++)
                @php($device = $devices->get($slot))
                <section class="compare-slot" data-slot="{{ $slot }}" aria-label="Comparison slot {{ $slot + 1 }}">
                    <div class="compare-slot-search">
                        <label class="compare-search-label" for="compare-search-{{ $slot }}">Compare with</label>
                        <div class="position-relative">
                            <svg class="compare-search-icon" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                            <input
                                id="compare-search-{{ $slot }}"
                                type="search"
                                class="form-control compare-search-input"
                                placeholder="Search phone model..."
                                autocomplete="off"
                                data-slot="{{ $slot }}"
                                aria-label="Search phone for comparison slot {{ $slot + 1 }}"
                            >
                            <div class="compare-search-results" data-results-for="{{ $slot }}" role="listbox" aria-label="Phone search results"></div>
                        </div>
                    </div>

                    @if ($device)
                        <div class="compare-device-head">
                            <div class="compare-device-image-wrap">
                                @if ($device->image)
                                    <img
                                        src="{{ asset('storage/' . $device->image) }}"
                                        alt="{{ $device->brand->name }} {{ $device->name }}"
                                        class="compare-device-image"
                                        loading="eager"
                                    >
                                @else
                                    <div class="compare-device-image-placeholder">No image</div>
                                @endif
                            </div>
                            <div class="compare-device-copy">
                                <div class="small text-muted text-uppercase fw-semibold">{{ $device->brand->name }}</div>
                                <h2 class="compare-device-name">{{ $device->name }}</h2>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    @if ($device->release_date)
                                        <span class="text-muted small">{{ $device->release_date->format('Y') }}</span>
                                    @endif
                                    @if ($device->status)
                                        <span class="badge rounded-pill text-bg-light border text-capitalize">{{ $device->status }}</span>
                                    @endif
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-light border compare-remove" data-slot="{{ $slot }}" aria-label="Remove {{ $device->name }} from comparison">&times;</button>
                        </div>

                        <div class="compare-quick-grid">
                            @php
                                $quick = [
                                    'Screen' => $device->specs->first(fn ($s) => strcasecmp($s->spec_key, 'Screen Size') === 0)?->spec_value,
                                    'Camera' => $device->specs->first(fn ($s) => strcasecmp($s->spec_key, 'Main Camera') === 0)?->spec_value,
                                    'RAM' => $device->specs->first(fn ($s) => strcasecmp($s->spec_key, 'RAM') === 0)?->spec_value,
                                    'Battery' => $device->specs->first(fn ($s) => strcasecmp($s->spec_key, 'Capacity') === 0)?->spec_value,
                                ];
                            @endphp
                            @foreach ($quick as $label => $value)
                                <div class="compare-quick-item">
                                    <span class="compare-quick-label">{{ $label }}</span>
                                    <strong class="compare-quick-value">{{ $value ?: '—' }}</strong>
                                </div>
                            @endforeach
                        </div>

                        <div class="compare-device-actions">
                            <a href="{{ route('devices.show', $device) }}" class="btn btn-sm btn-outline-primary">Specifications</a>
                        </div>
                    @else
                        <div class="compare-empty-state">
                            <div class="compare-empty-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <rect x="7" y="3" width="10" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M10 18h4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <h2 class="h6 mb-1">Add a phone</h2>
                            <p class="text-muted small mb-0">Use the search box above to choose a device.</p>
                        </div>
                    @endif
                </section>
            @endfor
        </div>
    </div>

    @if ($devices->count() >= 2)
        <div class="compare-table-panel">
            <div class="compare-table-heading">
                <div>
                    <span class="text-primary small fw-semibold text-uppercase">Side by side</span>
                    <h2 class="h4 mb-1">Detailed specifications</h2>
                </div>
                <span class="text-muted small">{{ $devices->count() }} devices selected</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0 compare-spec-table">
                    <thead>
                        <tr>
                            <th class="compare-spec-name">Specification</th>
                            @foreach ($devices as $device)
                                <th>
                                    <div class="compare-table-device">{{ $device->brand->name }}</div>
                                    <div class="fw-semibold">{{ $device->name }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($groupedKeys as $category => $keys)
                            <tr class="compare-category-row">
                                <th colspan="{{ $devices->count() + 1 }}">{{ $category }}</th>
                            </tr>
                            @foreach ($keys as $key => $_)
                                @php
                                    $values = [];
                                    foreach ($devices as $compareDevice) {
                                        $values[] = $compareDevice->specs->first(fn ($specification) => $specification->category === $category && $specification->spec_key === $key)?->spec_value;
                                    }
                                    $normalizedValues = collect($values)->filter(fn ($value) => filled($value))->map(fn ($value) => trim((string) $value))->unique()->values();
                                    $hasDifference = $normalizedValues->count() > 1;
                                @endphp
                                <tr>
                                    <th class="compare-spec-name">{{ $key }}</th>
                                    @foreach ($values as $value)
                                        <td class="{{ $hasDifference ? 'compare-value-different' : '' }}">{{ $value ?: '—' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="compare-howto card content-card">
            <div class="card-body py-4">
                <div class="d-flex gap-3 align-items-start">
                    <div class="compare-howto-icon" aria-hidden="true">+</div>
                    <div>
                        <h2 class="h5 mb-1">Build your comparison</h2>
                        <p class="text-muted mb-0">Choose at least two phones above to unlock the detailed side-by-side specification table.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
(() => {
    const devices = @json($allDevices->map(fn ($device) => [
        'id' => $device->id,
        'name' => $device->name,
        'brand' => $device->brand?->name,
        'url' => route('devices.show', $device),
        'image' => $device->image ? asset('storage/' . $device->image) : null,
    ])->values());

    const selectedIds = @json($devices->pluck('id')->values());
    const compareUrl = @json(route('compare.index'));

    const buildUrl = (ids) => {
        const valid = ids.filter(Boolean).slice(0, 3);
        return valid.length ? `${compareUrl}?devices=${valid.join(',')}` : compareUrl;
    };

    const renderResults = (input, resultsBox) => {
        const query = input.value.trim().toLowerCase();
        const currentSlot = Number(input.dataset.slot);
        const blocked = selectedIds.filter((id, index) => index !== currentSlot);
        resultsBox.innerHTML = '';

        if (query.length < 2) {
            resultsBox.classList.remove('is-visible');
            return;
        }

        const results = devices
            .filter((device) => !blocked.includes(device.id))
            .filter((device) => `${device.brand} ${device.name}`.toLowerCase().includes(query))
            .slice(0, 8);

        if (!results.length) {
            resultsBox.innerHTML = '<div class="compare-search-empty">No phones found.</div>';
        } else {
            results.forEach((device) => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'compare-search-result';
                item.dataset.id = device.id;
                item.innerHTML = `
                    <span class="compare-result-thumb">
                        ${device.image ? `<img src="${device.image}" alt="">` : '<span>+</span>'}
                    </span>
                    <span class="compare-result-copy">
                        <strong>${escapeHtml(device.name)}</strong>
                        <small>${escapeHtml(device.brand || '')}</small>
                    </span>
                `;
                resultsBox.appendChild(item);
            });
        }

        resultsBox.classList.add('is-visible');
    };

    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    document.querySelectorAll('.compare-search-input').forEach((input) => {
        const resultsBox = document.querySelector(`[data-results-for="${input.dataset.slot}"]`);
        let timer;

        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => renderResults(input, resultsBox), 120);
        });

        input.addEventListener('focus', () => {
            if (input.value.trim().length >= 2) renderResults(input, resultsBox);
        });
    });

    document.addEventListener('click', (event) => {
        const result = event.target.closest('.compare-search-result');
        if (result) {
            const slot = Number(result.closest('.compare-search-results').dataset.resultsFor);
            selectedIds[slot] = Number(result.dataset.id);
            window.location.href = buildUrl(selectedIds);
            return;
        }

        const remove = event.target.closest('.compare-remove');
        if (remove) {
            const slot = Number(remove.dataset.slot);
            selectedIds[slot] = null;
            window.location.href = buildUrl(selectedIds);
            return;
        }

        if (!event.target.closest('.compare-slot-search')) {
            document.querySelectorAll('.compare-search-results').forEach((box) => box.classList.remove('is-visible'));
        }
    });
})();
</script>
@endsection
