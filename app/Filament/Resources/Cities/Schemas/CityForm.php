<?php

namespace App\Filament\Resources\Cities\Schemas;

use App\Models\Province;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('province_id')
                    ->label('استان')
                    ->options(fn () => Province::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->label('نام شهر')
                    ->required()
                    ->maxLength(255),
                Toggle::make('status')
                    ->label('فعال')
                    ->default(true),
            ]);
    }
}
