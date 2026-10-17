<?php

namespace App\Filament\Resources\ReturnRequests\Schemas;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReturnRequestForm
{
    public static function returnReasons(): array
    {
        return [
            'defect' => 'کالای معیوب',
            'mismatch' => 'مغایرت با سفارش',
            'damaged_shipping' => 'آسیب در حمل‌ونقل',
            'cancel' => 'انصراف از خرید',
            'warranty' => 'استفاده از گارانتی',
        ];
    }

    public static function returnStatuses(): array
    {
        return [
            'pending' => 'در انتظار بررسی',
            'approved' => 'تأیید شده',
            'rejected' => 'رد شده',
            'completed' => 'تکمیل شده',
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('مشتری')
                    ->options(fn () => User::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('order_id')
                    ->label('سفارش')
                    ->options(fn () => Order::pluck('ref_code', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('product_id')
                    ->label('کالا')
                    ->options(fn () => Product::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('quantity')
                    ->label('تعداد')
                    ->numeric()
                    ->default(1),
                Select::make('reason')
                    ->label('دلیل')
                    ->options(self::returnReasons())
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options(self::returnStatuses())
                    ->default('pending')
                    ->required(),
                Textarea::make('description')
                    ->label('توضیحات')
                    ->columnSpanFull(),
            ]);
    }
}
