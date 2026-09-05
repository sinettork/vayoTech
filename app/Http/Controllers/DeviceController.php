<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Device::with('brand')
            ->latest('release_date')
            ->latest('id');

        if ($request->filled('brand')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('slug', $request->query('brand'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        $devices = $query->paginate(12)->withQueryString();

        $brands = Brand::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'description']);

        return view('devices.index', compact('devices', 'brands'));
    }

    public function search(Request $request): JsonResponse
    {
        $q = $request
            ->string('q')
            ->trim()
            ->substr(0, 80)
            ->toString();

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $devices = Device::with('brand')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhereHas('brand', function ($brandQuery) use ($q) {
                        $brandQuery->where('name', 'like', "%{$q}%");
                    });
            })
            ->orderBy('name')
            ->limit(8)
            ->get();

        return response()->json(
            $devices->map(function (Device $device): array {
                return [
                    'name' => $device->name,
                    'brand' => $device->brand?->name,
                    'url' => route('devices.show', $device),
                    'image' => $device->image
                        ? asset('storage/' . $device->image)
                        : null,
                ];
            })->values()
        );
    }

    public function show(Device $device): View
    {
        $device->load([
            'brand',
            'specs' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        if (Schema::hasTable('device_variants')) {
            $device->load('variants');
        }

        $groupedSpecs = $device->specs->groupBy('category');

        $findSpec = function (string $key) use ($device): ?string {
            $spec = $device->specs->first(
                fn ($item) => strcasecmp(
                    trim((string) $item->spec_key),
                    $key
                ) === 0
            );

            return $spec?->spec_value;
        };

        $quickSpecs = [
            'screen' => $findSpec('Screen Size'),
            'camera' => $findSpec('Main Camera'),
            'ram' => $findSpec('RAM'),
            'battery' => $findSpec('Capacity'),
        ];

        return view('devices.show', compact(
            'device',
            'groupedSpecs',
            'quickSpecs'
        ));
    }
}
