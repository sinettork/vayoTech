@extends('admin.layouts.app')

@section('title', 'Add News')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-1">Add News</h1>
    <p class="text-muted mb-0">Create a new article.</p>
</div>

<form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data">
    @include('admin.news._form')
</form>
@endsection
