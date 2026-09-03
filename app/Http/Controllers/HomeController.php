<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Device;
use App\Models\NewsPost;

class HomeController extends Controller
{
    public function index()
    {
        $latestDevices = Device::with('brand')
            ->orderByDesc('release_date')
            ->take(8)
            ->get();

        $latestNews = NewsPost::whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->take(4)
            ->get();

        $brands = Brand::orderBy('name')->get();

        $comingSoon = Device::with('brand')
            ->where('status', 'rumored')
            ->orderByDesc('release_date')
            ->take(3)
            ->get();

        return view('home', compact('latestDevices', 'latestNews', 'brands', 'comingSoon'));
    }
}