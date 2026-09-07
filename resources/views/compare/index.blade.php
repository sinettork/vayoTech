@extends('layouts.app')

@section('title', 'Compare Phones - PhoneSpecs')
@section('meta_description', 'Compare up to three smartphones side by side with quick specs and detailed specifications.')

@section('content')
<div class="compare-page">
    <header class="compare-banner mb-3">
        <div>
            <div class="compare-banner-kicker">Phone tools</div>
            <h1>Compare specs</h1>
        </div>
        <a href="{{ route('devices.index') }}" class="btn btn-light btn-sm">Browse phones</a>
    </header>

    <section class="compare-workspace mb-4" aria-label="Phone comparison workspace">
        <div class="compare-slots">
            @for ($slot = 0; $slot < 3; $slot++)
                <section class="compare-slot" data-slot="{{ $slot }}" aria-label="Comparison slot {{ $slot + 1 }}">
                    <div class="compare-slot-search">
                        <label class="compare-search-label" for="compare-search-{{ $slot }}">Phone {{ $slot + 1 }}</label>
                        <div class="position-relative">
                            <svg class="compare-search-icon" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
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
                            <div
                                class="compare-search-results"
                                data-results-for="{{ $slot }}"
                                role="listbox"
                                aria-label="Phone search results"
                            ></div>
                        </div>
                    </div>

                    @if ($devices->get($slot))
                        <div class="compare-device-head">
                            <div class="compare-device-image-wrap">
                                @if ($devices->get($slot)->image)
                                    <img
                                        src="{{ asset('storage/' . $devices->get($slot)->image) }}"
                                        alt="{{ $devices->get($slot)->brand->name }} {{ $devices->get($slot)->name }}"
                                        class="compare-device-image"
                                        loading="eager"
                                    >
                                @else
                                    <div class="compare-device-image-placeholder">No image</div>
                                @endif
                            </div>

                            <div class="compare-device-copy">
                                <div class="small text-muted fw-semibold">{{ $devices->get($slot)->brand->name }}</div>
                                <h2 class="compare-device-name">{{ $devices->get($slot)->name }}</h2>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    @if ($devices->get($slot)->release_date)
                                        <span class="text-muted small">{{ substr((string) $devices->get($slot)->release_date, 0, 4) }}</span>
                                    @endif
                                    @if ($devices->get($slot)->status)
                                        <span class="badge text-bg-light border text-capitalize">{{ $devices->get($slot)->status }}</span>
                                    @endif
                                </div>
                            </div>

                            <button
                                type="button"
                                class="btn btn-sm btn-light border compare-remove"
                                data-slot="{{ $slot }}"
                                aria-label="Remove phone from comparison"
                            >&times;</button>
                        </div>

                        <div class="compare-quick-grid">
                            <div class="compare-quick-item">
                                <span class="compare-quick-label">Screen</span>
                                <strong class="compare-quick-value">{{ $quickSpecs->get($slot)['screen'] ?? '—' }}</strong>
                            </div>
                            <div class="compare-quick-item">
                                <span class="compare-quick-label">Camera</span>
                                <strong class="compare-quick-value">{{ $quickSpecs->get($slot)['camera'] ?? '—' }}</strong>
                            </div>
                            <div class="compare-quick-item">
                                <span class="compare-quick-label">RAM</span>
                                <strong class="compare-quick-value">{{ $quickSpecs->get($slot)['ram'] ?? '—' }}</strong>
                            </div>
                            <div class="compare-quick-item">
                                <span class="compare-quick-label">Battery</span>
                                <strong class="compare-quick-value">{{ $quickSpecs->get($slot)['battery'] ?? '—' }}</strong>
                            </div>
                        </div>

                        <div class="compare-device-actions">
                            <a href="{{ route('devices.show', $devices->get($slot)) }}" class="btn btn-sm btn-outline-primary">View specifications</a>
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
                        </div>
                    @endif
                </section>
            @endfor
        </div>
    </section>

    @if ($devices->count() >= 2)
        <section class="compare-table-panel" aria-label="Detailed specifications comparison">
            <div class="compare-table-heading">
                <div>
                    <span class="text-primary small fw-semibold">Side by side</span>
                    <h2 class="h4 mb-1">Detailed specifications</h2>
                </div>
                <div class="compare-table-tools">
                    <span class="text-muted small">{{ $devices->count() }} phones selected</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="compare-differences-toggle" aria-pressed="false">
                        Show differences only
                    </button>
                </div>
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
                        @foreach ($compareRows as $row)
                            @if ($loop->first || $row['category'] !== $compareRows[$loop->index - 1]['category'])
                                <tr class="compare-category-row">
                                    <th colspan="{{ $devices->count() + 1 }}">{{ $row['category'] }}</th>
                                </tr>
                            @endif
                            <tr class="{{ $row['has_difference'] ? 'compare-difference-row' : 'compare-same-row' }}">
                                <th class="compare-spec-name">{{ $row['key'] }}</th>
                                @foreach ($row['values'] as $value)
                                    <td class="{{ $row['has_difference'] ? 'compare-value-different' : '' }}">{{ $value ?: '—' }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
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
@endsection

@push('scripts')
<script>
(() => {
    const differencesToggle = document.getElementById('compare-differences-toggle');
    differencesToggle?.addEventListener('click', () => {
        const showingDifferences = differencesToggle.getAttribute('aria-pressed') === 'true';
        const nextState = !showingDifferences;
        differencesToggle.setAttribute('aria-pressed', String(nextState));
        differencesToggle.classList.toggle('active', nextState);
        differencesToggle.textContent = nextState ? 'Show all specifications' : 'Show differences only';
        document.querySelectorAll('.compare-same-row').forEach((row) => {
            row.classList.toggle('d-none', nextState);
        });
    });

    const selectedIds = @json($devices->pluck('id')->values());
    const compareUrl = @json(route('compare.index'));
    const searchUrl = @json(route('devices.search'));

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

        fetch(`${searchUrl}?q=${encodeURIComponent(query)}`, {
            headers: { 'Accept': 'application/json' },
        })
            .then((response) => {
                if (!response.ok) throw new Error('Phone search failed.');
                return response.json();
            })
            .then((results) => {
                const available = results.filter((device) => !blocked.includes(device.id));

                if (!available.length) {
                    resultsBox.innerHTML = '<div class="compare-search-empty">No phones found.</div>';
                    resultsBox.classList.add('is-visible');
                    return;
                }

                available.forEach((device) => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'compare-search-result';
                item.dataset.id = device.id;
                item.innerHTML = `
                    <span class="compare-result-thumb">
                        ${device.image ? `<img src="${escapeHtml(device.image)}" alt="">` : '<span>+</span>'}
                    </span>
                    <span class="compare-result-copy">
                        <strong>${escapeHtml(device.name)}</strong>
                        <small>${escapeHtml(device.brand || '')}</small>
                    </span>
                `;
                resultsBox.appendChild(item);
                });
                resultsBox.classList.add('is-visible');
            })
            .catch(() => {
                resultsBox.innerHTML = '<div class="compare-search-empty">Search is unavailable. Try again.</div>';
                resultsBox.classList.add('is-visible');
            });
    };

    document.querySelectorAll('.compare-search-input').forEach((input) => {
        const resultsBox = document.querySelector(`[data-results-for="${input.dataset.slot}"]`);
        let timer;

        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => renderResults(input, resultsBox), 120);
        });

        input.addEventListener('focus', () => {
            if (input.value.trim().length >= 2) {
                renderResults(input, resultsBox);
            }
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
@endpush
