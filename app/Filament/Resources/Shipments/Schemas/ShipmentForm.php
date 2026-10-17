<?php

namespace App\Filament\Resources\Shipments\Schemas;

use App\Filament\Support\MoneyComponents;
use App\Models\City;
use App\Models\Province;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('عنوان')
                    ->required()
                    ->maxLength(255),
                MoneyComponents::tomanInput('default_price_rial', 'هزینه پیش‌فرض', true),
                Toggle::make('is_default')
                    ->label('پیش‌فرض'),
                Toggle::make('pay_after_delivery')
                    ->label('پرداخت درب منزل'),
                Toggle::make('status')
                    ->label('فعال')
                    ->default(true),
                FileUpload::make('logo')
                    ->label('لوگو')
                    ->image()
                    ->directory('shipments'),
                Textarea::make('description')
                    ->label('توضیحات')
                    ->columnSpanFull(),
                Repeater::make('exceptions')
                    ->label('استثنای قیمت استان / شهر')
                    ->relationship()
                    ->schema([
                        Select::make('province_id')
                            ->label('استان')
                            ->options(fn () => Province::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        Select::make('city_ids')
                            ->label('شهرها (خالی = کل استان)')
                            ->multiple()
                            ->options(fn ($get) => City::where('province_id', $get('province_id'))->pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        MoneyComponents::tomanInput('price_rial', 'هزینه', true),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}
