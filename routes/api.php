<?php

use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\CheckoutController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\ProductQuestionController;
use App\Http\Controllers\Api\V1\ReturnController;
use App\Http\Controllers\Api\V1\TicketController;
use App\Http\Controllers\Api\V1\WishlistController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::get('/categories', [CatalogController::class, 'categories']);
    Route::get('/brands', [CatalogController::class, 'brands']);
    Route::get('/products', [CatalogController::class, 'products']);
    Route::get('/products/{slug}', [CatalogController::class, 'product']);
    Route::get('/sliders', [CatalogController::class, 'sliders']);
    Route::get('/amazings', [CatalogController::class, 'amazings']);
    Route::get('/blogs', [CatalogController::class, 'blogs']);
    Route::get('/blogs/{slug}', [CatalogController::class, 'blog']);
    Route::get('/shipments', [CatalogController::class, 'shipments']);
    Route::get('/installments', [CatalogController::class, 'installments']);
    Route::get('/track/{ref}', [CheckoutController::class, 'track'])->middleware('throttle:10,1');
    Route::post('/newsletter', [CatalogController::class, 'newsletter'])->middleware('throttle:5,1');
    Route::post('/discounts/check', [CatalogController::class, 'discountCheck']);
    Route::get('/products/{slug}/comments', [CommentController::class, 'index']);
    Route::get('/products/{slug}/questions', [ProductQuestionController::class, 'index']);
    Route::get('/provinces', [CatalogController::class, 'provinces']);
    Route::get('/settings', [CatalogController::class, 'settings']);
    Route::get('/home', [CatalogController::class, 'home']);

    Route::post('/auth/otp/send', [AuthController::class, 'sendOtp'])->middleware('throttle:5,1');
    Route::post('/auth/otp/verify', [AuthController::class, 'verifyOtp'])->middleware('throttle:10,1');
    Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::patch('/me', [AuthController::class, 'update']);
        Route::post('/me/password', [AuthController::class, 'setPassword'])->middleware('throttle:10,1');
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/addresses', [AddressController::class, 'index']);
        Route::post('/addresses', [AddressController::class, 'store']);
        Route::patch('/addresses/{id}', [AddressController::class, 'update']);
        Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);

        Route::post('/checkout', [CheckoutController::class, 'checkout'])->middleware('throttle:10,1');
        Route::post('/products/{slug}/comments', [CommentController::class, 'store'])->middleware('throttle:10,1');
        Route::post('/products/{slug}/questions', [ProductQuestionController::class, 'store'])->middleware('throttle:10,1');

        Route::get('/tickets', [TicketController::class, 'index']);
        Route::post('/tickets', [TicketController::class, 'store']);
        Route::get('/tickets/{id}', [TicketController::class, 'show']);
        Route::post('/tickets/{id}/reply', [TicketController::class, 'reply']);

        Route::get('/returns', [ReturnController::class, 'index']);
        Route::post('/returns', [ReturnController::class, 'store']);

        Route::get('/wishlist', [WishlistController::class, 'index']);
        Route::post('/wishlist/toggle', [WishlistController::class, 'toggle']);
        Route::post('/wishlist/sync', [WishlistController::class, 'sync']);

        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart/sync', [CartController::class, 'sync']);
        Route::delete('/cart', [CartController::class, 'clear']);

        Route::get('/orders/{ref}/invoice', [InvoiceController::class, 'invoiceForApi']);
        Route::patch('/orders/{ref}/cancel', [CheckoutController::class, 'cancel']);
        Route::post('/stock-alerts', [CatalogController::class, 'stockAlert']);
        Route::get('/orders', [CheckoutController::class, 'orders']);
        Route::get('/orders/{ref}', [CheckoutController::class, 'order']);
    });
});
