<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Device::with('brand')->latest('release_date');

        if ($request->filled('brand')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('slug', $request->query('brand'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        $devices = $query->paginate(12)->withQueryString();
        $brands = Brand::query()->orderBy('name')->get(['id', 'name', 'slug', 'description']);

        return view('devices.index', compact('devices', 'brands'));
    }

    public function search(Request $request): JsonResponse
    {
        $q = $request->string('q')->trim()->substr(0, 80)->toString();

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $devices = Device::with('brand')
            ->where('name', 'like', "%{$q}%")
            ->orWhereHas('brand', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%");
            })
            ->limit(8)
            ->get();

        return response()->json($devices->map(function ($device) {
            return [
                'name' => $device->name,
                'brand' => $device->brand->name,
                'url' => route('devices.show', $device),
            ];
        }));
    }

   public function show(Device $device)
{
    $groupedSpecs = $device->groupedSpecs();

    $quickSpecs = [
        'screen' => optional($device->specs->firstWhere('spec_key', 'Screen Size'))->spec_value,
        'camera' => optional($device->specs->firstWhere('spec_key', 'Main Camera'))->spec_value,
        'ram' => optional($device->specs->firstWhere('spec_key', 'RAM'))->spec_value,
        'battery' => optional($device->specs->firstWhere('spec_key', 'Capacity'))->spec_value,
    ];

    return view('devices.show', compact('device', 'groupedSpecs', 'quickSpecs'));
}
}
