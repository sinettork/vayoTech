<?php

namespace App\Filament\Imports;

use App\Models\Brand;
use App\Models\Device;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class DeviceImporter extends Importer
{
    protected static ?string $model = Device::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('brand_name')
                ->label('Brand')
                ->requiredMapping()
                ->rules(['required', 'string']),

            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),

            ImportColumn::make('slug')
                ->rules(['nullable', 'string', 'max:255']),

            ImportColumn::make('release_date')
                ->rules(['nullable', 'date']),

            ImportColumn::make('status')
                ->rules(['nullable', 'in:rumored,available,discontinued']),
        ];
    }

    public function resolveRecord(): ?Device
    {
        $slug = $this->data['slug'] ?? Str::slug($this->data['name']);

        return Device::firstOrNew(['slug' => $slug]);
    }

    // Take full manual control of which fields actually get set on the model —
    // this is what stops Filament from trying to write 'brand_name' directly
   public function fillRecord(): void
{
    $brand = Brand::firstOrCreate(
        ['name' => trim($this->data['brand_name'])],
        ['slug' => Str::slug(trim($this->data['brand_name']))]
    );

    $this->record->brand_id = $brand->id;
    $this->record->name = $this->data['name'];
    $this->record->slug = $this->data['slug'] ?? Str::slug($this->data['name']);
    $this->record->release_date = $this->data['release_date'] ?? null;
    $this->record->status = $this->data['status'] ?? 'available';
}

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your device import has completed. ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}