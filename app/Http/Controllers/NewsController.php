<?php

namespace App\Http\Controllers;

use App\Models\NewsPost;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $posts = NewsPost::whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('news.index', compact('posts'));
    }

    public function show(NewsPost $newsPost): View
    {
        abort_unless(
            $newsPost->published_at && $newsPost->published_at->isPast(),
            404
        );

        return view('news.show', compact('newsPost'));
    }
}
