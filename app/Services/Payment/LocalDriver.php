<?php

namespace App\Services\Payment;

use Illuminate\Support\Str;
use Shetabit\Multipay\Abstracts\Driver;
use Shetabit\Multipay\Contracts\ReceiptInterface;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Multipay\Receipt;
use Shetabit\Multipay\RedirectionForm;

/**
 * Fake gateway for development and automated tests.
 *
 * Redirects to a local test page with success/cancel buttons instead of
 * a real bank. Never use with production credentials.
 */
class LocalDriver extends Driver
{
    private string|int|null $authority = null;

    public function __construct(Invoice $invoice, array|object $settings)
    {
        $this->invoice($invoice);
        $this->settings = is_array($settings) ? (object) $settings : $settings;
    }

    public function purchase(): string|int|null
    {
        return $this->authority = 'LOCAL-'.Str::upper(Str::random(10));
    }

    public function pay(): RedirectionForm
    {
        return $this->redirectWithForm(
            route('payment.local'),
            ['Authority' => $this->authority ?? $this->invoice->getTransactionId()],
            'GET'
        );
    }

    public function verify(): ReceiptInterface
    {
        $data = request()->all();

        if (($data['Status'] ?? null) !== 'OK' || empty($data['Authority'])) {
            throw new InvalidPaymentException('پرداخت توسط کاربر لغو شد.');
        }

        return new Receipt('local', $data['Authority']);
    }
}
