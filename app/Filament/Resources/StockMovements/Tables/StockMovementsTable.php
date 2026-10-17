<?php

namespace App\Filament\Resources\StockMovements\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('کالا')
                    ->searchable(),
                TextColumn::make('variant.color.name')
                    ->label('رنگ')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('type')
                    ->label('نوع')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'in' ? 'ورود' : 'خروج'),
                TextColumn::make('quantity')
                    ->label('تعداد')
                    ->sortable(),
                TextColumn::make('reason')
                    ->label('دلیل')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('order.ref_code')
                    ->label('سفارش')
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('کاربر')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('زمان')
                    ->jalaliDateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('نوع')
                    ->options(['in' => 'ورود', 'out' => 'خروج']),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
