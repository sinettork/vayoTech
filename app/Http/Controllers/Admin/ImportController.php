<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Import;
use App\Services\DeviceImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function index(): View
    {
        return view('admin.imports.index', [
            'imports' => Import::with('user')->latest()->paginate(20),
        ]);
    }

    public function store(Request $request, DeviceImportService $importer): RedirectResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        $file = $data['file'];
        $path = $file->store('imports');

        $import = Import::create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'importer' => DeviceImportService::class,
            'total_rows' => 0,
            'user_id' => $request->user()->id,
        ]);

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
}
