<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;
use Throwable;

/**
 * Online payment via Shetabit. Amounts are stored in Rial,
 * gateways work in Toman -> conversion happens here, in one place.
 */
class PaymentService
{
    public function driver(): string
    {
        return Setting::get('payment_gateway', config('payment.default', 'saman')) ?: 'saman';
    }

    public function merchantId(?string $driver = null): string
    {
        $driver ??= $this->driver();
        $key = $driver.'_merchant';

        return Setting::get($key, config("payment.drivers.{$driver}.merchantId", '')) ?? '';
    }

    public function applyMerchantConfig(): void
    {
        $driver = $this->driver();
        $merchant = $this->merchantId($driver);

        if ($merchant !== '') {
            config(["payment.drivers.{$driver}.merchantId" => $merchant]);
        }
    }

    public static function rialToToman(int $rial): int
    {
        return (int) ($rial / 10);
    }

    /**
     * @return array{url: string, method: string, inputs: array, transaction: Transaction}
     */
    public function start(Order $order): array
    {
        $this->applyMerchantConfig();

        $transaction = Transaction::create([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'amount_rial' => $order->final_price_rial,
            'gateway' => $this->driver(),
            'status' => 'start_payment',
        ]);

        $invoice = (new Invoice)->amount(self::rialToToman($order->final_price_rial));
        $invoice->detail('orderId', $order->ref_code);
        $invoice->detail('transactionId', $transaction->id);

        $redirectUrl = Payment::via($this->driver())
            ->callbackUrl(route('payment.callback'))
            ->purchase(
                $invoice,
                fn ($driver, $transactionId) => $transaction->update(['authority' => $transactionId])
            )
            ->pay();

        return [
            'url' => $redirectUrl->getAction(),
            'method' => $redirectUrl->getMethod(),
            'inputs' => $redirectUrl->getInputs(),
            'transaction' => $transaction->fresh(),
        ];
    }

    /**
     * @return array{ok: bool, ref_id: ?string, message: string}
     */
    public function verify(Request $request): array
    {
        $this->applyMerchantConfig();

        $authority = $request->get('Authority');
        $transaction = Transaction::where('authority', $authority)->latest()->first();

        if (! $transaction) {
            return ['ok' => false, 'ref_id' => null, 'message' => 'تراکنش یافت نشد.'];
        }

        try {
            $receipt = Payment::via($transaction->gateway ?: $this->driver())
                ->amount(self::rialToToman($transaction->amount_rial))
                ->transactionId($authority)
                ->verify();

            $transaction->update([
                'status' => 'paid',
                'ref_id' => $receipt->getReferenceId(),
            ]);

            $this->markOrderPaid($transaction);

            return ['ok' => true, 'ref_id' => (string) $receipt->getReferenceId(), 'message' => 'پرداخت موفق بود.'];
        } catch (InvalidPaymentException|Throwable $e) {
            $transaction->update(['status' => 'failed']);

            return ['ok' => false, 'ref_id' => null, 'message' => 'پرداخت ناموفق بود.'];
        }
    }

    private function markOrderPaid(Transaction $transaction): void
    {
        $order = $transaction->order;

        if (! $order || $order->status !== 'pending') {
            return;
        }

        $order->update(['status' => 'paid']);
        $order->discountCode?->increment('uses_count');

        foreach ($order->items as $item) {
            $product = $item->product;

            if (! $product) {
                continue;
            }

            $product->decrement('quantity', $item->quantity);
            $product->update(['in_stock' => $product->fresh()->quantity > 0]);

            $product->stockMovements()->create([
                'type' => 'out',
                'quantity' => $item->quantity,
                'reason' => 'فروش سفارش '.$order->ref_code,
                'order_id' => $order->id,
            ]);
        }
    }
}
