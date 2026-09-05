@extends('admin.layouts.app')

@section('title', 'Brands')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Brands</h1>
        <p class="text-muted mb-0">Manage phone manufacturers.</p>
    </div>
    <a href="{{ route('admin.brands.create') }}" class="btn btn-dark">Add Brand</a>
</div>

<form method="GET" action="{{ route('admin.brands.index') }}" class="mb-4">
    <div class="row g-2">
        <div class="col-md-10">
            <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search brands...">
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-outline-dark">Search</button>
        </div>
    </div>
</form>

<div class="table-responsive">
    <table class="table align-middle">
        <thead>
        <tr>
            <th>Brand</th>
            <th>Slug</th>
            <th>Devices</th>
            <th class="text-end">Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($brands as $brand)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        @if($brand->logo)
                            <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" width="48" height="48" class="rounded border" style="object-fit:contain;">
                        @endif
                        <div>
                            <div class="fw-semibold">{{ $brand->name }}</div>
                            @if($brand->description)
                                <div class="text-muted small">{{ Str::limit($brand->description, 80) }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td><code>{{ $brand->slug }}</code></td>
                <td>{{ $brand->devices_count }}</td>
                <td class="text-end">
                    <a href="{{ route('brands.show', $brand->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View</a>
                    <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-outline-dark">Edit</a>
                    <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this brand?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center py-5 text-muted">No brands found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{ $brands->links() }}
@endsection
