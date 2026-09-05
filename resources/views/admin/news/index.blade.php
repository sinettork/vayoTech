@extends('admin.layouts.app')

@section('title', 'News')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">News</h1>
        <p class="text-muted mb-0">Manage website articles.</p>
    </div>
    <a href="{{ route('admin.news.create') }}" class="btn btn-dark">Add News</a>
</div>

<form method="GET" action="{{ route('admin.news.index') }}" class="mb-4">
    <div class="row g-2">
        <div class="col-md-10">
            <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search news...">
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-outline-dark">Search</button>
        </div>
    </div>
</form>

<div class="table-responsive">
    <table class="table align-middle">
        <thead>
        <tr>
            <th>Article</th>
            <th>Published</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($posts as $post)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        @if($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" width="72" height="50" class="rounded border" style="object-fit:cover;">
                        @endif
                        <div>
                            <div class="fw-semibold">{{ $post->title }}</div>
                            <div class="text-muted small">{{ $post->slug }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ $post->published_at?->format('M d, Y H:i') ?? 'Not published' }}</td>
                <td>
                    @if(!$post->published_at)
                        Draft
                    @elseif($post->published_at->isFuture())
                        Scheduled
                    @else
                        Published
                    @endif
                </td>
                <td class="text-end">
                    @if($post->published_at && $post->published_at->isPast())
                        <a href="{{ route('news.show', $post->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View</a>
                    @endif
                    <a href="{{ route('admin.news.edit', $post) }}" class="btn btn-sm btn-outline-dark">Edit</a>
                    <form action="{{ route('admin.news.destroy', $post) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this news post?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center py-5 text-muted">No news posts found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{ $posts->links() }}
@endsection
