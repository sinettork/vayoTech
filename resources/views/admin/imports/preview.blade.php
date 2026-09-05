@extends('admin.layouts.app')

@section('title', 'Import preview')

@section('content')
<div class="mb-4">
    <div class="sidebar-title mb-2">Catalog</div>
    <div>
        <h1 class="h3 mb-1">Import preview</h1>
        <p class="text-muted mb-0">{{ $fileName }}</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-2">
        <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Total rows</div><div class="fs-3 fw-semibold">{{ $preview['total'] }}</div></div></div>
    </div>
    <div class="col-md-6 col-xl-2">
        <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">New phones</div><div class="fs-3 fw-semibold">{{ $preview['new_devices'] }}</div></div></div>
    </div>
    <div class="col-md-6 col-xl-2">
        <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Existing phones</div><div class="fs-3 fw-semibold">{{ $preview['existing_devices'] }}</div></div></div>
    </div>
    <div class="col-md-6 col-xl-2">
        <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">New brands</div><div class="fs-3 fw-semibold">{{ $preview['new_brands'] }}</div></div></div>
    </div>
    <div class="col-md-6 col-xl-2">
        <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Duplicates</div><div class="fs-3 fw-semibold">{{ $preview['duplicate_rows'] }}</div></div></div>
    </div>
    <div class="col-md-6 col-xl-2">
        <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Invalid rows</div><div class="fs-3 fw-semibold">{{ $preview['invalid_rows'] }}</div></div></div>
    </div>
</div>

@if($preview['invalid_rows'] > 0)
    <div class="alert alert-warning mb-4">
        <strong>Review the issues before importing.</strong>
        Rows with validation issues will not be counted as clean rows. The current importer can still record failed rows, but for a large catalog it is safer to fix the CSV first.
    </div>
@endif

@if(count($preview['issues']))
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Detected issues</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Row</th>
                        <th>Device</th>
                        <th>Issue</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($preview['issues'] as $issue)
                        <tr>
                            <td>{{ $issue['row'] }}</td>
                            <td>{{ $issue['device'] }}</td>
                            <td>{{ implode(', ', $issue['issues']) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if($preview['invalid_rows'] > count($preview['issues']))
                <p class="text-muted small mt-3 mb-0">Showing the first {{ count($preview['issues']) }} problematic rows.</p>
            @endif
        </div>
    </div>
@endif

<div class="d-flex flex-column flex-sm-row gap-2">
    <form method="POST" action="{{ route('admin.imports.store') }}">
        @csrf
        <button type="submit" class="btn btn-dark" {{ $preview['invalid_rows'] > 0 ? 'disabled' : '' }}>
            Confirm import
        </button>
    </form>

    <a href="{{ route('admin.imports.index') }}" class="btn btn-outline-secondary">Choose another file</a>
</div>

@if($preview['invalid_rows'] > 0)
    <p class="text-muted small mt-3 mb-0">Fix the invalid rows and upload the corrected CSV again.</p>
@endif
@endsection
