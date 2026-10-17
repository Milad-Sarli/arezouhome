<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Filament\Support\MoneyComponents;
use App\Models\Address;
use App\Models\DiscountCode;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function orderStatuses(): array
    {
        return [
            'pending' => 'در انتظار پرداخت',
            'paid' => 'پرداخت شده',
            'processing' => 'در حال پردازش',
            'shipped' => 'ارسال شده',
            'delivered' => 'تحویل داده شده',
            'canceled' => 'لغو شده',
            'returned' => 'مرجوع شده',
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ref_code')
                    ->label('کد پیگیری')
                    ->default(fn () => 'AH-'.now()->format('Ymd').'-'.random_int(1000, 9999))
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('user_id')
                    ->label('مشتری')
                    ->options(fn () => User::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')->label('نام')->required(),
                        TextInput::make('phone')->label('موبایل')->required(),
                    ]),
                Select::make('address_id')
                    ->label('آدرس')
                    ->options(fn (Get $get) => Address::where('user_id', $get('user_id'))
                        ->with(['province', 'city'])
                        ->get()
                        ->mapWithKeys(fn ($a) => [$a->id => $a->province->name.'، '.$a->city->name.' — '.$a->address]))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('shipment_id')
                    ->label('روش ارسال')
                    ->options(fn () => Shipment::where('status', true)->pluck('title', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('discount_code_id')
                    ->label('کد تخفیف')
                    ->options(fn () => DiscountCode::where('is_active', true)->pluck('code', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options(self::orderStatuses())
                    ->default('pending')
                    ->required(),
                Repeater::make('items')
                    ->label('اقلام سفارش')
                    ->relationship()
                    ->schema([
                        Select::make('product_id')
                            ->label('کالا')
                            ->options(fn () => Product::where('status', true)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                $product = Product::find($state);
                                if ($product) {
                                    $set('product_name', $product->name);
                                    $set('unit_price_rial', $product->finalPrice());
                                    self::recalcItem($set, $get);
                                }
                            }),
                        TextInput::make('product_name')
                            ->label('نام کالا (ثبت‌شده)')
                            ->required(),
                        TextInput::make('quantity')
                            ->label('تعداد')
                            ->numeric()
                            ->default(1)
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::recalcItem($set, $get)),
                        TextInput::make('unit_price_rial')
                            ->label('قیمت واحد (ریال)')
                            ->numeric()
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::recalcItem($set, $get)),
                        TextInput::make('total_rial')
                            ->label('جمع (ریال)')
                            ->numeric()
                            ->readOnly(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                MoneyComponents::tomanInput('post_price_rial', 'هزینه ارسال'),
                MoneyComponents::tomanInput('discount_price_rial', 'مبلغ تخفیف'),
                MoneyComponents::tomanInput('final_price_rial', 'مبلغ نهایی', true),
                Textarea::make('note')
                    ->label('یادداشت')
                    ->columnSpanFull(),
            ]);
    }

    private static function recalcItem(Set $set, Get $get): void
    {
        $set('total_rial', (int) $get('quantity') * (int) $get('unit_price_rial'));
    }
}
