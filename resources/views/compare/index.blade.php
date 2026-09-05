@extends('layouts.app')

@section('title', 'Compare Phones - PhoneSpecs')
@section('meta_description', 'Compare up to three smartphones side by side with quick specs and detailed specifications.')

@section('content')
<style>
    .compare-page {
        --compare-border: #e2e6ea;
        --compare-muted: #6c757d;
        --compare-surface: #fff;
        --compare-soft: #f8f9fa;
    }

    .compare-workspace {
        background: var(--compare-surface);
        border: 1px solid var(--compare-border);
        border-radius: .5rem;
        box-shadow: 0 1px 2px rgba(33, 37, 41, .04);
        overflow: hidden;
    }

    .compare-slots {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .compare-slot {
        min-width: 0;
        min-height: 430px;
        padding: 1rem;
        border-right: 1px solid var(--compare-border);
        display: flex;
        flex-direction: column;
    }

    .compare-slot:last-child {
        border-right: 0;
    }

    .compare-slot-search {
        margin-bottom: 1rem;
    }

    .compare-search-label {
        display: block;
        margin-bottom: .45rem;
        color: var(--compare-muted);
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .compare-search-input {
        padding-left: 2.25rem;
        border-color: #ced4da;
        min-height: 44px;
    }

    .compare-search-input:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .1);
    }

    .compare-search-icon {
        position: absolute;
        left: .8rem;
        top: 50%;
        width: 18px;
        height: 18px;
        transform: translateY(-50%);
        color: #6c757d;
        pointer-events: none;
        z-index: 2;
    }

    .compare-search-results {
        position: absolute;
        top: calc(100% + .35rem);
        left: 0;
        right: 0;
        z-index: 30;
        display: none;
        overflow: hidden;
        background: #fff;
        border: 1px solid var(--compare-border);
        border-radius: .4rem;
        box-shadow: 0 8px 24px rgba(33, 37, 41, .12);
    }

    .compare-search-results.is-visible {
        display: block;
    }

    .compare-search-result {
        width: 100%;
        display: flex;
        gap: .7rem;
        align-items: center;
        padding: .65rem .75rem;
        border: 0;
        border-bottom: 1px solid #f0f1f2;
        background: #fff;
        text-align: left;
    }

    .compare-search-result:last-child {
        border-bottom: 0;
    }

    .compare-search-result:hover,
    .compare-search-result:focus-visible {
        background: #f8f9fa;
        outline: 0;
    }

    .compare-result-thumb {
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 1px solid var(--compare-border);
        border-radius: .35rem;
        background: #f8f9fa;
        color: #adb5bd;
    }

    .compare-result-thumb img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .compare-result-copy {
        min-width: 0;
        display: flex;
        flex-direction: column;
    }

    .compare-result-copy strong {
        font-size: .88rem;
        color: #212529;
    }

    .compare-result-copy small {
        color: var(--compare-muted);
    }

    .compare-search-empty {
        padding: .8rem;
        color: var(--compare-muted);
        font-size: .85rem;
    }

    .compare-device-head {
        position: relative;
        display: grid;
        grid-template-columns: 92px minmax(0, 1fr) auto;
        gap: .8rem;
        align-items: center;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--compare-border);
    }

    .compare-device-image-wrap {
        width: 92px;
        height: 116px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #edf0f2;
        border-radius: .45rem;
        background: var(--compare-soft);
    }

    .compare-device-image {
        max-width: 100%;
        max-height: 100%;
        padding: .45rem;
        object-fit: contain;
    }

    .compare-device-image-placeholder {
        font-size: .7rem;
        color: var(--compare-muted);
    }

    .compare-device-copy {
        min-width: 0;
    }

    .compare-device-name {
        margin: .15rem 0 .35rem;
        font-size: 1.05rem;
        font-weight: 700;
        line-height: 1.25;
    }

    .compare-remove {
        align-self: start;
        line-height: 1;
        font-size: 1.15rem;
        padding: .25rem .5rem;
    }

    .compare-quick-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin-top: 1rem;
        border-top: 1px solid #edf0f2;
        border-left: 1px solid #edf0f2;
    }

    .compare-quick-item {
        min-width: 0;
        padding: .65rem .6rem;
        border-right: 1px solid #edf0f2;
        border-bottom: 1px solid #edf0f2;
    }

    .compare-quick-label {
        display: block;
        margin-bottom: .15rem;
        color: var(--compare-muted);
        font-size: .64rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .compare-quick-value {
        display: block;
        font-size: .76rem;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .compare-device-actions {
        margin-top: auto;
        padding-top: 1rem;
    }

    .compare-empty-state {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 2rem 1rem;
        color: #495057;
    }

    .compare-empty-icon {
        width: 76px;
        height: 108px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        color: #adb5bd;
        border: 2px dashed #ced4da;
        border-radius: 12px;
    }

    .compare-empty-icon svg {
        width: 42px;
        height: 42px;
    }

    .compare-table-panel {
        overflow: hidden;
        background: #fff;
        border: 1px solid var(--compare-border);
        border-radius: .5rem;
        box-shadow: 0 1px 2px rgba(33, 37, 41, .04);
    }

    .compare-table-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1rem .85rem;
        border-bottom: 1px solid var(--compare-border);
    }

    .compare-spec-table {
        --bs-table-border-color: #e9ecef;
    }

    .compare-spec-table th,
    .compare-spec-table td {
        min-width: 170px;
        padding: .8rem .85rem;
        vertical-align: middle;
    }

    .compare-spec-table thead th {
        background: #f8f9fa;
        border-bottom: 1px solid var(--compare-border);
    }

    .compare-spec-table .compare-spec-name {
        min-width: 155px;
        position: sticky;
        left: 0;
        z-index: 3;
        background: #fff;
    }

    .compare-spec-table thead .compare-spec-name {
        background: #f8f9fa;
    }

    .compare-category-row th {
        background: #212529;
        color: #fff;
        font-size: .76rem;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        padding-top: .62rem;
        padding-bottom: .62rem;
    }

    .compare-table-device {
        color: #6c757d;
        font-size: .7rem;
        font-weight: 600;
        margin-bottom: .1rem;
        text-transform: uppercase;
    }

    .compare-value-different {
        background: rgba(13, 110, 253, .045);
    }

    .compare-howto {
        border-color: var(--compare-border);
    }

    .compare-howto-icon {
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed #adb5bd;
        border-radius: .4rem;
        color: #6c757d;
        font-size: 1.25rem;
    }

    @media (max-width: 991.98px) {
        .compare-slots {
            grid-template-columns: 1fr;
        }

        .compare-slot {
            min-height: 330px;
            border-right: 0;
            border-bottom: 1px solid var(--compare-border);
        }

        .compare-slot:last-child {
            border-bottom: 0;
        }
    }

    @media (max-width: 575.98px) {
        .compare-slot {
            padding: .85rem;
        }

        .compare-device-head {
            grid-template-columns: 78px minmax(0, 1fr) auto;
            gap: .65rem;
        }

        .compare-device-image-wrap {
            width: 78px;
            height: 102px;
        }

        .compare-device-name {
            font-size: .95rem;
        }

        .compare-table-heading {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>

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

    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

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
