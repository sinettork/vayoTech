<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(Request $request): View
    {
        $query = Brand::withCount('devices')->orderBy('name');

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return view('admin.brands.index', [
            'brands' => $query->paginate(15)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.brands.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateBrand($request);

        if (blank($data['slug'])) {
            $data['slug'] = $this->uniqueSlug($data['name']);
        }

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        Brand::create($data);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand created successfully.');
    }

    public function edit(Brand $brand): View
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $data = $this->validateBrand($request, $brand->id);

        if (blank($data['slug'])) {
            $data['slug'] = $brand->slug ?: $this->uniqueSlug($data['name']);
        }

        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }

            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $brand->update($data);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->devices()->exists()) {
            return redirect()->route('admin.brands.index')
                ->with('error', 'This brand cannot be deleted because it has devices.');
        }

        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand deleted successfully.');
    }

    private function validateBrand(Request $request, ?int $brandId = null): array
    {
        $slugRule = 'unique:brands,slug';

        if ($brandId) {
            $slugRule .= ',' . $brandId;
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $slugRule],
            'description' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'brand';
        $slug = $base;
        $counter = 2;

        while (Brand::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
