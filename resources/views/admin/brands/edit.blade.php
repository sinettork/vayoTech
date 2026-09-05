@extends('admin.layouts.app')

@section('title', 'Edit Brand')

@section('content')
<form method="POST" action="{{ route('admin.brands.update', $brand) }}" enctype="multipart/form-data">
    @method('PUT')
    @include('admin.brands._form', ['brand' => $brand])
</form>
@endsection
