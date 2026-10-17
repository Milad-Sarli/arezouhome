<?php

namespace App\Filament\Resources\BlogCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BlogCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('عنوان')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('نامک')
                    ->required()
                    ->unique(ignoreRecord: true),
                Toggle::make('status')
                    ->label('فعال')
                    ->default(true),
            ]);
    }
}
