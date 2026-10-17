<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Filament\Support\MoneyComponents;
use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestOrders extends TableWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'تازه‌ترین سفارش‌ها';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('سفارشی ثبت نشده')
            ->emptyStateDescription('سفارش‌های جدید اینجا نمایش داده می‌شوند')
            ->query(Order::with(['user'])->latest()->limit(10))
            ->columns([
                TextColumn::make('ref_code')
                    ->label('کد پیگیری'),
                TextColumn::make('user.name')
                    ->label('مشتری'),
                MoneyComponents::tomanColumn('final_price_rial', 'مبلغ'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => OrderForm::orderStatuses()[$state] ?? $state),
                TextColumn::make('created_at')
                    ->label('ثبت')
                    ->jalaliDateTime(),
            ]);
    }
}
