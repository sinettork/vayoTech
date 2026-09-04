<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Device;
use App\Models\NewsPost;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'deviceCount' => Device::count(),
            'availableCount' => Device::where('status', 'available')->count(),
            'rumoredCount' => Device::where('status', 'rumored')->count(),
            'brandCount' => Brand::count(),
            'newsCount' => NewsPost::count(),
            'latestDevices' => Device::with('brand')->latest('id')->take(8)->get(),
        ]);
    }
}
