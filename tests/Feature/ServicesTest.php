<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Services\Payment\PaymentService;
use App\Services\Sms\IppanelEdgeService;
use App\Services\Sms\SmsSenderService;
use Database\Seeders\RolesAndAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_normalize_phone_converts_to_e164(): void
    {
        $this->assertSame('+989121234567', IppanelEdgeService::normalizePhone('09121234567'));
        $this->assertSame('+989121234567', IppanelEdgeService::normalizePhone('+989121234567'));
    }

    public function test_rial_to_toman_conversion(): void
    {
        $this->assertSame(250000, PaymentService::rialToToman(2500000));
    }

    public function test_payment_driver_and_merchant_come_from_settings(): void
    {
        Setting::set('payment_gateway', 'zarinpal');
        Setting::set('zarinpal_merchant', 'test-merchant-id');

        $service = new PaymentService;

        $this->assertSame('zarinpal', $service->driver());
        $this->assertSame('test-merchant-id', $service->merchantId());
    }

    public function test_sms_service_is_not_configured_without_api_key(): void
    {
        $service = new IppanelEdgeService(apiKey: '');

        $this->assertFalse($service->isConfigured());
        $this->expectException(\RuntimeException::class);
        $service->assertConfigured();
    }

    public function test_order_sms_is_skipped_silently_when_no_pattern_set(): void
    {
        $user = User::factory()->create(['phone' => '09121234567']);
        $order = Order::create([
            'user_id' => $user->id,
            'ref_code' => 'AH-TEST-1',
            'status' => 'pending',
            'final_price_rial' => 100000,
        ]);

        // Must not throw and must not hit the network (no pattern configured).
        app(SmsSenderService::class)->sendOrderPlaced($order);

        $this->assertTrue(true);
    }

    public function test_sms_panel_page_loads_for_admin(): void
    {
        $this->seed(RolesAndAdminSeeder::class);
        $admin = User::where('email', 'admin@arezouhome.test')->firstOrFail();

        $this->actingAs($admin)->get('/admin/sms-panel')->assertOk();
    }
}
