<?php

namespace App\Http\Controllers;

use App\Models\NewsPost;

class NewsController extends Controller
{
    public function index()
    {
        $posts = NewsPost::whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('news.index', compact('posts'));
    }

    public function show(NewsPost $newsPost)
    {
        return view('news.show', compact('newsPost'));
    }
}
