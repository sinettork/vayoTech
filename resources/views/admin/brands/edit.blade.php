@extends('admin.layouts.app')

@section('title', 'Edit Brand')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-1">Edit Brand</h1>
    <p class="text-muted mb-0">Update {{ $brand->name }}.</p>
</div>

<form method="POST" action="{{ route('admin.brands.update', $brand) }}" enctype="multipart/form-data">
    @method('PUT')
    @include('admin.brands._form', ['brand' => $brand])
</form>
@endsection
