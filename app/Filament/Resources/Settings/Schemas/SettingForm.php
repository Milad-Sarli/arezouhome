<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function keys(): array
    {
        return [
            'contact_phone' => 'تلفن تماس',
            'contact_phone_2' => 'تلفن تماس دوم',
            'contact_email' => 'ایمیل',
            'contact_address' => 'نشانی',
            'working_hours' => 'ساعات کاری',
            'support_info' => 'اطلاعات پشتیبانی',
            'about' => 'درباره فروشگاه',
            'terms' => 'قوانین و مقررات',
            'instagram' => 'اینستاگرام',
            'telegram' => 'تلگرام',
            'whatsapp' => 'واتساپ',
            'store_name' => 'نام فروشگاه (سربرگ فاکتور)',
            'economic_code' => 'کد اقتصادی',
            'payment_gateway' => 'درگاه پرداخت (saman یا zarinpal)',
            'saman_merchant' => 'مرچنت سامان',
            'zarinpal_merchant' => 'مرچنت زرین‌پال',
            'sms_api_key' => 'کلید API پیامک (IPPanel)',
            'sms_sender_number' => 'شماره فرستنده پیامک',
            'sms_pattern_order' => 'کد پترن ثبت سفارش',
            'sms_pattern_status' => 'کد پترن تغییر وضعیت',
            'sms_pattern_login' => 'کد پترن ورود',
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('key')
                    ->label('کلید')
                    ->options(self::keys())
                    ->required()
                    ->unique(ignoreRecord: true),
                Textarea::make('value')
                    ->label('مقدار')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
