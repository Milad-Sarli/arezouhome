<?php

namespace App\Filament\Resources\Shipments\Tables;

use App\Filament\Support\MoneyComponents;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable(),
                MoneyComponents::tomanColumn('default_price_rial', 'هزینه پیش‌فرض'),
                IconColumn::make('is_default')
                    ->label('پیش‌فرض')
                    ->boolean(),
                IconColumn::make('pay_after_delivery')
                    ->label('درب منزل')
                    ->boolean(),
                ToggleColumn::make('status')
                    ->label('فعال'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
