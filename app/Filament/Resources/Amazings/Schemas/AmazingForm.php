<?php

namespace App\Filament\Resources\Amazings\Schemas;

use App\Filament\Support\MoneyComponents;
use App\Models\Product;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AmazingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label('کالا')
                    ->options(fn () => Product::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('percent')
                    ->label('درصد تخفیف')
                    ->numeric()
                    ->suffix('٪')
                    ->nullable(),
                MoneyComponents::tomanInput('price_rial', 'قیمت ویژه'),
                DateTimePicker::make('start_at')
                    ->label('شروع')
                    ->jalali()
                    ->nullable(),
                DateTimePicker::make('end_at')
                    ->label('پایان')
                    ->jalali()
                    ->nullable(),
                Toggle::make('status')
                    ->label('فعال')
                    ->default(true),
            ]);
    }
}
