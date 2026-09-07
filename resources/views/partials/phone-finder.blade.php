@php
    $phoneFinderBrands = \App\Models\Brand::query()
        ->orderBy('name')
        ->get(['id', 'name', 'slug', 'logo', 'brand_domain']);
@endphp

<div class="card content-card mb-3">
    <div class="card-header bg-dark text-white">
        <strong class="sidebar-title">Phone finder</strong>
    </div>

    <div class="list-group list-group-flush phone-finder-list">
        @forelse ($phoneFinderBrands as $brand)
            <a
                href="{{ route('brands.show', $brand) }}"
                class="list-group-item list-group-item-action"
            >
                <div class="d-flex align-items-center gap-2">
                    @if($brand->brandfetch_logo_url)
                        <img
                            src="{{ $brand->brandfetch_logo_url }}"
                            alt="{{ $brand->name }}"
                            width="28"
                            height="28"
                            loading="lazy"
                            class="phone-brand-logo"
                        >
                    @elseif($brand->logo)
                        <img
                            src="{{ asset('storage/' . $brand->logo) }}"
                            alt="{{ $brand->name }}"
                            width="28"
                            height="28"
                            loading="lazy"
                            class="phone-brand-logo"
                        >
                    @else
                        <span
                            class="phone-brand-logo d-inline-flex align-items-center justify-content-center bg-light border"
                            style="width:28px;height:28px;font-size:.75rem;"
                            aria-hidden="true"
                        >
                            {{ mb_strtoupper(mb_substr($brand->name, 0, 1)) }}
                        </span>
                    @endif

                    <span>{{ $brand->name }}</span>
                </div>
            </a>
        @empty
            <div class="list-group-item text-muted small">
                No brands available.
            </div>
        @endforelse
    </div>

    <div class="card-footer">
        <div class="row g-2" aria-label="Explore phone brands">
            <div class="col-6">
                <a
                    href="{{ route('brands.index') }}"
                    class="btn btn-sm btn-outline-dark w-100"
                >
                    All Brand
                </a>
            </div>
            <div class="col-6">
                <a
                    href="{{ route('brands.index', ['filter' => 'rumored']) }}"
                    class="btn btn-sm btn-outline-dark w-100"
                >
                    Rumored
                </a>
            </div>
        </div>
    </div>
</div>

@yield('sidebar_extra')
