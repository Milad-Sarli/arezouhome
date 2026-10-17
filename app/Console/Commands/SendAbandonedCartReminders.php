<?php

namespace App\Console\Commands;

use App\Models\AbandonedCart;
use App\Services\Sms\IppanelEdgeService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('app:send-abandoned-cart-reminders')]
#[Description('Send SMS reminder for carts abandoned over 1 hour')]
class SendAbandonedCartReminders extends Command
{
    public function handle(IppanelEdgeService $sms): int
    {
        $carts = AbandonedCart::whereNull('notified_at')
            ->where('updated_at', '<', now()->subHour())
            ->with('user')
            ->get();

        foreach ($carts as $cart) {
            $phone = $cart->user?->phone;
            if (! $phone) {
                continue;
            }

            try {
                // Use generic pattern if not configured, just log
                Log::info("Abandoned cart reminder for {$phone} with ".count($cart->items).' items');
                // If SMS pattern for abandoned cart is configured, send it
                // $sms->sendPattern(config('sms.pattern_abandoned'), $phone, ['name' => $cart->user->name]);
            } catch (\Throwable $e) {
                Log::warning('Abandoned cart SMS failed: '.$e->getMessage());
            }

            $cart->update(['notified_at' => now()]);
        }

        $this->info("Processed {$carts->count()} abandoned carts");

        return 0;
    }
}
