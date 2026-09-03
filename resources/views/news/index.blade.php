@extends('layouts.app')

@section('title', 'News')
@section('meta_description', 'The latest phone launches, mobile technology updates, and smartphone news.')
@section('canonical', route('news.index'))

@section('content')
<div class="mb-4">
    <span class="text-primary small fw-semibold text-uppercase">From the mobile world</span>
    <h1 class="mb-1">Latest News</h1>
    <p class="text-muted mb-0">Launches, updates, and useful context for the phones you follow.</p>
</div>

<div class="row g-3">
@foreach ($posts as $post)
    <div class="col-md-6">
        <article class="card article-card h-100">
            @if ($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" class="article-image" alt="{{ $post->title }}" loading="lazy">
            @endif
            <div class="card-body">
                <p class="text-muted small mb-2">{{ $post->published_at->format('M d, Y') }}</p>
                <h2 class="h5 card-title"><a href="{{ route('news.show', $post) }}" class="text-decoration-none">{{ $post->title }}</a></h2>
                <p class="card-text text-muted mb-0">{{ Str::limit(strip_tags($post->body), 150) }}</p>
            </div>
            <div class="card-footer bg-white border-0 pt-0"><a href="{{ route('news.show', $post) }}" class="small">Read story</a></div>
        </div>
    </div>
@endforeach
</div>

<div class="mt-4">{{ $posts->links() }}</div>
@endsection
