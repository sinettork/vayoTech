<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Device;
use App\Models\NewsPost;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // The homepage should show genuinely recent phones, not simply the
        // newest records in the database. Released phones must have a real
        // release date in the past; upcoming phones use a future date.
        $latestDevices = Device::with('brand')
            ->where(function ($query) use ($today) {
                $query->where(function ($released) use ($today) {
                    $released->where('status', 'available')
                        ->whereNotNull('release_date')
                        ->whereDate('release_date', '<=', $today);
                })->orWhere(function ($upcoming) use ($today) {
                    $upcoming->where('status', 'rumored')
                        ->whereNotNull('release_date')
                        ->whereDate('release_date', '>=', $today);
                });
            })
            ->orderByRaw("CASE WHEN status = 'rumored' THEN 0 ELSE 1 END")
            ->orderByDesc('release_date')
            ->orderByDesc('id')
            ->take(8)
            ->get();

        $latestNews = NewsPost::whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->take(4)
            ->get();

        $brands = Brand::orderBy('name')->get();

        $comingSoon = Device::with('brand')
            ->where('status', 'rumored')
            ->where(function ($query) use ($today) {
                $query->whereNull('release_date')
                    ->orWhereDate('release_date', '>=', $today);
            })
            ->orderByRaw('CASE WHEN release_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('release_date')
            ->take(3)
            ->get();

        return view('home', compact('latestDevices', 'latestNews', 'brands', 'comingSoon'));
    }
}