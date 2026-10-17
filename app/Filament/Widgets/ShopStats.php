<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ShopStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $revenue = (int) Transaction::where('status', 'paid')->sum('amount_rial');

        return [
            Stat::make('سفارش‌های امروز', Order::whereDate('created_at', today())->count()),
            Stat::make('درآمد پرداخت‌شده (تومان)', number_format($revenue / 10)),
            Stat::make('مشتریان', User::doesntHave('roles')->count()),
            Stat::make('کالاهای فعال', Product::where('status', true)->count()),
        ];
    }
}
