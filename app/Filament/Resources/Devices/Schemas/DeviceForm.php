<?php

namespace App\Filament\Resources\Devices\Schemas;

use App\Models\Brand;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DeviceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('brand_id')
                    ->label('Brand')
                    ->options(Brand::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                DatePicker::make('release_date'),
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('devices')
                    ->imageEditor()
                    ->imageEditorAspectRatios(['1:1', '4:3', null])
                    ->maxSize(2048),
                Select::make('status')
                    ->options(['rumored' => 'Rumored', 'available' => 'Available', 'discontinued' => 'Discontinued'])
                    ->default('available')
                    ->required(),

                Repeater::make('specs')
                    ->relationship()
                    ->schema([
                        Select::make('category')
                            ->options([
                                'Display' => 'Display',
                                'Camera' => 'Camera',
                                'Battery' => 'Battery',
                                'Performance' => 'Performance',
                                'Body' => 'Body',
                                'Connectivity' => 'Connectivity',
                            ])
                            ->required(),
                        TextInput::make('spec_key')
                            ->label('Spec Name')
                            ->placeholder('e.g. Screen Size')
                            ->required(),
                        TextInput::make('spec_value')
                            ->label('Value')
                            ->placeholder('e.g. 6.3 inches')
                            ->required(),
                        Hidden::make('sort_order')
                            ->default(0),
                    ])
                    ->columns(3)
                    ->defaultItems(0)
                    ->addActionLabel('Add Spec')
                    ->columnSpanFull(),
            ]);
    }
}
