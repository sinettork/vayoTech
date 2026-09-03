<?php

namespace App\Filament\Resources\NewsPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NewsPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Textarea::make('body')
                    ->required()
                    ->rows(12)
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->disk('public')
                    ->directory('news')
                    ->image()
                    ->imageEditor()
                    ->maxSize(2048),
                DateTimePicker::make('published_at'),
            ]);
    }
}
