<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Filament\Support\MoneyComponents;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable(),
                TextColumn::make('order.ref_code')
                    ->label('سفارش')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('مشتری')
                    ->searchable(),
                MoneyComponents::tomanColumn('amount_rial', 'مبلغ'),
                TextColumn::make('gateway')
                    ->label('درگاه')
                    ->badge()
                    ->formatStateUsing(fn ($record) => $record->gatewayLabel()),
                TextColumn::make('ref_id')
                    ->label('شماره پیگیری')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('installment_count')
                    ->label('اقساط')
                    ->formatStateUsing(fn ($state, $record) => $state ? $state.' قسط '.number_format((int) $record->installment_amount_rial / 10).' تومانی' : '—')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($record) => $record->statusLabel()),
                TextColumn::make('created_at')
                    ->label('زمان')
                    ->jalaliDateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'start_payment' => 'در انتظار پرداخت',
                        'paid' => 'پرداخت شده',
                        'failed' => 'ناموفق',
                        'canceled' => 'لغو شده',
                    ]),
                SelectFilter::make('gateway')
                    ->label('درگاه')
                    ->options([
                        'saman' => 'سامان',
                        'zarinpal' => 'زرین‌پال',
                        'digipay' => 'دیجی‌پی',
                        'torobpay' => 'ترب‌پی',
                    ]),
                TrashedFilter::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
