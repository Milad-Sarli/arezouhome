<?php

namespace App\Services\Auth;

use App\Models\Setting;
use App\Models\User;
use App\Services\Sms\SmsSenderService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Phone + OTP login. Code is cached 2 minutes and sent via SMS pattern.
 */
class OtpService
{
    public function __construct(private SmsSenderService $sms) {}

    public static function normalize(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '98')) {
            $phone = '0'.substr($phone, 2);
        }

        if (! preg_match('/^09\d{9}$/', $phone)) {
            throw new RuntimeException('شماره موبایل معتبر نیست.');
        }

        return $phone;
    }

    public function send(string $phone): void
    {
        $phone = self::normalize($phone);
        $key = 'otp-send:'.$phone;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            throw new RuntimeException('تعداد تلاش زیاد است. دقایقی بعد امتحان کنید.');
        }

        RateLimiter::hit($key, 600);

        $code = (string) random_int(10000, 99999);
        Cache::put('otp:'.$phone, $code, 120);

        if (app()->isLocal()) {
            Log::info("OTP login code for {$phone}: {$code}");
        }

        $this->sms->sendOtp($phone, $code);
    }

    /**
     * The pending OTP code, exposed only on local without an SMS pattern
     * so developers can auto-fill login forms. Always null in production.
     */
    public static function debugCode(string $phone): ?string
    {
        if (! app()->isLocal() || Setting::get('sms_pattern_login')) {
            return null;
        }

        return Cache::get('otp:'.self::normalize($phone));
    }

    public function verify(string $phone, string $code): User
    {
        $phone = self::normalize($phone);
        $expected = Cache::get('otp:'.$phone);

        if (! $expected || ! hash_equals((string) $expected, trim($code))) {
            throw new RuntimeException('کد تأیید اشتباه است.');
        }

        Cache::forget('otp:'.$phone);

        return User::firstOrCreate(
            ['phone' => $phone],
            ['name' => 'کاربر '.$phone, 'email' => $phone.'@otp.local', 'password' => Str::random(32)]
        );
    }
}
