@extends('layouts.app')

@section('title', 'Explore Phone Brands | VayoTech')
@section('meta_description', 'Explore smartphone brands on VayoTech and browse phones by manufacturer, with device counts and direct brand pages.')
@section('canonical', route('brands.index'))

@section('content')
<div class="explore-brands-page">
    <header class="explore-brands-hero">
        <div>
            <div class="small text-muted mb-1">Discover phones</div>
            <h1>Explore brands</h1>
            <p class="mb-0 text-muted">Browse smartphone makers and jump directly to their phones, specifications, and latest devices.</p>
        </div>
        <a href="{{ route('devices.index') }}" class="btn btn-outline-dark btn-sm">Browse all phones</a>
    </header>

    <section class="explore-brands-panel" aria-labelledby="brands-heading">
        <div class="explore-brands-heading">
            <div>
                <div class="small text-muted">Phone brands</div>
                <h2 id="brands-heading" class="h5 mb-0">Choose a brand</h2>
            </div>
            <span class="small text-muted">{{ $brands->count() }} {{ Str::plural('brand', $brands->count()) }}</span>
        </div>

        @if ($brands->isEmpty())
            <div class="alert alert-light border mb-0">No brands are available yet.</div>
        @else
            <div class="brand-card-grid">
                @foreach ($brands as $brand)
                    <x-brand-card :brand="$brand" show-count />
                @endforeach
            </div>
        @endif
    </section>

    <section class="explore-brands-discovery">
        <div>
            <div class="small text-muted">Not sure what to choose?</div>
            <h2 class="h5 mb-1">Let phone finder narrow it down</h2>
            <p class="text-muted mb-0">Use your priorities to find phones that fit the way you use them.</p>
        </div>
        <a href="{{ route('devices.index') }}" class="btn btn-dark btn-sm">Open phone finder</a>
    </section>
</div>
@endsection

@push('styles')
<style>
    .explore-brands-page {
        display: grid;
        gap: 18px;
    }

    .explore-brands-hero {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 18px;
        padding: 28px 30px;
        background: #fff;
        border: 1px solid var(--phonespecs-border, #dee2e6);
    }

    .explore-brands-hero h1 {
        margin: 0 0 8px;
        font-size: clamp(2rem, 4vw, 3rem);
        line-height: 1.05;
    }

    .explore-brands-hero p {
        max-width: 680px;
    }

    .explore-brands-panel {
        padding: 18px;
        background: #f8f9fa;
        border: 1px solid var(--phonespecs-border, #dee2e6);
    }

    .explore-brands-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .explore-brands-discovery {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 18px 20px;
        background: #fff;
        border: 1px solid var(--phonespecs-border, #dee2e6);
    }

    @media (max-width: 767.98px) {
        .explore-brands-hero,
        .explore-brands-discovery {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>
@endpush
