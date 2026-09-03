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
    "name": "{{ $device->name }}",
    "brand": { "@@type": "Brand", "name": "{{ $device->brand->name }}" },
    "image": "{{ $device->image ? asset('storage/' . $device->image) : '' }}",
    "releaseDate": "{{ $device->release_date?->format('Y-m-d') }}"
}
</script>
@endsection

@section('content')

<div class="device-hero rounded p-4 mb-4">
    <div class="row align-items-center g-4">
        <div class="col-md-4 text-center">
            @if ($device->image)
                <img src="{{ asset('storage/' . $device->image) }}" alt="{{ $device->name }}" class="img-fluid device-hero-image">
            @else
                <div class="device-hero-image d-flex align-items-center justify-content-center">
                    <span class="text-white-50">No image</span>
                </div>
            @endif
        </div>
        <div class="col-md-8">
            <p class="text-uppercase small mb-1 opacity-75">{{ $device->brand->name }}</p>
            <h1 class="h2 mb-2">{{ $device->name }}</h1>
            <p class="mb-3 opacity-75">
                @if ($device->release_date)
                    Released {{ $device->release_date->format('F Y') }}
                @endif
                &middot; <span class="text-capitalize">{{ $device->status }}</span>
            </p>

            <div class="row g-2">
                @if ($quickSpecs['screen'])
                    <div class="col-6 col-md-3">
                        <div class="quick-spec-box">
                            <div class="quick-spec-label">Display</div>
                            <div class="quick-spec-value">{{ $quickSpecs['screen'] }}</div>
                        </div>
                    </div>
                @endif

                @if ($quickSpecs['camera'])
                    <div class="col-6 col-md-3">
                        <div class="quick-spec-box">
                            <div class="quick-spec-label">Camera</div>
                            <div class="quick-spec-value">{{ $quickSpecs['camera'] }}</div>
                        </div>
                    </div>
                @endif

                @if ($quickSpecs['ram'])
                    <div class="col-6 col-md-3">
                        <div class="quick-spec-box">
                            <div class="quick-spec-label">RAM</div>
                            <div class="quick-spec-value">{{ $quickSpecs['ram'] }}</div>
                        </div>
                    </div>
                @endif

                @if ($quickSpecs['battery'])
                    <div class="col-6 col-md-3">
                        <div class="quick-spec-box">
                            <div class="quick-spec-label">Battery</div>
                            <div class="quick-spec-value">{{ $quickSpecs['battery'] }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@foreach ($groupedSpecs as $category => $specs)
    <h4 class="mt-4">{{ $category }}</h4>
    <table class="table table-striped">
        <tbody>
            @foreach ($specs as $spec)
                <tr>
                    <th style="width: 30%">{{ $spec->spec_key }}</th>
                    <td>{{ $spec->spec_value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endforeach

@endsection