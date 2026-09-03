<?php

namespace App\Filament\Resources\DeviceSpecs;

use App\Filament\Resources\DeviceSpecs\Pages\CreateDeviceSpec;
use App\Filament\Resources\DeviceSpecs\Pages\EditDeviceSpec;
use App\Filament\Resources\DeviceSpecs\Pages\ListDeviceSpecs;
use App\Filament\Resources\DeviceSpecs\Schemas\DeviceSpecForm;
use App\Filament\Resources\DeviceSpecs\Tables\DeviceSpecsTable;
use App\Models\DeviceSpec;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DeviceSpecResource extends Resource
{
    protected static ?string $model = DeviceSpec::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return DeviceSpecForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeviceSpecsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeviceSpecs::route('/'),
            'create' => CreateDeviceSpec::route('/create'),
            'edit' => EditDeviceSpec::route('/{record}/edit'),
        ];
    }
}
