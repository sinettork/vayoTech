@extends('admin.layouts.app')

@section('title', 'Edit Device')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Edit Device</h1>
        <p class="text-muted mb-0">Update {{ $device->name }}.</p>
    </div>
    <a href="{{ route('devices.show', $device) }}" target="_blank" class="btn btn-outline-secondary">View on Website</a>
</div>

<form method="POST" action="{{ route('admin.devices.update', $device) }}" enctype="multipart/form-data">
    @method('PUT')
    @include('admin.devices._form', ['device' => $device])
</form>
@endsection
