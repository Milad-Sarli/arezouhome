<?php

namespace App\Filament\Resources\DiscountCodes\Schemas;

use App\Filament\Support\MoneyComponents;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class DiscountCodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('کد')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Select::make('type')
                    ->label('نوع')
                    ->options(['percent' => 'درصدی', 'fixed' => 'مبلغ ثابت (تومان)'])
                    ->default('percent')
                    ->live()
                    ->required(),
                TextInput::make('value_rial')
                    ->label(fn ($get) => ($get('type') ?? 'percent') === 'percent' ? 'درصد تخفیف' : 'مبلغ تخفیف (تومان)')
                    ->numeric()
                    ->required()
                    ->helperText('درصدی: عدد ۰ تا ۱۰۰ — مبلغی: به تومان، موقع ذخیره به ریال تبدیل می‌شود')
                    ->formatStateUsing(fn (Get $get, $state) => $state === null ? null : (($get('type') ?? 'percent') === 'percent' ? $state : (int) ((int) $state / 10)))
                    ->dehydrateStateUsing(function ($state, $get) {
                        if (($get('type') ?? 'percent') === 'percent') {
                            return (int) $state;
                        }

                        return (int) preg_replace('/\D/', '', (string) $state) * 10;
                    }),
                TextInput::make('max_uses')
                    ->label('سقف استفاده (خالی = نامحدود)')
                    ->numeric()
                    ->nullable(),
                MoneyComponents::tomanInput('min_order_rial', 'حداقل مبلغ سفارش'),
                DateTimePicker::make('starts_at')
                    ->label('شروع')
                    ->jalali()
                    ->nullable(),
                DateTimePicker::make('expires_at')
                    ->label('پایان')
                    ->jalali()
                    ->nullable(),
                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ]);
    }
}
