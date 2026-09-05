@extends('admin.layouts.app')

@section('title', 'Add Brand')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-1">Add Brand</h1>
    <p class="text-muted mb-0">Create a phone manufacturer.</p>
</div>

<form method="POST" action="{{ route('admin.brands.store') }}" enctype="multipart/form-data">
    @include('admin.brands._form')
</form>
@endsection
