<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Filament\Support\MoneyComponents;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $title = 'سفارش‌ها';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ref_code')
                    ->label('کد پیگیری')
                    ->searchable(),
                MoneyComponents::tomanColumn('final_price_rial', 'مبلغ'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => OrderForm::orderStatuses()[$state] ?? $state),
                TextColumn::make('created_at')
                    ->label('ثبت')
                    ->jalaliDateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
