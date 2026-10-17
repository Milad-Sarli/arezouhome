<?php

namespace App\Filament\Resources\Provinces\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProvinceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('نام استان')
                    ->required()
                    ->maxLength(255),
                Toggle::make('status')
                    ->label('فعال')
                    ->default(true),
            ]);
    }
}
