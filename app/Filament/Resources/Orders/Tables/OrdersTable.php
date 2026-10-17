<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Filament\Support\MoneyComponents;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ref_code')
                    ->label('کد پیگیری')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('مشتری')
                    ->searchable(),
                TextColumn::make('items_count')
                    ->label('اقلام')
                    ->counts('items'),
                MoneyComponents::tomanColumn('final_price_rial', 'مبلغ نهایی'),
                TextColumn::make('shipment.title')
                    ->label('ارسال')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => OrderForm::orderStatuses()[$state] ?? $state),
                TextColumn::make('created_at')
                    ->label('ثبت')
                    ->jalaliDateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(OrderForm::orderStatuses()),
                Filter::make('created_at')
                    ->label('بازه ثبت')
                    ->schema([
                        DatePicker::make('from')->label('از تاریخ')->jalali(),
                        DatePicker::make('until')->label('تا تاریخ')->jalali(),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date))),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('invoice')
                    ->label('چاپ فاکتور')
                    ->icon(Heroicon::OutlinedPrinter)
                    ->url(fn ($record) => route('admin.orders.invoice', $record))
                    ->openUrlInNewTab(),
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
