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
            ->take(4)
            ->values()
            ->all();

        $devices = Device::with(['brand', 'specs'])
            ->whereIn('id', $deviceIds)
            ->get();

        $groupedKeys = [];
        foreach ($devices as $device) {
            foreach ($device->specs as $spec) {
                $groupedKeys[$spec->category][$spec->spec_key] = true;
            }
        }

        $allDevices = Device::with('brand')->orderBy('name')->get();

        return view('compare.index', compact('devices', 'groupedKeys', 'allDevices'));
    }
}
