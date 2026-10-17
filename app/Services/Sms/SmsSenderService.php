<?php

namespace App\Services\Sms;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * High-level SMS sender. Never throws: SMS failures are logged,
 * order/payment flows must not break because of them.
 */
class SmsSenderService
{
    public function __construct(private IppanelEdgeService $edge) {}

    public function sendOrderPlaced(Order $order): void
    {
        $code = Setting::get('sms_pattern_order');
        $phone = $order->user?->phone;

        if (! $code || ! $phone) {
            return;
        }

        $this->safe(fn () => $this->edge->sendPattern($code, $phone, [
            'name' => $order->user->name ?? '',
            'order' => $order->ref_code,
        ]));
    }

    public function sendStatusChanged(Order $order): void
    {
        $code = Setting::get('sms_pattern_status');
        $phone = $order->user?->phone;

        if (! $code || ! $phone) {
            return;
        }

        $this->safe(fn () => $this->edge->sendPattern($code, $phone, [
            'order' => $order->ref_code,
            'status' => $order->statusLabel(),
        ]));
    }

    public function sendOtp(string $phone, string $code): void
    {
        $pattern = Setting::get('sms_pattern_login');

        if (! $pattern) {
            return;
        }

        $this->safe(fn () => $this->edge->sendPattern($pattern, $phone, ['code' => $code]));
    }

    private function safe(callable $send): void
    {
        try {
            $send();
        } catch (Throwable $e) {
            Log::warning('SMS failed: '.$e->getMessage());
        }
    }
}
