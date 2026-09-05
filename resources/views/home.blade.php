@extends('layouts.app')

@section('title', 'PhoneSpecs - Compare Phones & Read the Latest Tech News')
@section('meta_description', 'Compare smartphone specifications, browse the latest devices, and read mobile technology news.')
@section('canonical', route('home'))

@section('content')

<div class="row g-3">
    <aside class="col-lg-3 mb-4">
        <div class="card content-card mb-3">
            <div class="card-header bg-dark text-white">
                <strong class="sidebar-title">Phone Finder</strong>
            </div>
            <div class="list-group list-group-flush">
                @foreach ($brands as $brand)
                    <a href="{{ route('brands.show', $brand) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex align-items-center gap-2">
                            @if($brand->brandfetch_logo_url)
                                <img
                                    src="{{ $brand->brandfetch_logo_url }}"
                                    alt="{{ $brand->name }}"
                                    width="28"
                                    height="28"
                                    loading="lazy"
                                    style="object-fit:contain;background:#fff;"
                                >
                            @elseif($brand->logo)
                                <img
                                    src="{{ asset('storage/' . $brand->logo) }}"
                                    alt="{{ $brand->name }}"
                                    width="28"
                                    height="28"
                                    loading="lazy"
                                    style="object-fit:contain;background:#fff;"
                                >
                            @endif
                            <span>{{ $brand->name }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('devices.index') }}" class="btn btn-sm btn-outline-dark">All Brands</a>
            </div>
        </div>

        @if ($comingSoon->isNotEmpty())
            <div class="card content-card mb-3">
                <div class="card-header bg-primary text-white">
                    <strong class="sidebar-title">Coming Soon</strong>
                </div>
                <div class="list-group list-group-flush">
                    @foreach ($comingSoon as $device)
                        <a href="{{ route('devices.show', $device) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex align-items-center gap-2">
                                @if ($device->image)
                                    <img src="{{ asset('storage/' . $device->image) }}" alt="{{ $device->name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                @endif
                                <div>
                                    <div class="fw-semibold small">{{ $device->name }}</div>
                                    <div class="text-muted small">{{ $device->brand->name }}</div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="card content-card">
            <div class="card-header"><strong class="sidebar-title">Useful links</strong></div>
            <div class="list-group list-group-flush">
                <a href="{{ route('devices.index', ['status' => 'available']) }}" class="list-group-item list-group-item-action">Available phones</a>
                <a href="{{ route('devices.index', ['status' => 'rumored']) }}" class="list-group-item list-group-item-action">Upcoming phones</a>
                <a href="{{ route('compare.index') }}" class="list-group-item list-group-item-action">Compare devices</a>
            </div>
        </div>
    </aside>

    <section class="col-lg-9">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <span class="text-primary small fw-semibold text-uppercase">Fresh arrivals</span>
                <h1 class="h2 mb-0">Latest Devices</h1>
            </div>
            <a href="{{ route('devices.index') }}" class="btn btn-outline-primary btn-sm">Browse all</a>
        </div>

        <div class="row g-2 mb-5">
            @foreach ($latestDevices as $device)
                <div class="col-6 col-md-3">
                    @include('partials.device-card', ['device' => $device])
                </div>
            @endforeach
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="h3 mb-0">Latest News</h2>
            <a href="{{ route('news.index') }}" class="btn btn-outline-secondary btn-sm">All news</a>
        </div>

        <div class="row g-2">
            @foreach ($latestNews as $post)
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ route('news.show', $post) }}" class="text-decoration-none text-dark">
                                    {{ $post->title }}
                                </a>
                            </h5>
                            <p class="text-muted small">{{ $post->published_at->format('M d, Y') }}</p>
                            <p class="card-text">{{ Str::limit(strip_tags($post->body), 100) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>

@endsection
