@extends('layouts.app')

@section('title', 'All Phones')
@section('meta_description', 'Browse and compare phone specifications from the leading mobile brands.')
@section('canonical', route('devices.index', request()->only('brand')))

@section('content')
@php($selectedBrand = $brands->firstWhere('slug', request('brand')))
<div class="mb-3">
    <span class="text-primary small fw-semibold">PHONE DIRECTORY</span>
    <h1 class="mb-1">{{ $selectedBrand ? $selectedBrand->name . ' Phones' : 'All Phones' }}</h1>
    @if ($selectedBrand && $selectedBrand->description)
        <p class="text-muted mb-0">{{ $selectedBrand->description }}</p>
    @else
        <p class="text-muted mb-0">Find phones by brand, status, or model.</p>
    @endif
</div>

<nav class="device-directory-tabs mb-3" aria-label="Phone status">
    @foreach ([
        '' => 'All phones',
        'available' => 'Available',
        'rumored' => 'Rumored',
        'discontinued' => 'Discontinued',
    ] as $value => $label)
        <a href="{{ route('devices.index', array_filter(['brand' => request('brand'), 'status' => $value])) }}" class="{{ $status === ($value ?: null) ? 'active' : '' }}">{{ $label }}</a>
    @endforeach
</nav>

<form method="GET" action="{{ route('devices.index') }}" class="card content-card card-body mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label" for="device-search">Search</label>
            <input class="form-control" id="device-search" name="q" value="{{ $search }}" placeholder="Search phones..." type="search">
        </div>
        <div class="col-md-5">
            <label class="form-label" for="brand">Brand</label>
            <select class="form-select" id="brand" name="brand">
                <option value="">All brands</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->slug }}" @selected(request('brand') === $brand->slug)>{{ $brand->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="status">Availability</label>
            <select class="form-select" id="status" name="status">
                <option value="">All statuses</option>
                @foreach (['available' => 'Available', 'rumored' => 'Rumored', 'discontinued' => 'Discontinued'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12 d-flex flex-wrap gap-2">
            <button class="btn btn-primary flex-grow-1" type="submit">Filter</button>
            <a class="btn btn-outline-secondary" href="{{ route('devices.index') }}">Reset</a>
        </div>
    </div>
</form>

<div class="row g-2">
    @forelse ($devices as $device)
        <div class="col-6 col-md-4 col-xl-3">
            @include('partials.device-card', ['device' => $device])
        </div>
    @empty
        <div class="col-12"><div class="alert alert-info mb-4">No devices match these filters.</div></div>
    @endforelse
</div>

{{ $devices->links() }}
@endsection
