@extends('layouts.app')

@section('title', 'VayoTech - Compare Phones & Read the Latest Tech News')
@section('meta_description', 'Compare smartphone specifications, browse the latest devices, explore phone brands, and read mobile technology news.')
@section('canonical', route('home'))

@section('sidebar_extra')
    @if ($comingSoon->isNotEmpty())
        <div class="card content-card mb-3">
            <div class="card-header bg-primary text-white">
                <strong class="sidebar-title">Coming soon</strong>
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
                            </article>
                        </div>
                    </a>
                @endforeach
            </div>
        </article>
    @endif

    <div class="card content-card">
        <div class="card-header"><strong class="sidebar-title">Useful links</strong></div>
        <div class="list-group list-group-flush">
            <a href="{{ route('devices.index', ['status' => 'available']) }}" class="list-group-item list-group-item-action">Available phones</a>
            <a href="{{ route('devices.index', ['status' => 'rumored']) }}" class="list-group-item list-group-item-action">Upcoming phones</a>
            <a href="{{ route('compare.index') }}" class="list-group-item list-group-item-action">Compare devices</a>
        </div>
    </div>
@endsection

@section('content')

<section class="home-section">
    <div class="section-heading">
        <div>
            <span class="section-kicker">Devices</span>
            <h1 class="section-title">Latest devices</h1>
        </div>
        <a href="{{ route('devices.index') }}" class="btn btn-outline-primary btn-sm">Browse all phones</a>
    </div>

    <div class="row g-3">
        @foreach ($latestDevices as $device)
            <div class="col-6 col-md-3">
                @include('partials.device-card', ['device' => $device])
            </div>
        @endforeach
    </div>
</section>

<section class="home-section home-section-news">
    <div class="section-heading">
        <div>
            <span class="section-kicker">News</span>
            <h2 class="section-title">Latest news</h2>
        </div>
        <a href="{{ route('news.index') }}" class="btn btn-outline-secondary btn-sm">View all news</a>
    </div>

    <div class="row g-3">
        @foreach ($latestNews as $post)
            <div class="col-md-6">
                <article class="card news-preview-card h-100">
                    <div class="card-body">
                        <div class="news-preview-date">{{ $post->published_at->format('M d, Y') }}</div>
                        <h3 class="h5 card-title">
                            <a href="{{ route('news.show', $post) }}" class="text-decoration-none text-dark">
                                {{ $post->title }}
                            </a>
                        </h3>
                        <p class="card-text text-muted mb-0">{{ Str::limit(strip_tags($post->body), 140) }}</p>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0">
                        <a href="{{ route('news.show', $post) }}" class="small fw-semibold text-decoration-none">Read story</a>
                    </div>
                </article>
            </div>
        @endforeach
    </div>
</section>

@endsection
