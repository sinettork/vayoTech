<?php

namespace App\Http\Controllers;

use App\Models\NewsPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(Request $request): View
    {
        $query = NewsPost::whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at');

        $search = $request->string('q')->trim()->substr(0, 80)->toString();
        if ($search !== '') {
            $query->where(function ($posts) use ($search): void {
                $posts->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(10)->withQueryString();

        return view('news.index', compact('posts', 'search'));
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
