<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('نام برند')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('نامک (slug)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->helperText('به فارسی با خط‌تیره، مثلا: اسنوا'),
                FileUpload::make('logo')
                    ->label('لوگو')
                    ->image()
                    ->imageEditor()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                    ->maxSize(2048)
                    ->directory('brands'),
                Toggle::make('status')
                    ->label('فعال')
                    ->default(true),
                Textarea::make('description')
                    ->label('توضیحات')
                    ->columnSpanFull(),
            ]);
    }
}
