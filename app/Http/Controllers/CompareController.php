<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompareController extends Controller
{
    public function index(Request $request): View
    {
        $deviceIds = collect(explode(',', (string) $request->query('devices', '')))
            ->filter(fn (string $id): bool => ctype_digit($id))
            ->map(fn (string $id): int => (int) $id)
            ->unique()
            ->take(3)
            ->values();

        $devicesById = Device::with(['brand', 'specs'])
            ->whereIn('id', $deviceIds)
            ->get()
            ->keyBy('id');

        $devices = $deviceIds
            ->map(fn (int $id) => $devicesById->get($id))
            ->filter()
            ->values();

        $groupedKeys = [];
        foreach ($devices as $device) {
            foreach ($device->specs as $spec) {
                $groupedKeys[$spec->category][$spec->spec_key] = true;
            }
        }

        $compareRows = [];
        foreach ($groupedKeys as $category => $keys) {
            foreach (array_keys($keys) as $key) {
                $values = [];
                foreach ($devices as $device) {
                    $match = $device->specs->first(
                        fn ($spec) => $spec->category === $category && $spec->spec_key === $key
                    );
                    $values[] = $match?->spec_value;
                }

                $normalizedValues = collect($values)
                    ->filter(fn ($value) => filled($value))
                    ->map(fn ($value) => trim((string) $value))
                    ->unique()
                    ->values();

                $compareRows[] = [
                    'category' => $category,
                    'key' => $key,
                    'values' => $values,
                    'has_difference' => $normalizedValues->count() > 1,
                ];
            }
        }

        $allDevices = Device::with('brand')
            ->orderBy('name')
            ->get(['id', 'brand_id', 'name', 'slug', 'image', 'release_date', 'status']);

        $compareSearchData = $allDevices->map(fn ($device) => [
            'id' => $device->id,
            'name' => $device->name,
            'brand' => $device->brand?->name,
            'url' => route('devices.show', $device),
            'image' => $device->image ? asset('storage/' . $device->image) : null,
        ])->values();

        $quickSpecs = $devices->map(function ($device) {
            $find = fn (string $key) => $device->specs
                ->first(fn ($spec) => strcasecmp($spec->spec_key, $key) === 0)
                ?->spec_value;

            return [
                'screen' => $find('Screen Size'),
                'camera' => $find('Main Camera'),
                'ram' => $find('RAM'),
                'battery' => $find('Capacity'),
            ];
        })->values();

        return view('compare.index', compact(
            'devices',
            'compareRows',
            'compareSearchData',
            'quickSpecs'
        ));
    }
}
