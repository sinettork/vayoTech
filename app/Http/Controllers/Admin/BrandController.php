<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\BrandfetchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class BrandController extends Controller
{
    public function index(Request $request): View
    {
        $query = Brand::withCount('devices')->orderBy('name');

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('brand_domain', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return view('admin.brands.index', [
            'brands' => $query->paginate(15)->withQueryString(),
        ]);
    }

    public function search(Request $request, BrandfetchService $brandfetch): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        try {
            $results = $brandfetch->search($validated['name']);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Brandfetch search is currently unavailable.',
            ], 502);
        }

        return response()->json([
            'results' => collect($results)
                ->take(8)
                ->map(fn (array $result): array => [
                    'name' => $result['name'] ?? null,
                    'domain' => $brandfetch->normalizeDomain($result['domain'] ?? ''),
                    'icon' => $result['icon'] ?? null,
                    'brandId' => $result['brandId'] ?? null,
                ])
                ->filter(fn (array $result): bool => filled($result['name']) && filled($result['domain']))
                ->values()
                ->all(),
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

        if (filled($data['brand_domain'])) {
            $data['brand_domain'] = app(BrandfetchService::class)
                ->normalizeDomain($data['brand_domain']);
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

        if (filled($data['brand_domain'])) {
            $data['brand_domain'] = app(BrandfetchService::class)
                ->normalizeDomain($data['brand_domain']);
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
            'brand_domain' => ['nullable', 'string', 'max:255'],
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
