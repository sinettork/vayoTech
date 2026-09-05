<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Device;
use App\Models\Import;
use App\Services\DeviceImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function index(): View
    {
        return view('admin.imports.index', [
            'imports' => Import::with('user')
                ->withCount('failedRows')
                ->latest()
                ->paginate(20),
        ]);
    }

    public function preview(Request $request): View|RedirectResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        $file = $data['file'];
        $path = $file->store('imports/previews');
        $absolutePath = Storage::path($path);

        try {
            $handle = fopen($absolutePath, 'rb');
            $headers = $this->readHeaders($handle);

            if (!$headers) {
                fclose($handle);
                Storage::delete($path);

                return redirect()
                    ->route('admin.imports.index')
                    ->with('error', 'The CSV file is empty or has no header row.');
            }

            $requiredHeaders = ['brand', 'name'];
            $missingHeaders = array_values(array_diff($requiredHeaders, $headers));

            if ($missingHeaders) {
                fclose($handle);
                Storage::delete($path);

                return redirect()
                    ->route('admin.imports.index')
                    ->with('error', 'Missing required columns: ' . implode(', ', $missingHeaders) . '.');
            }

            $preview = [
                'total' => 0,
                'new_devices' => 0,
                'existing_devices' => 0,
                'new_brands' => 0,
                'duplicate_rows' => 0,
                'invalid_rows' => 0,
                'issues' => [],
            ];

            $seenKeys = [];
            $knownBrands = Brand::query()->pluck('id', 'slug')->all();
            $knownDevices = Device::query()->pluck('id', 'slug')->all();

            while (($row = fgetcsv($handle)) !== false) {
                if ($this->rowIsEmpty($row)) {
                    continue;
                }

                $preview['total']++;
                $record = $this->combineRow($headers, $row);
                $brandName = trim((string) ($record['brand'] ?? ''));
                $name = trim((string) ($record['name'] ?? ''));
                $slug = trim((string) ($record['slug'] ?? ''));
                $slug = $slug !== '' ? Str::slug($slug) : Str::slug($brandName . ' ' . $name);
                $key = $slug !== '' ? $slug : 'row-' . $preview['total'];
                $issues = [];

                if ($brandName === '') {
                    $issues[] = 'brand is required';
                }

                if ($name === '') {
                    $issues[] = 'name is required';
                }

                if (!empty($record['status']) && !in_array($record['status'], ['rumored', 'available', 'discontinued'], true)) {
                    $issues[] = 'invalid status';
                }

                if (!empty($record['release_date']) && date_create($record['release_date']) === false) {
                    $issues[] = 'invalid release_date';
                }

                if (isset($seenKeys[$key])) {
                    $preview['duplicate_rows']++;
                    $issues[] = 'duplicate device in this CSV';
                } else {
                    $seenKeys[$key] = true;
                }

                if ($issues) {
                    $preview['invalid_rows']++;
                    if (count($preview['issues']) < 20) {
                        $preview['issues'][] = [
                            'row' => $preview['total'] + 1,
                            'device' => $brandName && $name ? $brandName . ' ' . $name : ($name ?: 'Unknown device'),
                            'issues' => $issues,
                        ];
                    }
                } elseif (isset($knownDevices[$key])) {
                    $preview['existing_devices']++;
                } else {
                    $preview['new_devices']++;
                }

                if ($brandName !== '' && !isset($knownBrands[Str::slug($brandName)])) {
                    $knownBrands[Str::slug($brandName)] = true;
                    $preview['new_brands']++;
                }
            }

            fclose($handle);
        } catch (\Throwable $e) {
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }

            Storage::delete($path);
            report($e);

            return redirect()
                ->route('admin.imports.index')
                ->with('error', 'The CSV preview could not be generated.');
        }

        session([
            'device_import_preview' => [
                'path' => $path,
                'file_name' => $file->getClientOriginalName(),
            ],
        ]);

        return view('admin.imports.preview', [
            'preview' => $preview,
            'fileName' => $file->getClientOriginalName(),
        ]);
    }

    public function store(Request $request, DeviceImportService $importer): RedirectResponse
    {
        $preview = session('device_import_preview');

        if (!is_array($preview) || empty($preview['path']) || !Storage::exists($preview['path'])) {
            return redirect()
                ->route('admin.imports.index')
                ->with('error', 'The import preview has expired. Please upload the CSV again.');
        }

        $path = $preview['path'];
        $fileName = $preview['file_name'] ?? basename($path);

        $import = Import::create([
            'file_name' => $fileName,
            'file_path' => $path,
            'importer' => DeviceImportService::class,
            'total_rows' => 0,
            'user_id' => $request->user()->id,
        ]);

        session()->forget('device_import_preview');

        try {
            $importer->import($import);
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.imports.index')
                ->with('error', 'The import could not be completed: ' . $e->getMessage());
        }

        return redirect()
            ->route('admin.imports.index')
            ->with('success', "Import completed: {$import->successful_rows} of {$import->total_rows} rows imported.");
    }

    private function readHeaders($handle): array
    {
        $headers = fgetcsv($handle);

        if ($headers === false) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($header) => strtolower(trim((string) $header)),
            $headers
        ), static fn ($header) => $header !== ''));
    }

    private function combineRow(array $headers, array $row): array
    {
        $row = array_pad($row, count($headers), null);
        $record = [];

        foreach ($headers as $index => $header) {
            $record[$header] = trim((string) ($row[$index] ?? ''));
        }

        return $record;
    }

    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }
}
