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
            <a href="{{ route('brands.show', $brand) }}" class="list-group-item list-group-item-action">
                <div class="d-flex align-items-center gap-2">
                    @if($brand->brandfetch_logo_url)
                        <img src="{{ $brand->brandfetch_logo_url }}" alt="{{ $brand->name }}" width="28" height="28" loading="lazy" class="phone-brand-logo">
                    @elseif($brand->logo)
                        <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" width="28" height="28" loading="lazy" class="phone-brand-logo">
                    @else
                        <span class="phone-brand-logo d-inline-flex align-items-center justify-content-center bg-light border" style="width:28px;height:28px;font-size:.75rem;" aria-hidden="true">
                            {{ mb_strtoupper(mb_substr($brand->name, 0, 1)) }}
                        </span>
                    @endif
                    <span>{{ $brand->name }}</span>
                </div>
            </a>
        @empty
            <div class="list-group-item text-muted small">No brands available.</div>
        @endforelse
    </div>

    <div class="card-footer p-2">
        <div class="phone-finder-brand-buttons" aria-label="Explore phone brands">
            <a href="{{ route('brands.index') }}" class="phone-finder-brand-button {{ request()->routeIs('brands.index') && request('filter', 'all') === 'all' ? 'is-active' : '' }}" @if(request()->routeIs('brands.index') && request('filter', 'all') === 'all') aria-current="page" @endif>All Brand</a>
            <a href="{{ route('brands.index', ['filter' => 'rumored']) }}" class="phone-finder-brand-button {{ request()->routeIs('brands.index') && request('filter') === 'rumored' ? 'is-active' : '' }}" @if(request()->routeIs('brands.index') && request('filter') === 'rumored') aria-current="page" @endif>Rumored</a>
        </div>
    </div>
</div>

@yield('sidebar_extra')

@push('styles')
<style>
    .phone-finder-brand-buttons {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 4px;
    }

    .phone-finder-brand-button {
        display: flex;
        align-items: center;
        min-height: 42px;
        padding: 8px 12px;
        background: #fff;
        border: 1px solid #dee2e6;
        color: #343a40;
        font-size: .875rem;
        line-height: 1.2;
        text-decoration: none;
        transition: background-color .15s ease, border-color .15s ease, color .15s ease;
    }

    .phone-finder-brand-button:hover {
        background: #f8f9fa;
        border-color: #dee2e6;
        color: #212529;
        text-decoration: none;
    }

    .phone-finder-brand-button.is-active {
        background: #f8f9fa;
        border-color: #dee2e6;
        color: #212529;
        font-weight: 500;
    }
</style>
@endpush
