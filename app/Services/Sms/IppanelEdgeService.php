<?php

namespace App\Services\Sms;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * IPPanel Edge API client (Access-Key mode).
 * Credentials are read from Settings (admin-editable) with env fallback,
 * so nothing needs a code change once the SMS panel is purchased.
 */
class IppanelEdgeService
{
    public function __construct(
        private ?string $apiKey = null,
        private ?string $sender = null,
    ) {
        $this->apiKey = $apiKey ?? Setting::get('sms_api_key', config('sms.api_key'));
        $this->sender = $sender ?? Setting::get('sms_sender_number', config('sms.sender'));
    }

    public function isConfigured(): bool
    {
        return ! empty($this->apiKey);
    }

    public function defaultSender(): string
    {
        return (string) $this->sender;
    }

    public function assertConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('کلید پیامک تنظیم نشده است. از بخش تنظیمات فروشگاه، sms_api_key را وارد کنید.');
        }
    }

    private function request(string $method, string $path, array $body = [], array $query = []): array
    {
        $this->assertConfigured();

        $client = Http::acceptJson()
            ->withHeaders(['Authorization' => $this->apiKey])
            ->baseUrl(rtrim((string) config('sms.edge_url'), '/'))
            ->timeout(35);

        $response = $method === 'GET'
            ? $client->get($path, $query)
            : $client->asJson()->post($path, $body);

        if ($response->status() === 401) {
            throw new RuntimeException('کلید IPPanel نامعتبر است. تنظیمات را بررسی کنید.');
        }

        if (! $response->successful()) {
            $payload = $response->json();
            $message = $payload['meta']['message'] ?? $response->body();

            throw new RuntimeException('خطای IPPanel: '.$message);
        }

        $payload = $response->json();

        if (($payload['meta']['status'] ?? false) === false) {
            throw new RuntimeException('خطای IPPanel: '.($payload['meta']['message'] ?? 'نامشخص'));
        }

        return $payload;
    }

    public function credit(): array
    {
        return $this->request('GET', '/api/payment/credit/mine')['data'] ?? [];
    }

    public function numbers(): array
    {
        return $this->request('GET', '/api/number/numbers', [], ['page' => 1, 'per_page' => 100])['data'] ?? [];
    }

    public function sendWebservice(string $message, array $recipients, ?string $from = null): array
    {
        return $this->request('POST', '/api/send', [
            'sending_type' => 'webservice',
            'from_number' => $from ?: $this->defaultSender(),
            'message' => $message,
            'params' => ['recipients' => array_map([self::class, 'normalizePhone'], $recipients)],
        ])['data'] ?? [];
    }

    public function sendPattern(string $code, string $recipient, array $params, ?string $from = null): array
    {
        return $this->request('POST', '/api/send', [
            'sending_type' => 'pattern',
            'from_number' => $from ?: $this->defaultSender(),
            'code' => $code,
            'recipients' => [self::normalizePhone($recipient)],
            'params' => $params,
        ])['data'] ?? [];
    }

    public static function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', trim($phone));
        $phone = ltrim($phone, '0');

        if (! str_starts_with($phone, '+')) {
            $phone = '+98'.$phone;
        }

        return $phone;
    }
}
