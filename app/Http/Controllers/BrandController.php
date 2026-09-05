<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\NewsPost;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function show(Brand $brand, Request $request)
    {
        $query = $brand->devices()
            ->with('brand')
            ->withCount('specs');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->boolean('card_slot')) {
            $query->whereHas('specs', function ($specs) {
                $specs->where('spec_key', 'like', '%card slot%');
            });
        }

        if ($request->boolean('jack_3_5mm')) {
            $query->whereHas('specs', function ($specs) {
                $specs->where(function ($where) {
                    $where->where('spec_key', 'like', '%3.5mm%')
                        ->orWhere('spec_value', 'like', '%3.5mm%');
                });
            });
        }

        if ($request->boolean('esim')) {
            $query->whereHas('specs', function ($specs) {
                $specs->where(function ($where) {
                    $where->where('spec_key', 'like', '%esim%')
                        ->orWhere('spec_value', 'like', '%esim%');
                });
            });
        }

        if ($request->boolean('foldable')) {
            $query->where(function ($devices) {
                $devices->where('name', 'like', '%fold%')
                    ->orWhereHas('specs', function ($specs) {
                        $specs->where(function ($where) {
                            $where->where('spec_key', 'like', '%form factor%')
                                ->orWhere('spec_value', 'like', '%fold%');
                        });
                    });
            });
        }

        $sort = $request->query('sort', 'new');

        if ($sort === 'popular') {
            $query->orderByDesc('specs_count')->orderByDesc('release_date');
        } else {
            $query->orderByDesc('release_date');
        }

        $devices = $query->paginate(15)->withQueryString();

        $deviceCount = $brand->devices()->count();

        $brands = Brand::query()
            ->orderBy('name')
            ->get();

        $brandNews = NewsPost::query()
            ->whereNotNull('published_at')
            ->where(function ($news) use ($brand) {
                $news->where('title', 'like', '%' . $brand->name . '%')
                    ->orWhere('body', 'like', '%' . $brand->name . '%');
            })
            ->orderByDesc('published_at')
            ->first();

        $editorialPost = $brandNews ?: NewsPost::query()
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->first();

        $heroDevice = $brand->devices()
            ->whereNotNull('image')
            ->orderByDesc('release_date')
            ->first();

        return view('brands.show', compact(
            'brand',
            'devices',
            'deviceCount',
            'brands',
            'editorialPost',
            'heroDevice',
            'sort'
        ));
    }
}
