@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="mb-4">
    <div class="sidebar-title mb-2">Overview</div>
    <h1 class="h3 mb-1">Dashboard</h1>
    <p class="text-muted mb-0">
        Manage the VayoTech device catalog and website content.
    </p>
</div>

<div class="row g-3 mb-4">

    <div class="col-6 col-xl-3">
        <div class="border bg-white p-3 h-100">
            <div class="text-muted small mb-1">Devices</div>
            <div class="h3 mb-0">{{ $deviceCount }}</div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="border bg-white p-3 h-100">
            <div class="text-muted small mb-1">Available</div>
            <div class="h3 mb-0">{{ $availableCount }}</div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="border bg-white p-3 h-100">
            <div class="text-muted small mb-1">Rumored</div>
            <div class="h3 mb-0">{{ $rumoredCount }}</div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="border bg-white p-3 h-100">
            <div class="text-muted small mb-1">Brands</div>
            <div class="h3 mb-0">{{ $brandCount }}</div>
        </div>
    </div>

</div>

<div class="row g-4">

    <div class="col-lg-8">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h5 mb-1">Latest Devices</h2>
                <p class="text-muted small mb-0">
                    Recently added models.
                </p>
            </div>

            <a
                href="{{ route('admin.devices.index') }}"
                class="small text-decoration-none"
            >
                View all
            </a>
        </div>

        <div class="table-responsive bg-white border">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th>Device</th>
                    <th>Brand</th>
                    <th>Release</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>

                <tbody>
                @forelse($latestDevices as $device)
                    <tr>
                        <td>
                            <div class="fw-semibold">
                                {{ $device->name }}
                            </div>
                            <div class="text-muted small">
                                {{ $device->slug }}
                            </div>
                        </td>

                        <td>
                            {{ $device->brand?->name ?? '—' }}
                        </td>

                        <td>
                            {{ $device->release_date?->format('M d, Y') ?? '—' }}
                        </td>

                        <td>
                            {{ ucfirst($device->status) }}
                        </td>

                        <td class="text-end">
                            <a
                                href="{{ route('admin.devices.edit', $device) }}"
                                class="btn btn-sm btn-outline-secondary"
                            >
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No devices yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <div class="col-lg-4">

        <div class="mb-3">
            <h2 class="h5 mb-1">Content</h2>
            <p class="text-muted small mb-0">
                Manage your website content.
            </p>
        </div>

        <div class="bg-white border">

            <a
                href="{{ route('admin.devices.index') }}"
                class="d-flex justify-content-between align-items-center text-decoration-none text-dark p-3 border-bottom"
            >
                <span>Devices</span>
                <strong>{{ $deviceCount }}</strong>
            </a>

            <a
                href="{{ route('admin.brands.index') }}"
                class="d-flex justify-content-between align-items-center text-decoration-none text-dark p-3 border-bottom"
            >
                <span>Brands</span>
                <strong>{{ $brandCount }}</strong>
            </a>

            <a
                href="{{ route('admin.news.index') }}"
                class="d-flex justify-content-between align-items-center text-decoration-none text-dark p-3"
            >
                <span>News</span>
                <strong>{{ $newsCount }}</strong>
            </a>

        </div>

    </div>

</div>

@endsection
