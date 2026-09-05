@extends('admin.layouts.app')

@section('title', 'Devices')

@section('content')

<div class="mb-4">
    <div class="sidebar-title mb-2">Catalog</div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
        <div>
            <h1 class="h3 mb-1">Devices</h1>
            <p class="text-muted mb-0">
                Manage phone models and their specifications.
            </p>
        </div>

        <a href="{{ route('admin.devices.create') }}" class="btn btn-dark">
            Add Device
        </a>
    </div>
</div>

<form method="GET" action="{{ route('admin.devices.index') }}" class="mb-4">
    <div class="row g-2">
        <div class="col-lg-5">
            <label for="device-search" class="visually-hidden">Search devices</label>
            <input
                type="search"
                id="device-search"
                name="search"
                value="{{ request('search') }}"
                class="form-control"
                placeholder="Search phone, brand, or slug..."
            >
        </div>

        <div class="col-lg-3">
            <label for="device-brand-filter" class="visually-hidden">Filter by brand</label>
            <select name="brand_id" id="device-brand-filter" class="form-select">
                <option value="">All brands</option>
                @foreach($brands as $brand)
                    <option
                        value="{{ $brand->id }}"
                        @selected((string) request('brand_id') === (string) $brand->id)
                    >
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-2">
            <label for="device-status-filter" class="visually-hidden">Filter by status</label>
            <select name="status" id="device-status-filter" class="form-select">
                <option value="">All statuses</option>
                <option value="available" @selected(request('status') === 'available')>Available</option>
                <option value="rumored" @selected(request('status') === 'rumored')>Rumored</option>
                <option value="discontinued" @selected(request('status') === 'discontinued')>Discontinued</option>
            </select>
        </div>

        <div class="col-lg-2 d-grid">
            <button type="submit" class="btn btn-outline-dark">Filter</button>
        </div>
    </div>

    @if(request()->filled('search') || request()->filled('brand_id') || request()->filled('status'))
        <div class="mt-2">
            <a href="{{ route('admin.devices.index') }}" class="small text-decoration-none">
                Clear filters
            </a>
        </div>
    @endif
</form>

<div class="table-responsive bg-white border">
    <table class="table align-middle mb-0">
        <thead>
        <tr>
            <th scope="col" class="text-center" style="width:72px;">Image</th>
            <th scope="col">Phone</th>
            <th scope="col">Brand</th>
            <th scope="col">Release</th>
            <th scope="col">Status</th>
            <th scope="col" class="text-end">Actions</th>
        </tr>
        </thead>

        <tbody>
        @forelse($devices as $device)
            <tr>
                <td class="text-center">
                    @if($device->image)
                        <img
                            src="{{ asset('storage/' . $device->image) }}"
                            width="52"
                            height="52"
                            alt="{{ $device->name }}"
                            class="border bg-light p-1"
                            style="object-fit:contain;"
                            loading="lazy"
                        >
                    @else
                        <div
                            class="border bg-light text-muted d-inline-flex align-items-center justify-content-center small"
                            style="width:52px;height:52px;"
                        >
                            —
                        </div>
                    @endif
                </td>

                <td>
                    <a
                        href="{{ route('admin.devices.edit', $device) }}"
                        class="fw-semibold text-decoration-none text-dark"
                    >
                        {{ $device->name }}
                    </a>
                    <div class="text-muted small mt-1">
                        {{ $device->slug }}
                    </div>
                </td>

                <td>
                    {{ $device->brand?->name ?? '—' }}
                </td>

                <td class="text-nowrap">
                    {{ $device->release_date?->format('M d, Y') ?? '—' }}
                </td>

                <td class="text-nowrap">
                    @php
                        $statusClass = match ($device->status) {
                            'available' => 'text-bg-success',
                            'rumored' => 'text-bg-warning',
                            'discontinued' => 'text-bg-secondary',
                            default => 'text-bg-light',
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">
                        {{ ucfirst($device->status) }}
                    </span>
                </td>

                <td class="text-end text-nowrap">
                    <a
                        href="{{ route('devices.show', $device) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn btn-sm btn-outline-secondary"
                    >
                        View
                    </a>
                    <a
                        href="{{ route('admin.devices.edit', $device) }}"
                        class="btn btn-sm btn-outline-dark"
                    >
                        Edit
                    </a>
                    <form
                        method="POST"
                        action="{{ route('admin.devices.destroy', $device) }}"
                        class="d-inline"
                        onsubmit="return confirm('Delete this device? This cannot be undone.');"
                    >
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-5">
                    No devices found.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@if($devices->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $devices->links() }}
    </div>
@endif

@endsection
