<?php

namespace App\Filament\Resources\DeviceSpecs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DeviceSpecForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('device_id')
                    ->required()
                    ->numeric(),
                TextInput::make('category')
                    ->required(),
                TextInput::make('spec_key')
                    ->required(),
                TextInput::make('spec_value')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
