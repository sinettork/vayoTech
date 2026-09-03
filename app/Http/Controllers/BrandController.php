<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function show(Brand $brand, Request $request)
    {
        $query = $brand->devices()->orderByDesc('release_date');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $devices = $query->paginate(12)->withQueryString();

        $deviceCount = $brand->devices()->count();

        return view('brands.show', compact('brand', 'devices', 'deviceCount'));
    }
}