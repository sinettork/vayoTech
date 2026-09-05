@extends('admin.layouts.app')

@section('title', 'Add Brand')

@section('content')
<form method="POST" action="{{ route('admin.brands.store') }}" enctype="multipart/form-data">
    @include('admin.brands._form')
</form>
@endsection
