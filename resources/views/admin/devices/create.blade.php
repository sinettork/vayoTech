@extends('admin.layouts.app')

@section('title', 'Add Device')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-1">Add Device</h1>
    <p class="text-muted mb-0">Create a new phone model.</p>
</div>

<form method="POST" action="{{ route('admin.devices.store') }}" enctype="multipart/form-data">
    @include('admin.devices._form')
</form>
@endsection
