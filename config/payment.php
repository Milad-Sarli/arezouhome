<?php

use App\Services\Payment\LocalDriver;
use Shetabit\Multipay\Constants\IranCurrency;
use Shetabit\Multipay\Drivers\Saman\Saman;
use Shetabit\Multipay\Drivers\Zarinpal\Zarinpal;

return [
    'default' => env('PAYMENT_GATEWAY', 'saman'),

    'drivers' => [
        'local' => [
            'callbackUrl' => '/payment/callback',
            'title' => 'درگاه پرداخت تست',
            'description' => 'صرفا برای تست روند پرداخت',
            'orderLabel' => 'شماره سفارش',
            'amountLabel' => 'مبلغ قابل پرداخت',
            'payButton' => 'پرداخت موفق',
            'cancelButton' => 'پرداخت ناموفق',
        ],
        'saman' => [
            'merchantId' => env('SAMAN_MERCHANT', ''),
            'callbackUrl' => env('APP_URL').'/payment/callback',
            'currency' => IranCurrency::TOMAN,
        ],
        'zarinpal' => [
            'merchantId' => env('ZARINPAL_MERCHANT', ''),
            'callbackUrl' => env('APP_URL').'/payment/callback',
            'currency' => IranCurrency::TOMAN,
            'sandbox' => env('ZARINPAL_SANDBOX', false),
        ],
    ],

    // NOTE: this replaces the package default map (shallow merge), so every
    // driver we use must be listed here with its class.
    'map' => [
        'local' => LocalDriver::class,
        'saman' => Saman::class,
        'zarinpal' => Zarinpal::class,
    ],
];
