<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Device;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Device::with('brand')->latest('release_date')->latest('id');

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhereHas('brand', fn ($brand) => $brand->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->integer('brand_id'));
        }

        return view('admin.devices.index', [
            'devices' => $query->paginate(15)->withQueryString(),
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.devices.create', [
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateDevice($request);
        $data['slug'] = $data['slug'] ?: $this->uniqueSlug($data['name'], $data['brand_id']);
        $data['image'] = $this->storeImage($request);

        $device = Device::create($data);
        $this->syncSpecs($device, $request->input('specs', []));

        return redirect()->route('admin.devices.index')->with('success', 'Device created successfully.');
    }

    public function edit(Device $device): View
    {
        $device->load(['brand', 'specs' => fn ($query) => $query->orderBy('sort_order')]);

        return view('admin.devices.edit', [
            'device' => $device,
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Device $device): RedirectResponse
    {
        $data = $this->validateDevice($request, $device->id);
        $data['slug'] = $data['slug'] ?: $device->slug;

        if ($request->hasFile('image')) {
            if ($device->image) {
                Storage::disk('public')->delete($device->image);
            }
            $data['image'] = $this->storeImage($request);
        }

        $device->update($data);
        $this->syncSpecs($device, $request->input('specs', []));

        return redirect()->route('admin.devices.index')->with('success', 'Device updated successfully.');
    }

    public function destroy(Device $device): RedirectResponse
    {
        if ($device->image) {
            Storage::disk('public')->delete($device->image);
        }

        $device->delete();

        return redirect()->route('admin.devices.index')->with('success', 'Device deleted successfully.');
    }

    private function validateDevice(Request $request, ?int $deviceId = null): array
    {
        return $request->validate([
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('devices', 'slug')->ignore($deviceId),
            ],
            'release_date' => ['nullable', 'date'],
            'status' => ['required', 'in:rumored,available,discontinued'],
            'image' => ['nullable', 'image', 'max:2048'],
            'specs' => ['nullable', 'array'],
            'specs.*.category' => ['required', 'string', 'max:100'],
            'specs.*.spec_key' => ['required', 'string', 'max:255'],
            'specs.*.spec_value' => ['required', 'string', 'max:500'],
        ]);
    }

    private function uniqueSlug(string $name, int $brandId): string
    {
        $brand = Brand::find($brandId);
        $base = Str::slug(($brand?->name ? $brand->name . ' ' : '') . $name) ?: 'device';
        $slug = $base;
        $counter = 2;

        while (Device::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }

    private function storeImage(Request $request): ?string
    {
        return $request->hasFile('image')
            ? $request->file('image')->store('devices', 'public')
            : null;
    }

    private function syncSpecs(Device $device, array $specs): void
    {
        $device->specs()->delete();

        foreach (array_values($specs) as $index => $spec) {
            $device->specs()->create([
                'category' => $spec['category'],
                'spec_key' => $spec['spec_key'],
                'spec_value' => $spec['spec_value'],
                'sort_order' => $index + 1,
            ]);
        }
    }
}
