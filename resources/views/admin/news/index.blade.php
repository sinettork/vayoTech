@extends('admin.layouts.app')

@section('title', 'News')

@section('content')
<div class="mb-4">
    <div class="sidebar-title mb-2">Content</div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
        <div>
            <h1 class="h3 mb-1">News</h1>
            <p class="text-muted mb-0">Manage website articles and publication status.</p>
        </div>
        <a href="{{ route('admin.news.create') }}" class="btn btn-dark">Add News</a>
    </div>
</div>

<form method="GET" action="{{ route('admin.news.index') }}" class="mb-4">
    <div class="row g-2">
        <div class="col-lg-8">
            <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search title, content, or slug...">
        </div>
        <div class="col-lg-2">
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                <option value="scheduled" @selected(request('status') === 'scheduled')>Scheduled</option>
                <option value="published" @selected(request('status') === 'published')>Published</option>
            </select>
        </div>
        <div class="col-lg-2 d-grid">
            <button type="submit" class="btn btn-outline-dark">Filter</button>
        </div>
    </div>
</form>

<div class="table-responsive bg-white border">
    <table class="table align-middle mb-0">
        <thead>
        <tr>
            <th>Article</th>
            <th>Publication</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($posts as $post)
            @php
                $status = !$post->published_at
                    ? 'Draft'
                    : ($post->published_at->isFuture() ? 'Scheduled' : 'Published');
            @endphp
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        @if($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" width="72" height="50" class="border" style="object-fit:cover;">
                        @endif
                        <div class="min-w-0">
                            <div class="fw-semibold text-truncate">{{ $post->title }}</div>
                            <div class="text-muted small text-truncate">{{ $post->slug }}</div>
                        </div>
                    </div>
                </td>
                <td class="small">
                    {{ $post->published_at?->format('M d, Y H:i') ?? 'Not published' }}
                </td>
                <td>{{ $status }}</td>
                <td class="text-end text-nowrap">
                    @if($post->published_at && $post->published_at->isPast())
                        <a href="{{ route('news.show', $post->slug) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary">View</a>
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

@if($posts->hasPages())
    <div class="mt-4">{{ $posts->links() }}</div>
@endif
@endsection
