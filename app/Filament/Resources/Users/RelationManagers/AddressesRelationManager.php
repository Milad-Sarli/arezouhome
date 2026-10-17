<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    protected static ?string $title = 'آدرس‌ها';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('province.name')
                    ->label('استان'),
                TextColumn::make('city.name')
                    ->label('شهر'),
                TextColumn::make('address')
                    ->label('نشانی')
                    ->limit(50),
                TextColumn::make('postal_code')
                    ->label('کدپستی'),
                TextColumn::make('receiver_name')
                    ->label('تحویل‌گیرنده'),
                TextColumn::make('receiver_mobile')
                    ->label('موبایل'),
                IconColumn::make('is_default')
                    ->label('پیش‌فرض')
                    ->boolean(),
            ]);
    }
}
