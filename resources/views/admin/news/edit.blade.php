@extends('admin.layouts.app')

@section('title', 'Edit News')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-1">Edit News</h1>
    <p class="text-muted mb-0">Update {{ $newsPost->title }}.</p>
</div>

<form method="POST" action="{{ route('admin.news.update', $newsPost) }}" enctype="multipart/form-data">
    @method('PUT')
    @include('admin.news._form', ['newsPost' => $newsPost])
</form>
@endsection
