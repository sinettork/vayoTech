@extends('admin.layouts.app')

@section('title', 'Data imports')

@section('content')
<div class="mb-4">
    <div class="sidebar-title mb-2">Catalog</div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
        <div>
            <h1 class="h3 mb-1">Data imports</h1>
            <p class="text-muted mb-0">Import many phone records from a CSV file at once.</p>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h2 class="h5 mb-2">Import phone data</h2>
        <p class="text-muted small mb-3">
            CSV files can contain device fields plus any number of <code>spec_*</code> columns.
        </p>

        <form method="POST" action="{{ route('admin.imports.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-lg-9">
                    <label for="import-file" class="form-label">CSV file</label>
                    <input type="file" id="import-file" name="file" class="form-control" accept=".csv,.txt" required>
                </div>
                <div class="col-lg-3 d-grid">
                    <button type="submit" class="btn btn-dark">Import data</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h2 class="h5">CSV format</h2>
        <p class="text-muted small">Use these columns for the device fields. Specification columns are optional.</p>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                <tr>
                    <th>Column</th>
                    <th>Required</th>
                    <th>Example</th>
                </tr>
                </thead>
                <tbody>
                <tr><td><code>brand</code></td><td>Yes</td><td>Samsung</td></tr>
                <tr><td><code>name</code></td><td>Yes</td><td>Galaxy S26 Ultra</td></tr>
                <tr><td><code>slug</code></td><td>No</td><td>samsung-galaxy-s26-ultra</td></tr>
                <tr><td><code>release_date</code></td><td>No</td><td>2026-02-20</td></tr>
                <tr><td><code>status</code></td><td>No</td><td>available</td></tr>
                <tr><td><code>image</code></td><td>No</td><td>devices/s26-ultra.jpg</td></tr>
                <tr><td><code>spec_ram</code></td><td>No</td><td>12 GB</td></tr>
                <tr><td><code>spec_storage</code></td><td>No</td><td>256 GB</td></tr>
                <tr><td><code>spec_display</code></td><td>No</td><td>6.9 inches</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="table-responsive bg-white border">
    <table class="table align-middle mb-0">
        <thead>
        <tr>
            <th>File</th>
            <th>Importer</th>
            <th>Rows</th>
            <th>Successful</th>
            <th>Failed</th>
            <th>Imported</th>
        </tr>
        </thead>
        <tbody>
        @forelse($imports as $import)
            <tr>
                <td>
                    <div class="fw-semibold">{{ $import->file_name }}</div>
                    <div class="text-muted small">{{ $import->user?->name ?? 'Unknown user' }}</div>
                </td>
                <td class="text-muted small">{{ class_basename($import->importer) }}</td>
                <td>{{ $import->total_rows }}</td>
                <td><span class="badge text-bg-success">{{ $import->successful_rows }}</span></td>
                <td>
                    @if($import->failedRows()->count())
                        <span class="badge text-bg-danger">{{ $import->failedRows()->count() }}</span>
                    @else
                        <span class="text-muted">0</span>
                    @endif
                </td>
                <td class="text-nowrap">{{ $import->completed_at?->format('M d, Y H:i') ?? 'Processing' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-5">No imports yet.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@if($imports->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $imports->links() }}
    </div>
@endif
@endsection
