@extends('admin.layouts.app')

@section('title', 'Devices')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Devices</h1>
        <p class="text-muted mb-0">Manage phone models and their specifications.</p>
    </div>
    <a href="{{ route('admin.devices.create') }}" class="btn btn-dark">+ Add Device</a>
</div>

<form method="GET" class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-lg-5">
                <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search device or brand...">
            </div>
            <div class="col-lg-3">
                <select name="brand_id" class="form-select">
                    <option value="">All brands</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" @selected((string) request('brand_id') === (string) $brand->id)>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2">
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    <option value="available" @selected(request('status') === 'available')>Available</option>
                    <option value="rumored" @selected(request('status') === 'rumored')>Rumored</option>
                    <option value="discontinued" @selected(request('status') === 'discontinued')>Discontinued</option>
                </select>
            </div>
            <div class="col-lg-2 d-grid">
                <button class="btn btn-outline-dark">Filter</button>
            </div>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr><th>Device</th><th>Brand</th><th>Release</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
            @forelse($devices as $device)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            @if($device->image)
                                <img src="{{ asset('storage/' . $device->image) }}" width="56" height="56" class="rounded border" alt="{{ $device->name }}">
                            @else
                                <div class="rounded border bg-light d-flex align-items-center justify-content-center" style="width:56px;height:56px;">—</div>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $device->name }}</div>
                                <div class="text-muted small">{{ $device->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $device->brand->name }}</td>
                    <td>{{ $device->release_date?->format('M d, Y') ?? '—' }}</td>
                    <td><span class="badge text-bg-{{ $device->status === 'available' ? 'success' : ($device->status === 'rumored' ? 'warning' : 'secondary') }}">{{ ucfirst($device->status) }}</span></td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('devices.show', $device) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View</a>
                            <a href="{{ route('admin.devices.edit', $device) }}" class="btn btn-sm btn-outline-dark">Edit</a>
                            <form method="POST" action="{{ route('admin.devices.destroy', $device) }}" onsubmit="return confirm('Delete this device?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-5 text-muted">No devices found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $devices->links() }}</div>
</div>
@endsection
