@extends('layouts.app')

@section('title', $device->name . ' - Full Specifications | PhoneSpecs')
@section('meta_description', $device->name . ' specifications: display, camera, battery, and more. Compare with other devices on PhoneSpecs.')
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

<div class="mb-3 small">
    <a
        href="{{ route('devices.index') }}"
        class="text-decoration-none"
    >
        Devices
    </a>

    <span class="text-muted mx-1">/</span>

    <a
        href="{{ route('brands.show', $device->brand) }}"
        class="text-decoration-none"
    >
        {{ $device->brand->name }}
    </a>

    <span class="text-muted mx-1">/</span>

    <span class="text-muted">
        {{ $device->name }}
    </span>
</div>


<div class="device-hero rounded p-4 mb-4">

    <div class="row align-items-center g-4">

        <div class="col-md-4 text-center">

            @if ($device->image)

                <img
                    src="{{ asset('storage/' . $device->image) }}"
                    alt="{{ $device->name }}"
                    class="img-fluid device-hero-image"
                    loading="eager"
                >

            @else

                <div class="device-hero-image d-flex align-items-center justify-content-center">
                    <span class="text-white-50">
                        No image
                    </span>
                </div>

            @endif

        </div>


        <div class="col-md-8">

            <div class="d-flex align-items-center gap-2 mb-2">

                <a
                    href="{{ route('brands.show', $device->brand) }}"
                    class="text-white text-decoration-none small"
                >
                    {{ $device->brand->name }}
                </a>

                <span class="text-white-50">/</span>

                <span class="text-white-50 small">
                    {{ ucfirst($device->status) }}
                </span>

            </div>


            <h1 class="h2 mb-2">
                {{ $device->name }}
            </h1>


            <p class="mb-3 opacity-75">

                @if ($device->release_date)
                    Released {{ $device->release_date->format('F Y') }}
                @else
                    Release date not available
                @endif

            </p>


            <div class="d-flex flex-wrap gap-2 mb-4">

                <a
                    href="{{ route('compare.index', ['devices' => $device->id]) }}"
                    class="btn btn-light btn-sm"
                >
                    Compare this device
                </a>

                <a
                    href="{{ route('brands.show', $device->brand) }}"
                    class="btn btn-outline-light btn-sm"
                >
                    More {{ $device->brand->name }} phones
                </a>

            </div>


            <div class="row g-2">

                @foreach([
                    'screen' => 'Display',
                    'camera' => 'Camera',
                    'ram' => 'RAM',
                    'battery' => 'Battery',
                ] as $key => $label)

                    @if ($quickSpecs[$key])

                        <div class="col-6 col-md-3">

                            <div class="quick-spec-box">

                                <div class="quick-spec-label">
                                    {{ $label }}
                                </div>

                                <div class="quick-spec-value">
                                    {{ $quickSpecs[$key] }}
                                </div>

                            </div>

                        </div>

                    @endif

                @endforeach

            </div>

        </div>

    </div>

</div>


@if ($groupedSpecs->isNotEmpty())

    <div class="d-flex flex-wrap gap-2 mb-4">

        @foreach ($groupedSpecs as $category => $specs)

            <a
                href="#spec-{{ IlluminateSupportStr::slug($category) }}"
                class="btn btn-sm btn-outline-secondary"
            >
                {{ $category }}
            </a>

        @endforeach

    </div>


    @foreach ($groupedSpecs as $category => $specs)

        <section
            id="spec-{{ IlluminateSupportStr::slug($category) }}"
            class="specification-section"
        >

            <div class="d-flex align-items-end justify-content-between gap-3 mb-2">

                <div>

                    <div class="small text-muted text-uppercase">
                        Specifications
                    </div>

                    <h2 class="h5 mb-0">
                        {{ $category }}
                    </h2>

                </div>

                <a
                    href="#top"
                    class="small text-decoration-none"
                >
                    Back to top
                </a>

            </div>


            <div class="table-responsive">

                <table class="table table-striped align-middle bg-white">

                    <tbody>

                    @foreach ($specs as $spec)

                        <tr>

                            <th
                                scope="row"
                                style="width: 30%;"
                            >
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

@else

    <div class="text-center bg-white border p-5">

        <h2 class="h5">
            Specifications are not available yet.
        </h2>

        <p class="text-muted mb-0">
            More information will be added when available.
        </p>

    </div>

@endif

@endsection
