
@extends('layouts.app')

@section('title', $newsPost->title)
@section('meta_description', Str::limit(strip_tags($newsPost->body), 155))
@section('canonical', route('news.show', $newsPost))

@section('content')
<article class="card article-card">
    @if ($newsPost->image)
        <img src="{{ asset('storage/' . $newsPost->image) }}" class="article-image" alt="{{ $newsPost->title }}" loading="eager">
    @endif
    <div class="card-body p-4 p-md-5">
        <span class="text-primary small fw-semibold text-uppercase">PhoneSpecs news</span>
        <h1 class="mt-1 mb-2">{{ $newsPost->title }}</h1>
        <p class="text-muted mb-4">{{ $newsPost->published_at->format('M d, Y') }}</p>
        <div class="article-body">{{ $newsPost->body }}</div>
    </div>
</article>
<a href="{{ route('news.index') }}" class="btn btn-outline-secondary btn-sm mt-3">Back to news</a>
@endsection
