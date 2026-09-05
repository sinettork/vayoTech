@extends('layouts.app')

@section('title', $brand->name . ' Phones - Specs & Prices | VayoTech')
@section('meta_description', 'Browse ' . $brand->name . ' phones with specifications, release dates, comparisons, and the latest device information.')
@section('canonical', route('brands.show', $brand))

@section('content')
<div class="brand-directory-page">
    <div class="brand-directory-layout">
        <aside class="brand-directory-sidebar" aria-label="Brand navigation">
            <section class="brand-navigation-panel">
                <div class="brand-sidebar-heading">
                    <div class="small text-muted mb-1">Explore brands</div>
                    <h2 class="h6 mb-0">Phone brands</h2>
                </div>

                <div class="brand-grid">
                    @foreach ($brands as $navBrand)
                        <a
                            href="{{ route('brands.show', $navBrand) }}"
                            class="brand-grid-item {{ $navBrand->is($brand) ? 'active' : '' }}"
                            aria-current="{{ $navBrand->is($brand) ? 'page' : 'false' }}"
                        >
                            <span class="brand-grid-logo">
                                @if($navBrand->brandfetch_logo_url)
                                    <img src="{{ $navBrand->brandfetch_logo_url }}" alt="{{ $navBrand->name }}" loading="lazy">
                                @elseif($navBrand->logo)
                                    <img src="{{ asset('storage/' . $navBrand->logo) }}" alt="{{ $navBrand->name }}" loading="lazy">
                                @else
                                    <span>{{ substr($navBrand->name, 0, 1) }}</span>
                                @endif
                            </span>
                            <span class="brand-grid-name">{{ $navBrand->name }}</span>
                        </a>
                    @endforeach
                </div>
            </section>

            @if ($editorialPost)
                <section class="brand-editorial-panel">
                    <div class="brand-sidebar-heading">
                        <div class="small text-muted mb-1">Latest editorial</div>
                        <h2 class="h6 mb-0">{{ $brand->name }} news</h2>
                    </div>

                    <a href="{{ route('news.show', $editorialPost) }}" class="brand-editorial-card">
                        @if ($editorialPost->image)
                            <img
                                src="{{ asset('storage/' . $editorialPost->image) }}"
                                alt="{{ $editorialPost->title }}"
                                loading="lazy"
                            >
                        @else
                            <div class="brand-editorial-fallback"></div>
                        @endif
                        <div class="brand-editorial-overlay">
                            <span class="brand-editorial-kicker">{{ $brand->name }}</span>
                            <h3>{{ $editorialPost->title }}</h3>
                            @if ($editorialPost->published_at)
                                <time datetime="{{ $editorialPost->published_at->toIso8601String() }}">
                                    {{ $editorialPost->published_at->format('M j, Y') }}
                                </time>
                            @endif
                        </div>
                    </a>
                </section>
            @endif

            <div class="brand-sidebar-summary">
                <strong>{{ $deviceCount }}</strong>
                <span>{{ Str::plural('device', $deviceCount) }} in the catalog</span>
            </div>
        </aside>

        <main class="brand-directory-main">
            <header
                class="brand-hero-banner"
                style="@if($heroDevice && $heroDevice->image) background-image: linear-gradient(90deg, rgba(15, 23, 42, .90) 0%, rgba(15, 23, 42, .62) 48%, rgba(15, 23, 42, .12) 100%), url('{{ asset('storage/' . $heroDevice->image) }}'); @endif"
            >
                <div class="brand-hero-content">
                    <div class="brand-hero-kicker">Phone brand</div>
                    <h1>{{ $brand->name }} phones</h1>
                    <p>
                        Browse {{ $deviceCount }} {{ Str::plural('device', $deviceCount) }} with quick filters, release sorting, and detailed specifications.
                    </p>
                    <div class="brand-hero-actions">
                        <a href="{{ route('compare.index') }}" class="btn btn-light btn-sm">Compare devices</a>
                        <a href="{{ route('news.index') }}" class="btn btn-outline-light btn-sm">{{ $brand->name }} news</a>
                    </div>
                </div>
            </header>

            <div class="brand-action-bar">
                <div class="brand-action-group">
                    <a href="{{ route('compare.index') }}" class="brand-action-link">Compare</a>
                    <a href="{{ route('news.index') }}" class="brand-action-link">{{ $brand->name }} news</a>
                </div>

                <div class="brand-sort-group" aria-label="Sort devices">
                    <span class="brand-sort-label">Sort by</span>
                    <a
                        href="{{ route('brands.show', array_filter([$brand->slug, 'sort' => 'new'])) }}"
                        class="brand-sort-link {{ $sort === 'new' ? 'active' : '' }}"
                    >
                        Time of release
                    </a>
                    <a
                        href="{{ route('brands.show', array_filter([$brand->slug, 'sort' => 'popular'])) }}"
                        class="brand-sort-link {{ $sort === 'popular' ? 'active' : '' }}"
                    >
                        Popularity
                    </a>
                </div>
            </div>

            <section class="brand-filter-panel" aria-label="Quick filters">
                <div class="brand-filter-header">
                    <div>
                        <div class="small text-muted">Quick filters</div>
                        <div class="fw-semibold">Find the hardware you need</div>
                    </div>
                    <span class="small text-muted">{{ $devices->total() }} results</span>
                </div>

                <div class="brand-filter-list">
                    @php
                        $filterParams = request()->except('page');
                    @endphp

                    @foreach ([
                        'card_slot' => 'Card slot',
                        'jack_3_5mm' => '3.5mm jack',
                        'esim' => 'eSIM',
                        'foldable' => 'Foldables',
                    ] as $filterKey => $filterLabel)
                        @php
                            $filterQuery = $filterParams;
                            $isActive = request()->boolean($filterKey);

                            if ($isActive) {
                                unset($filterQuery[$filterKey]);
                            } else {
                                $filterQuery[$filterKey] = 1;
                            }
                        @endphp

                        <a
                            href="{{ route('brands.show', [$brand->slug] + $filterQuery) }}"
                            class="brand-filter-chip {{ $isActive ? 'active' : '' }}"
                        >
                            {{ $filterLabel }}
                        </a>
                    @endforeach

                    <a href="#advanced-filters" class="brand-filter-more">More filters</a>
                </div>
            </section>

            <section class="brand-catalog-section" id="advanced-filters">
                <div class="brand-catalog-heading">
                    <div>
                        <div class="small text-muted">Catalog</div>
                        <h2 class="h5 mb-0">{{ $brand->name }} devices</h2>
                    </div>
                    <div class="small text-muted">
                        Showing {{ $devices->firstItem() ?? 0 }}–{{ $devices->lastItem() ?? 0 }} of {{ $devices->total() }}
                    </div>
                </div>

                @if ($devices->isEmpty())
                    <div class="brand-empty-state">
                        <h3 class="h6 mb-2">No devices match these filters</h3>
                        <p class="text-muted mb-3">Try removing one or more quick filters.</p>
                        <a href="{{ route('brands.show', $brand) }}" class="btn btn-outline-secondary btn-sm">Reset filters</a>
                    </div>
                @else
                    <div class="brand-device-grid">
                        @foreach ($devices as $device)
                            <article class="brand-device-card">
                                <a href="{{ route('devices.show', $device) }}" class="brand-device-link">
                                    <div class="brand-device-image-wrap">
                                        @if ($device->image)
                                            <img
                                                src="{{ asset('storage/' . $device->image) }}"
                                                alt="{{ $device->name }}"
                                                class="brand-device-image"
                                                loading="lazy"
                                            >
                                        @else
                                            <div class="brand-device-image-placeholder">No image</div>
                                        @endif
                                    </div>
                                    <div class="brand-device-info">
                                        <h3>{{ $device->name }}</h3>
                                        <div class="brand-device-meta">
                                            @if ($device->release_date)
                                                {{ $device->release_date->format('Y') }}
                                            @endif
                                            @if ($device->status)
                                                <span aria-hidden="true">·</span>
                                                {{ ucfirst($device->status) }}
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $devices->links() }}
                    </div>
                @endif
            </section>
        </main>
    </div>
</div>
@endsection

@push('styles')
<style>
    .brand-directory-layout {
        display: grid;
        grid-template-columns: 250px minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }

    .brand-directory-sidebar {
        position: sticky;
        top: 1rem;
        display: grid;
        gap: 14px;
    }

    .brand-navigation-panel,
    .brand-editorial-panel,
    .brand-sidebar-summary,
    .brand-filter-panel {
        background: #fff;
        border: 1px solid var(--phonespecs-border);
    }

    .brand-sidebar-heading {
        padding: 14px 14px 12px;
        border-bottom: 1px solid var(--phonespecs-border);
    }

    .brand-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        background: #f1f3f5;
    }

    .brand-grid-item {
        min-height: 70px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 4px;
        border-right: 1px solid #fff;
        border-bottom: 1px solid #fff;
        color: #495057;
        text-align: center;
        text-decoration: none;
        font-size: .72rem;
        transition: background .15s ease, color .15s ease;
    }

    .brand-grid-item:hover,
    .brand-grid-item.active {
        background: #fff;
        color: #0d6efd;
    }

    .brand-grid-logo {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 28px;
        background: #fff;
        border: 1px solid #e9ecef;
        overflow: hidden;
        color: #212529;
        font-weight: 700;
        font-size: .8rem;
    }

    .brand-grid-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 4px;
    }

    .brand-grid-name {
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .brand-editorial-card {
        position: relative;
        display: block;
        overflow: hidden;
        min-height: 230px;
        color: #fff;
        text-decoration: none;
        background: #212529;
    }

    .brand-editorial-card img,
    .brand-editorial-fallback {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .brand-editorial-fallback {
        background: linear-gradient(145deg, #1f2937, #111827);
    }

    .brand-editorial-card::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0, 0, 0, .02) 20%, rgba(0, 0, 0, .82) 100%);
    }

    .brand-editorial-overlay {
        position: absolute;
        inset: auto 14px 14px;
        z-index: 1;
    }

    .brand-editorial-kicker {
        display: block;
        font-size: .7rem;
        margin-bottom: 4px;
        opacity: .8;
    }

    .brand-editorial-overlay h3 {
        margin: 0 0 6px;
        font-size: .95rem;
        line-height: 1.35;
    }

    .brand-editorial-overlay time {
        font-size: .72rem;
        opacity: .78;
    }

    .brand-sidebar-summary {
        display: flex;
        flex-direction: column;
        gap: 2px;
        padding: 12px 14px;
    }

    .brand-sidebar-summary strong {
        font-size: 1.1rem;
    }

    .brand-sidebar-summary span {
        color: var(--phonespecs-muted);
        font-size: .78rem;
    }

    .brand-hero-banner {
        min-height: 310px;
        display: flex;
        align-items: end;
        padding: 30px;
        color: #fff;
        background-color: #1f2937;
        background-repeat: no-repeat;
        background-position: center;
        background-size: cover;
        border: 1px solid rgba(0, 0, 0, .08);
    }

    .brand-hero-content {
        max-width: 600px;
    }

    .brand-hero-kicker {
        margin-bottom: 5px;
        font-size: .8rem;
        opacity: .78;
    }

    .brand-hero-banner h1 {
        margin: 0 0 8px;
        font-size: clamp(1.8rem, 4vw, 3rem);
        line-height: 1.05;
    }

    .brand-hero-banner p {
        max-width: 560px;
        margin: 0;
        color: rgba(255, 255, 255, .78);
        line-height: 1.6;
    }

    .brand-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 16px;
    }

    .brand-action-bar {
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 0 6px;
        min-height: 46px;
        background: #212529;
        color: #fff;
    }

    .brand-action-group,
    .brand-sort-group {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }

    .brand-action-link,
    .brand-sort-link,
    .brand-sort-label {
        display: inline-flex;
        align-items: center;
        min-height: 46px;
        padding: 0 12px;
        color: rgba(255, 255, 255, .74);
        font-size: .78rem;
        text-decoration: none;
    }

    .brand-action-link:hover,
    .brand-sort-link:hover {
        color: #fff;
    }

    .brand-sort-label {
        color: rgba(255, 255, 255, .48);
        padding-left: 4px;
        padding-right: 4px;
    }

    .brand-sort-link.active {
        background: #dc3545;
        color: #fff;
    }

    .brand-filter-panel {
        margin-top: 12px;
    }

    .brand-filter-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 13px 14px 8px;
    }

    .brand-filter-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 0 14px 14px;
    }

    .brand-filter-chip,
    .brand-filter-more {
        display: inline-flex;
        align-items: center;
        min-height: 34px;
        padding: 0 11px;
        border: 1px solid #ced4da;
        background: #fff;
        color: #495057;
        font-size: .78rem;
        text-decoration: none;
        border-radius: 2px;
    }

    .brand-filter-chip:hover,
    .brand-filter-more:hover {
        border-color: #adb5bd;
        color: #212529;
    }

    .brand-filter-chip.active {
        border-color: #0d6efd;
        background: #0d6efd;
        color: #fff;
    }

    .brand-filter-more {
        margin-left: auto;
        background: #f8f9fa;
    }

    .brand-catalog-section {
        margin-top: 18px;
    }

    .brand-catalog-heading {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
    }

    .brand-device-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 20px;
    }

    .brand-device-card {
        min-width: 0;
    }

    .brand-device-link {
        display: block;
        color: inherit;
        text-decoration: none;
    }

    .brand-device-image-wrap {
        height: 210px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
    }

    .brand-device-image {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 6px;
        transition: transform .15s ease;
    }

    .brand-device-link:hover .brand-device-image {
        transform: translateY(-3px);
    }

    .brand-device-image-placeholder {
        color: #868e96;
        font-size: .75rem;
        text-align: center;
    }

    .brand-device-info {
        padding: 8px 2px 0;
        text-align: center;
    }

    .brand-device-info h3 {
        margin: 0;
        font-size: .88rem;
        line-height: 1.35;
    }

    .brand-device-meta {
        margin-top: 3px;
        color: var(--phonespecs-muted);
        font-size: .72rem;
    }

    .brand-empty-state {
        padding: 46px 20px;
        border: 1px solid var(--phonespecs-border);
        background: #fff;
        text-align: center;
    }

    @media (max-width: 1199.98px) {
        .brand-device-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        .brand-directory-layout {
            grid-template-columns: 1fr;
        }

        .brand-directory-sidebar {
            position: static;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        }

        .brand-navigation-panel {
            grid-row: span 2;
        }

        .brand-editorial-panel,
        .brand-sidebar-summary {
            align-self: start;
        }
    }

    @media (max-width: 767.98px) {
        .brand-directory-sidebar {
            display: block;
        }

        .brand-editorial-panel,
        .brand-sidebar-summary {
            margin-top: 12px;
        }

        .brand-hero-banner {
            min-height: 270px;
            padding: 22px;
        }

        .brand-action-bar {
            align-items: stretch;
            flex-direction: column;
            gap: 0;
            padding: 0;
        }

        .brand-action-group,
        .brand-sort-group {
            overflow-x: auto;
            flex-wrap: nowrap;
        }

        .brand-action-link,
        .brand-sort-link,
        .brand-sort-label {
            white-space: nowrap;
        }

        .brand-sort-label {
            padding-left: 12px;
        }

        .brand-filter-list {
            flex-wrap: nowrap;
            overflow-x: auto;
        }

        .brand-filter-chip,
        .brand-filter-more {
            white-space: nowrap;
            flex: 0 0 auto;
        }

        .brand-filter-more {
            margin-left: 0;
        }

        .brand-catalog-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .brand-device-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px 14px;
        }

        .brand-device-image-wrap {
            height: 180px;
        }
    }
</style>
@endpush
