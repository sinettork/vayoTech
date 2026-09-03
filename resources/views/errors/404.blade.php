@extends('layouts.app')

@section('title', 'Page Not Found | PhoneSpecs')
@section('meta_description', 'The requested PhoneSpecs page could not be found.')
@section('content')
    <div class="text-center py-5">
        <h1 class="display-4">404</h1>
        <p class="lead">We could not find that page.</p>
        <a class="btn btn-primary" href="{{ route('home') }}">Return home</a>
    </div>
@endsection
