@extends('layouts.app')

@section('title', 'Something Went Wrong | PhoneSpecs')
@section('meta_description', 'PhoneSpecs encountered an unexpected error.')
@section('content')
    <div class="text-center py-5">
        <h1 class="display-4">Something went wrong</h1>
        <p class="lead">Please try again in a moment.</p>
        <a class="btn btn-primary" href="{{ route('home') }}">Return home</a>
    </div>
@endsection
