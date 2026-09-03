<?php

namespace App\Filament\Resources\DeviceSpecs\Pages;

use App\Filament\Resources\DeviceSpecs\DeviceSpecResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeviceSpec extends EditRecord
{
    protected static string $resource = DeviceSpecResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
