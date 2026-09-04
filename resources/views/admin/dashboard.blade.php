@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Dashboard</h1>
        <p class="text-muted mb-0">Manage your VayoTech phone specifications database.</p>
    </div>
    <a href="{{ route('admin.devices.create') }}" class="btn btn-dark">+ Add Device</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3"><div class="card admin-stat h-100"><div class="card-body"><div class="text-muted small">Total Devices</div><div class="display-6 fw-bold">{{ $deviceCount }}</div></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card admin-stat h-100"><div class="card-body"><div class="text-muted small">Available</div><div class="display-6 fw-bold text-success">{{ $availableCount }}</div></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card admin-stat h-100"><div class="card-body"><div class="text-muted small">Rumored</div><div class="display-6 fw-bold text-warning">{{ $rumoredCount }}</div></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card admin-stat h-100"><div class="card-body"><div class="text-muted small">Brands</div><div class="display-6 fw-bold">{{ $brandCount }}</div></div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Latest Devices</strong>
                <a href="{{ route('admin.devices.index') }}" class="text-decoration-none small">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Device</th><th>Brand</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    @forelse($latestDevices as $device)
                        <tr>
                            <td class="fw-semibold">{{ $device->name }}</td>
                            <td>{{ $device->brand->name }}</td>
                            <td><span class="badge text-bg-{{ $device->status === 'available' ? 'success' : ($device->status === 'rumored' ? 'warning' : 'secondary') }}">{{ ucfirst($device->status) }}</span></td>
                            <td class="text-end"><a href="{{ route('admin.devices.edit', $device) }}" class="btn btn-sm btn-outline-dark">Edit</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No devices yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h6">Content</h2>
                <div class="d-flex justify-content-between border-bottom py-3"><span>Brands</span><strong>{{ $brandCount }}</strong></div>
                <div class="d-flex justify-content-between border-bottom py-3"><span>News Posts</span><strong>{{ $newsCount }}</strong></div>
                <div class="d-flex justify-content-between py-3"><span>Devices</span><strong>{{ $deviceCount }}</strong></div>
            </div>
        </div>
    </div>
</div>
@endsection
