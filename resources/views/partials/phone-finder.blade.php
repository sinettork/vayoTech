@php
    $phoneFinderBrands = \App\Models\Brand::query()
        ->orderBy('name')
        ->get(['id', 'name', 'slug', 'logo', 'brand_domain'])
        ->unique('slug')
        ->values();
    $popularBrandSlugs = ['samsung', 'apple', 'xiaomi', 'google', 'oneplus', 'oppo'];
    $popularBrands = $phoneFinderBrands
        ->filter(fn ($brand): bool => in_array($brand->slug, $popularBrandSlugs, true))
        ->sortBy(fn ($brand): int => array_search($brand->slug, $popularBrandSlugs, true))
        ->values();
    $otherBrands = $phoneFinderBrands
        ->reject(fn ($brand): bool => in_array($brand->slug, $popularBrandSlugs, true))
        ->values();
@endphp

<div class="card content-card phone-finder-card mb-3">
    <div class="card-header bg-dark text-white">
        <strong class="sidebar-title d-block">PHONE FINDER</strong>
    </div>

    @if ($popularBrands->isNotEmpty())
        <div class="phone-finder-section-label">POPULAR BRANDS</div>
        <div class="list-group list-group-flush phone-finder-list">
            @foreach ($popularBrands as $brand)
                <a href="{{ route('brands.show', $brand) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex align-items-center gap-2">
                        @if($brand->brandfetch_logo_url)
                            <img src="{{ $brand->brandfetch_logo_url }}" alt="{{ $brand->name }}" width="28" height="28" loading="lazy" class="phone-brand-logo">
                        @elseif($brand->logo)
                            <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" width="28" height="28" loading="lazy" class="phone-brand-logo">
                        @else
                            <span class="phone-brand-logo d-inline-flex align-items-center justify-content-center bg-light" style="width:28px;height:28px;font-size:.75rem;" aria-hidden="true">
                                {{ mb_strtoupper(mb_substr($brand->name, 0, 1)) }}
                            </span>
                        @endif
                        <span>{{ $brand->name }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <div class="phone-finder-section-label">MORE BRANDS</div>
    <div class="list-group list-group-flush phone-finder-list">
        @forelse ($otherBrands as $brand)
            <a href="{{ route('brands.show', $brand) }}" class="list-group-item list-group-item-action">
                <div class="d-flex align-items-center gap-2">
                    @if($brand->brandfetch_logo_url)
                        <img src="{{ $brand->brandfetch_logo_url }}" alt="{{ $brand->name }}" width="28" height="28" loading="lazy" class="phone-brand-logo">
                    @elseif($brand->logo)
                        <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" width="28" height="28" loading="lazy" class="phone-brand-logo">
                    @else
                        <span class="phone-brand-logo d-inline-flex align-items-center justify-content-center bg-light" style="width:28px;height:28px;font-size:.75rem;" aria-hidden="true">
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

    <div class="phone-finder-list list-group list-group-flush" aria-label="Explore phone brands">
        <a href="{{ route('brands.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('brands.index') && request('filter', 'all') === 'all' ? 'active' : '' }}" @if(request()->routeIs('brands.index') && request('filter', 'all') === 'all') aria-current="page" @endif>ALL BRAND</a>
        <a href="{{ route('brands.index', ['filter' => 'rumored']) }}" class="list-group-item list-group-item-action {{ request()->routeIs('brands.index') && request('filter') === 'rumored' ? 'active' : '' }}" @if(request()->routeIs('brands.index') && request('filter') === 'rumored') aria-current="page" @endif>RUMORED</a>
    </div>
</div>

@yield('sidebar_extra')
