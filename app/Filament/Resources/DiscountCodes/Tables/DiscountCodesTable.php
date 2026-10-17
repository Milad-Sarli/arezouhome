<?php

namespace App\Filament\Resources\DiscountCodes\Tables;

use App\Filament\Support\MoneyComponents;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DiscountCodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('کد')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('نوع')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'percent' ? 'درصدی' : 'مبلغی'),
                TextColumn::make('value_rial')
                    ->label('مقدار')
                    ->formatStateUsing(fn ($state, $record) => $record->type === 'percent' ? $state.'٪' : number_format((int) $state / 10).' تومان'),
                TextColumn::make('uses_count')
                    ->label('استفاده‌شده')
                    ->suffix(fn ($record) => $record->max_uses ? ' / '.$record->max_uses : ''),
                MoneyComponents::tomanColumn('min_order_rial', 'حداقل سفارش'),
                TextColumn::make('expires_at')
                    ->label('انقضا')
                    ->jalaliDateTime()
                    ->sortable()
                    ->toggleable(),
                ToggleColumn::make('is_active')
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
