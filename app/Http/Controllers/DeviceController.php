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
        $query = Device::with('brand')
            ->latest('release_date')
            ->latest('id');

        if ($request->filled('brand')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('slug', $request->query('brand'));
            });
        }

        $status = $request->string('status')->toString();
        if (in_array($status, ['available', 'rumored', 'discontinued'], true)) {
            $query->where('status', $status);
        } else {
            $status = null;
        }

        $search = $request->string('q')->trim()->substr(0, 80)->toString();
        if ($search !== '') {
            $query->where(function ($devices) use ($search): void {
                $devices->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhereHas('brand', fn ($brand) => $brand->where('name', 'like', "%{$search}%"));
            });
        }

        $sort = $request->string('sort')->toString();
        if ($sort === 'name') {
            $query->reorder('name')->orderBy('id');
        } elseif ($sort === 'oldest') {
            $query->reorder('release_date')->orderBy('id');
        }

        $devices = $query->paginate(12)->withQueryString();

        $brands = Brand::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'description']);

        return view('devices.index', compact('devices', 'brands', 'status', 'search', 'sort'));
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
                    'id' => $device->id,
                    'name' => $device->name,
                    'brand' => $device->brand?->name,
                    'url' => route('devices.show', $device),
                    'image' => $device->image
                        ? asset('storage/'.$device->image)
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
            'variants' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        $groupedSpecs = $device->specs->groupBy('category');

        $findSpec = function (array $keys) use ($device): ?string {
            foreach ($keys as $key) {
                $spec = $device->specs->first(
                    fn ($item) => strcasecmp(
                        trim((string) $item->spec_key),
                        $key
                    ) === 0
                );

                if ($spec?->spec_value) {
                    return $spec->spec_value;
                }
            }

            return null;
        };

        $quickSpecs = [
            'os' => $findSpec(['OS', 'Operating System', 'Android Version', 'iOS Version']),
            'storage' => $findSpec(['Storage', 'Internal Storage', 'ROM']),
            'screen' => $findSpec(['Screen Size', 'Display Size', 'Size', 'Display']),
            'display' => $findSpec(['Display Type', 'Display Technology', 'Resolution']),
            'chip' => $findSpec(['Chipset', 'Chip', 'SoC', 'Processor']),
            'camera' => $findSpec(['Main Camera', 'Main camera setup', 'Camera']),
            'ram' => $findSpec(['RAM', 'Memory']),
            'battery' => $findSpec(['Capacity', 'Battery Capacity', 'Battery capacity']),
        ];

        $relatedDevices = Device::query()
            ->with('brand')
            ->where('brand_id', $device->brand_id)
            ->where('id', '!=', $device->id)
            ->latest('release_date')
            ->limit(4)
            ->get();

        return view('devices.show', compact(
            'device',
            'groupedSpecs',
            'quickSpecs',
            'relatedDevices'
        ));
    }
}
