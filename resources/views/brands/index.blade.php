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
        <a href="{{ route('devices.index') }}" class="btn btn-outline-dark btn-sm">
            <i class="fa-solid fa-mobile-screen-button me-1" aria-hidden="true"></i>
            Browse all phones
        </a>
    </header>

    <section class="explore-brands-panel" aria-labelledby="brands-heading">
        <div class="explore-brands-heading">
            <div>
                <div class="small text-muted">PHONE BRANDS</div>
                <h2 id="brands-heading" class="h5 mb-0">Choose a brand</h2>
            </div>
            <span class="small text-muted">{{ $brands->count() }} {{ Str::plural('brand', $brands->count()) }}</span>
        </div>

        <div class="brands-toolbar">
            <div class="brand-filter-buttons" aria-label="Brand filter">
            <a
                href="{{ route('brands.index') }}"
                class="btn btn-sm {{ $filter === 'all' ? 'btn-dark' : 'btn-outline-dark' }}"
                @if($filter === 'all') aria-current="page" @endif
            >
                <i class="fa-solid fa-layer-group me-1" aria-hidden="true"></i>
                All Brand
            </a>
            <a
                href="{{ route('brands.index', ['filter' => 'rumored']) }}"
                class="btn btn-sm {{ $filter === 'rumored' ? 'btn-dark' : 'btn-outline-dark' }}"
                @if($filter === 'rumored') aria-current="page" @endif
            >
                <i class="fa-solid fa-clock-rotate-left me-1" aria-hidden="true"></i>
                Rumored
            </a>
            </div>
            <label class="brand-search">
                <span class="visually-hidden">Search brands</span>
                <input type="search" class="form-control form-control-sm" id="brand-search" placeholder="Search brands..." autocomplete="off">
            </label>
        </div>

        @if ($brands->isEmpty())
            <div class="alert alert-light border mb-0">
                {{ $filter === 'rumored' ? 'No brands have rumored phones yet.' : 'No brands are available yet.' }}
            </div>
        @else
            <div class="brand-card-grid">
                @foreach ($brands as $brand)
                    <div class="brand-card-item" data-brand-name="{{ strtolower($brand->name) }}">
                        <x-brand-card :brand="$brand" show-count />
                    </div>
                @endforeach
            </div>
            <div id="brand-search-empty" class="alert alert-light border mt-3 mb-0 d-none">No brands match your search.</div>
        @endif
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

    .brand-filter-buttons {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        max-width: 360px;
    }

    .brand-filter-buttons .btn {
        width: 100%;
    }

    .brands-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .brand-search {
        width: min(260px, 100%);
    }

    .brand-card-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
    }

    @media (max-width: 991.98px) {
        .brand-card-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .explore-brands-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .brand-filter-buttons {
            max-width: none;
        }

        .brands-toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        .brand-search {
            width: 100%;
        }

        .brand-card-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 479.98px) {
        .brand-card-grid {
            grid-template-columns: 1fr;
        }
    }

</style>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('brand-search');
    const empty = document.getElementById('brand-search-empty');
    const cards = [...document.querySelectorAll('.brand-card-item')];

    input?.addEventListener('input', () => {
        const query = input.value.trim().toLowerCase();
        let visible = 0;

        cards.forEach((card) => {
            const matches = card.dataset.brandName.includes(query);
            card.classList.toggle('d-none', !matches);
            visible += matches ? 1 : 0;
        });

        empty?.classList.toggle('d-none', visible !== 0);
    });
});
</script>
@endpush
