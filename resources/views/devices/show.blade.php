@extends('layouts.app')

@section('title', $device->name . ' - Full Specifications | VayoTech')
@section('meta_description', $device->name . ' specifications, memory configurations, release information, and detailed technical specifications on VayoTech.')
@section('og_title', $device->name . ' Specs')
@section('og_description', 'Full specifications for the ' . $device->name)

@if ($device->image)
    @section('og_image', asset('storage/' . $device->image))
@endif

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Product",
    "name": @json($device->name),
    "brand": {
        "@@type": "Brand",
        "name": @json($device->brand->name)
    },
    "image": @json($device->image ? asset('storage/' . $device->image) : ''),
    "releaseDate": @json($device->release_date?->format('Y-m-d'))
}
</script>
@endsection

@section('content')

<div id="top"></div>

<nav class="device-breadcrumb mb-3" aria-label="Breadcrumb">
    <a href="{{ route('devices.index') }}">Devices</a>
    <span>/</span>
    <a href="{{ route('brands.show', $device->brand) }}">{{ $device->brand->name }}</a>
    <span>/</span>
    <span aria-current="page">{{ $device->name }}</span>
</nav>

<section class="device-spec-hero mb-3">
    <header class="device-spec-hero-header">
        <h1>{{ $device->name }}</h1>
        <button
            type="button"
            class="device-share-button"
            data-share-device
            data-share-title="{{ $device->name }}"
            data-share-url="{{ route('devices.show', $device) }}"
            aria-label="Share {{ $device->name }}"
        >
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M18 8a3 3 0 1 0-2.83-4A3 3 0 0 0 15 5c0 .19.02.37.05.55l-6.1 3.36a3 3 0 1 0 0 6.18l6.1 3.36A3 3 0 1 0 16 16c-.03 0-.06 0-.09.01l-6.1-3.36c.12-.4.19-.82.19-1.25s-.07-.85-.19-1.25l6.1-3.36c.55.75 1.44 1.24 2.45 1.24Z" fill="currentColor"/>
            </svg>
            <span class="visually-hidden">Share</span>
        </button>
        <span class="device-share-status" data-share-status aria-live="polite"></span>
    </header>

    <div class="device-spec-hero-main">

        <div class="device-spec-image-panel">
            @if ($device->image)
                <img
                    src="{{ asset('storage/' . $device->image) }}"
                    alt="{{ $device->brand->name }} {{ $device->name }}"
                    class="device-spec-image"
                    loading="eager"
                >
            @else
                <div class="device-spec-image-placeholder">
                    No image
                </div>
            @endif
        </div>

        <div class="device-spec-hero-copy">

            <div class="device-spec-brand-row">
                <a href="{{ route('brands.show', $device->brand) }}">
                    {{ $device->brand->name }}
                </a>
                <span>/</span>
                <span>{{ ucfirst($device->status) }}</span>
            </div>

            <div class="device-fact-grid">
                @foreach ([
                    'release' => ['fa-calendar-days', 'Release', $device->release_date ? 'Released '.$device->release_date->format('F Y') : 'Date not available'],
                    'os' => ['fa-mobile-screen-button', 'OS', $quickSpecs['os']],
                    'storage' => ['fa-hard-drive', 'Storage', $quickSpecs['storage']],
                    'display' => ['fa-display', 'Display', $quickSpecs['display'] ?: $quickSpecs['screen']],
                    'chip' => ['fa-microchip', 'Chip', $quickSpecs['chip']],
                ] as [$factIcon, $factLabel, $factValue])
                    @if ($factValue)
                        <div class="device-fact">
                            <i class="fa-solid {{ $factIcon }}" aria-hidden="true"></i>
                            <div>
                                <span>{{ $factLabel }}</span>
                                <strong>{{ $factValue }}</strong>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="device-key-specs">

                @foreach([
                    'screen' => ['fa-mobile-screen', 'Screen size'],
                    'camera' => ['fa-camera', 'Camera'],
                    'ram' => ['fa-memory', 'RAM'],
                    'battery' => ['fa-battery-full', 'Battery'],
                ] as $key => $label)

                    @if ($quickSpecs[$key])
                        <div class="device-key-spec">
                            <i class="fa-solid {{ $label[0] }}" aria-hidden="true"></i>
                            <span>{{ $label[1] }}</span>
                            <strong>{{ $quickSpecs[$key] }}</strong>
                            @if ($key === 'screen' && $quickSpecs['display'])
                                <small>{{ $quickSpecs['display'] }}</small>
                            @elseif ($key === 'camera' && $quickSpecs['chip'])
                                <small>{{ $quickSpecs['chip'] }}</small>
                            @endif
                        </div>
                    @endif

                @endforeach

            </div>

        </div>

    </div>

    <nav class="device-section-nav" aria-label="Device sections">

        <a class="active" href="#top">
            <i class="fa-regular fa-eye" aria-hidden="true"></i>
            <span>HANDS-ON</span>
        </a>

        <a href="#specifications">
            <i class="fa-regular fa-comments" aria-hidden="true"></i>
            <span>OPINIONS</span>
        </a>

        <a href="{{ route('compare.index', ['devices' => $device->id]) }}">
            <i class="fa-solid fa-code-compare" aria-hidden="true"></i>
            <span>COMPARE</span>
        </a>

        <a href="#top">
            <i class="fa-regular fa-image" aria-hidden="true"></i>
            <span>PICTURES</span>
        </a>

        <a href="#memory">
            <i class="fa-solid fa-dollar-sign" aria-hidden="true"></i>
            <span>PRICES</span>
        </a>

    </nav>
</section>

@if ($relatedDevices->isNotEmpty())
    <section class="device-data-section mb-4" aria-labelledby="related-devices-title">
        <div class="device-section-heading mb-3">
            <div>
                <span>More from {{ $device->brand->name }}</span>
                <h2 id="related-devices-title">Related phones</h2>
            </div>
            <a href="{{ route('brands.show', $device->brand) }}" class="small text-decoration-none">View brand</a>
        </div>
        <div class="row g-2">
            @foreach ($relatedDevices as $relatedDevice)
                <div class="col-6 col-md-3">
                    @include('partials.device-card', ['device' => $relatedDevice])
                </div>
            @endforeach
        </div>
    </section>
@endif


@if ($device->variants->isNotEmpty())

    <section id="memory" class="device-data-section mb-4">

        <div class="device-section-heading">
            <div>
                <span>Memory</span>
                <h2>Memory configurations</h2>
            </div>

            <span class="text-muted small">
                {{ $device->variants->count() }} configurations
            </span>
        </div>

        <div class="table-responsive device-data-table-wrap">

            <table class="table align-middle mb-0 device-data-table">

                <thead>
                <tr>
                    <th scope="col">RAM</th>
                    <th scope="col">Storage</th>
                    <th scope="col">Storage type</th>

                    @if ($device->variants->contains(fn ($variant) => $variant->model_code || $variant->market))
                        <th scope="col">Model / market</th>
                    @endif

                    @if ($device->variants->contains(fn ($variant) => filled($variant->price)))
                        <th scope="col">Price</th>
                    @endif
                </tr>
                </thead>

                <tbody>

                @foreach ($device->variants as $variant)

                    <tr class="{{ $variant->is_default ? 'device-variant-default' : '' }}">

                        <td class="fw-semibold">
                            {{ $variant->ram }}

                            @if ($variant->is_default)
                                <span class="badge text-bg-light border ms-1">
                                    Base
                                </span>
                            @endif
                        </td>

                        <td>{{ $variant->storage }}</td>

                        <td>{{ $variant->storage_type ?: '—' }}</td>

                        @if ($device->variants->contains(fn ($item) => $item->model_code || $item->market))
                            <td>
                                {{ $variant->model_code ?: '—' }}

                                @if ($variant->market)
                                    <span class="text-muted">
                                        ({{ $variant->market }})
                                    </span>
                                @endif
                            </td>
                        @endif

                        @if ($device->variants->contains(fn ($item) => filled($item->price)))
                            <td>
                                @if ($variant->price !== null)
                                    {{ $variant->currency }} {{ number_format((float) $variant->price, 2) }}
                                @else
                                    —
                                @endif
                            </td>
                        @endif

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    </section>

@endif


<section id="specifications" class="device-data-section">

    <div class="device-section-heading mb-3">
        <div>
            <span>Specifications</span>
            <h2>Technical specifications</h2>
        </div>

        <a href="#top" class="small text-decoration-none">
            Back to top
        </a>
    </div>


    @if ($groupedSpecs->isNotEmpty())

        <nav class="device-spec-tabs mb-3" aria-label="Specification categories">

            @foreach ($groupedSpecs as $category => $specs)

                @php
                    $categorySlug = \Illuminate\Support\Str::slug($category);
                @endphp

                <a href="#spec-{{ $categorySlug }}">
                    {{ $category }}
                </a>

            @endforeach

        </nav>


        <div class="device-spec-groups">

            @foreach ($groupedSpecs as $category => $specs)

                @php
                    $categorySlug = \Illuminate\Support\Str::slug($category);
                @endphp

                <section
                    id="spec-{{ $categorySlug }}"
                    class="device-spec-group"
                >

                    <div class="device-spec-group-title">
                        <h3>{{ $category }}</h3>
                    </div>

                    <div class="table-responsive">

                        <table class="table align-middle mb-0 device-spec-table">

                            <tbody>

                            @foreach ($specs as $spec)

                                <tr>

                                    <th scope="row">
                                        {{ $spec->spec_key }}
                                    </th>

                                    <td>
                                        {{ $spec->spec_value }}
                                    </td>

                                </tr>

                            @endforeach

                            </tbody>

                        </table>

                    </div>

                </section>

            @endforeach

        </div>

    @else

        <div class="device-empty-specs">
            <h3>Specifications are not available yet.</h3>
            <p class="text-muted mb-0">
                More technical information will be added when it has been verified.
            </p>
        </div>

    @endif

</section>

@endsection
