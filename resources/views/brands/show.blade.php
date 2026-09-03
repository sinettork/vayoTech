@extends('layouts.app')

@section('title', $brand->name . ' Phones - Specs & Prices | PhoneSpecs')
@section('meta_description', 'Browse all ' . $brand->name . ' phones with full specifications, release dates, and comparisons.')
@section('canonical', route('brands.show', $brand))

@section('content')

{{-- Brand banner --}}
<div class="bg-dark text-white rounded p-4 mb-4 d-flex align-items-center justify-content-between">
    <div>
        <h1 class="h2 mb-1">{{ $brand->name }} Phones</h1>
        <p class="mb-0 text-white-50">{{ $deviceCount }} {{ Str::plural('device', $deviceCount) }} listed</p>
    </div>
    <a href="{{ route('compare.index') }}" class="btn btn-outline-light btn-sm">Compare Devices</a>
</div>

<div class="row">
    {{-- Sidebar filters --}}
    <aside class="col-lg-3 mb-4">
        <div class="card">
            <div class="card-header"><strong>Filter</strong></div>
            <div class="list-group list-group-flush">
                <a href="{{ route('brands.show', $brand) }}"
                   class="list-group-item list-group-item-action {{ !request('status') ? 'active' : '' }}">
                    All
                </a>
                <a href="{{ route('brands.show', $brand) }}?status=available"
                   class="list-group-item list-group-item-action {{ request('status') === 'available' ? 'active' : '' }}">
                    Available
                </a>
                <a href="{{ route('brands.show', $brand) }}?status=rumored"
                   class="list-group-item list-group-item-action {{ request('status') === 'rumored' ? 'active' : '' }}">
                    Upcoming / Rumored
                </a>
                <a href="{{ route('brands.show', $brand) }}?status=discontinued"
                   class="list-group-item list-group-item-action {{ request('status') === 'discontinued' ? 'active' : '' }}">
                    Discontinued
                </a>
            </div>
        </div>
    </aside>

    {{-- Device grid --}}
    <section class="col-lg-9">
        @if ($devices->isEmpty())
            <p class="text-muted">No devices found for this filter.</p>
        @else
            <div class="row g-3">
                @foreach ($devices as $device)
                    <div class="col-6 col-md-3">
                        <div class="card device-card h-100">
                            @if ($device->image)
                                <img src="{{ asset('storage/' . $device->image) }}" class="device-image" alt="{{ $device->name }}" loading="lazy">
                            @else
                                <div class="device-image d-flex align-items-center justify-content-center">
                                    <span class="text-muted small">No image</span>
                                </div>
                            @endif
                            <div class="card-body p-2">
                                <h2 class="h6 card-title mb-1">{{ $device->name }}</h2>
                                <p class="text-muted small mb-2">
                                    @if ($device->release_date) {{ $device->release_date->format('Y') }} @endif
                                </p>
                                <a href="{{ route('devices.show', $device) }}" class="btn btn-sm btn-primary w-100">View Specs</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $devices->links() }}
            </div>
        @endif
    </section>
</div>

@endsection