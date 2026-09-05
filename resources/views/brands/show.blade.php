@extends('layouts.app')

@section('title', $brand->name . ' Phones - Specs & Prices | PhoneSpecs')
@section('meta_description', 'Browse all ' . $brand->name . ' phones with full specifications, release dates, and comparisons.')
@section('canonical', route('brands.show', $brand))

@section('content')

<div class="brand-hero border mb-4">
    <div class="row g-0 align-items-stretch">
        <div class="col-lg-8">
            <div class="brand-hero-main h-100 d-flex align-items-center gap-3 p-4 p-lg-5">
                <div class="brand-hero-logo flex-shrink-0">
                    @if($brand->brandfetch_logo_url)
                        <img
                            src="{{ $brand->brandfetch_logo_url }}"
                            alt="{{ $brand->name }}"
                            width="76"
                            height="76"
                            loading="eager"
                        >
                    @elseif($brand->logo)
                        <img
                            src="{{ asset('storage/' . $brand->logo) }}"
                            alt="{{ $brand->name }}"
                            width="76"
                            height="76"
                        >
                    @else
                        <span>{{ strtoupper(substr($brand->name, 0, 1)) }}</span>
                    @endif
                </div>

                <div>
                    <div class="small text-white-50 text-uppercase fw-semibold mb-1">
                        Phone Brand
                    </div>
                    <h1 class="display-6 fw-semibold mb-1">
                        {{ $brand->name }} Phones
                    </h1>
                    <p class="text-white-50 mb-0">
                        {{ $deviceCount }} {{ Str::plural('device', $deviceCount) }} in the catalog
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="brand-hero-side h-100 p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="small text-uppercase text-muted fw-semibold mb-2">Browse catalog</div>
                    <p class="small text-muted mb-0">
                        Explore {{ $brand->name }} devices by availability and release status.
                    </p>
                </div>

                <a href="{{ route('compare.index') }}" class="btn btn-dark btn-sm align-self-start mt-3">
                    Compare Devices
                </a>
            </div>
        </div>
    </div>
</div>

<div class="brand-toolbar border bg-white mb-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 p-3">
        <div class="small text-muted">
            Showing {{ $devices->firstItem() ?? 0 }}–{{ $devices->lastItem() ?? 0 }} of {{ $devices->total() }} devices
        </div>

        <nav class="d-flex flex-wrap gap-1" aria-label="Brand device filters">
            <a
                href="{{ route('brands.show', $brand) }}"
                class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-secondary' }}"
            >
                All
            </a>
            <a
                href="{{ route('brands.show', [$brand, 'status' => 'available']) }}"
                class="btn btn-sm {{ request('status') === 'available' ? 'btn-dark' : 'btn-outline-secondary' }}"
            >
                Available
            </a>
            <a
                href="{{ route('brands.show', [$brand, 'status' => 'rumored']) }}"
                class="btn btn-sm {{ request('status') === 'rumored' ? 'btn-dark' : 'btn-outline-secondary' }}"
            >
                Rumored
            </a>
            <a
                href="{{ route('brands.show', [$brand, 'status' => 'discontinued']) }}"
                class="btn btn-sm {{ request('status') === 'discontinued' ? 'btn-dark' : 'btn-outline-secondary' }}"
            >
                Discontinued
            </a>
        </nav>
    </div>
</div>

@if ($devices->isEmpty())
    <div class="content-card border bg-white p-5 text-center">
        <h2 class="h5 mb-2">No devices found</h2>
        <p class="text-muted mb-0">There are no {{ $brand->name }} devices matching this filter.</p>
    </div>
@else
    <div class="row g-3">
        @foreach ($devices as $device)
            <div class="col-6 col-md-4 col-lg-3">
                @include('partials.device-card', ['device' => $device])
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $devices->links() }}
    </div>
@endif

@endsection

@push('styles')
<style>
    .brand-hero {
        background: #fff;
        border-color: var(--phonespecs-border) !important;
    }

    .brand-hero-main {
        background: #212529;
        color: #fff;
    }

    .brand-hero-logo {
        width: 76px;
        height: 76px;
        flex: 0 0 76px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        border: 1px solid rgba(255, 255, 255, .18);
        overflow: hidden;
    }

    .brand-hero-logo img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 10px;
    }

    .brand-hero-logo span {
        color: #212529;
        font-size: 1.75rem;
        font-weight: 700;
    }

    .brand-hero-side {
        border-left: 1px solid var(--phonespecs-border);
        background: #f8f9fa;
    }

    .brand-toolbar {
        border-color: var(--phonespecs-border) !important;
    }

    @media (max-width: 991.98px) {
        .brand-hero-side {
            border-left: 0;
            border-top: 1px solid var(--phonespecs-border);
        }
    }
</style>
@endpush
