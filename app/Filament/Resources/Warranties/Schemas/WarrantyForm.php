<?php

namespace App\Filament\Resources\Warranties\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WarrantyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('عنوان')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('نوع')
                    ->options([
                        'official' => 'گارانتی شرکتی',
                        'unofficial' => 'گارانتی متفرقه',
                    ])
                    ->default('official')
                    ->required(),
                TextInput::make('months')
                    ->label('مدت (ماه)')
                    ->numeric()
                    ->default(0)
                    ->suffix('ماه'),
                Toggle::make('status')
                    ->label('فعال')
                    ->default(true),
                Textarea::make('description')
                    ->label('شرایط گارانتی')
                    ->columnSpanFull(),
            ]);
    }
}
