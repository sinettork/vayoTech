<?php
namespace App\Filament\Resources\Devices\Tables;

use App\Filament\Imports\DeviceImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DevicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('brand.name')
                    ->label('Brand')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('release_date')
                    ->date()
                    ->sortable(),
                ImageColumn::make('image')
                    ->disk('public'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('brand')
                    ->relationship('brand', 'name'),
                SelectFilter::make('status')
                    ->options([
                        'rumored' => 'Rumored',
                        'available' => 'Available',
                        'discontinued' => 'Discontinued',
                    ]),
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(DeviceImporter::class),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}