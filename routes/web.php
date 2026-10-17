<?php

use App\Http\Controllers\ImageVariantController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/img/{width}/{path}', [ImageVariantController::class, 'show'])
    ->where(['width' => '[0-9]+', 'path' => '.*'])
    ->withoutMiddleware([
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
        PreventRequestForgery::class,
    ])
    ->name('image.variant');

Route::get('/login', fn () => redirect('/admin/login'))->name('login');

Route::match(['get', 'post'], '/payment/callback', [PaymentController::class, 'callback'])
    ->name('payment.callback');

Route::get('/payment/local', [PaymentController::class, 'local'])
    ->name('payment.local');

Route::get('/admin/orders/{order}/invoice', [InvoiceController::class, 'show'])
    ->middleware('auth')
    ->name('admin.orders.invoice');

Route::get('/account/orders/{ref}/invoice', [InvoiceController::class, 'showForUser'])
    ->middleware('auth')
    ->name('account.orders.invoice');
