<?php

namespace App\Filament\Resources\DeviceSpecs\Pages;

use App\Filament\Resources\DeviceSpecs\DeviceSpecResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeviceSpecs extends ListRecords
{
    protected static string $resource = DeviceSpecResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
