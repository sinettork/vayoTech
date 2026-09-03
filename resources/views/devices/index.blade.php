@extends('layouts.app')

@section('title', 'All Devices')
@section('meta_description', 'Browse and compare phone specifications from the leading mobile brands.')
@section('canonical', route('devices.index', request()->only('brand')))

@section('content')
@php($selectedBrand = $brands->firstWhere('slug', request('brand')))
<div class="mb-4">
    <span class="text-primary small fw-semibold text-uppercase">Device directory</span>
    <h1 class="mb-1">{{ $selectedBrand ? $selectedBrand->name . ' Phones' : 'All Devices' }}</h1>
    @if ($selectedBrand && $selectedBrand->description)
        <p class="text-muted mb-0">{{ $selectedBrand->description }}</p>
    @else
        <p class="text-muted mb-0">Browse specifications, release dates, and availability across leading brands.</p>
    @endif
</div>

<form method="GET" action="{{ route('devices.index') }}" class="card content-card card-body mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label" for="brand">Brand</label>
            <select class="form-select" id="brand" name="brand">
                <option value="">All brands</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->slug }}" @selected(request('brand') === $brand->slug)>{{ $brand->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-5">
            <label class="form-label" for="status">Availability</label>
            <select class="form-select" id="status" name="status">
                <option value="">All statuses</option>
                @foreach (['available' => 'Available', 'rumored' => 'Rumored', 'discontinued' => 'Discontinued'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary flex-grow-1" type="submit">Filter</button>
            <a class="btn btn-outline-secondary" href="{{ route('devices.index') }}">Reset</a>
        </div>
    </div>
</form>

<div class="row g-2">
    @forelse ($devices as $device)
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card device-card h-100">
                @if ($device->image)
                    <img src="{{ asset('storage/' . $device->image) }}" class="device-image" alt="{{ $device->name }}" loading="lazy">
                @else
                    <div class="device-image"><span class="text-muted small">No image</span></div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $device->name }}</h5>
                    <p class="text-muted small mb-2">{{ $device->brand->name }} @if ($device->release_date) &middot; {{ $device->release_date->format('M Y') }} @endif</p>
                    <span class="badge text-bg-light border mb-3">{{ ucfirst($device->status) }}</span>
                    <a href="{{ route('devices.show', $device) }}" class="btn btn-primary btn-sm">View Specs</a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="alert alert-info mb-4">No devices match these filters.</div></div>
    @endforelse
</div>

{{ $devices->links() }}
@endsection
