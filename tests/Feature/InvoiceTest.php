<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Database\Seeders\RolesAndAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_invoice(): void
    {
        $order = Order::create([
            'user_id' => User::factory()->create()->id,
            'ref_code' => 'AH-TEST-1',
            'status' => 'paid',
            'final_price_rial' => 2500000,
        ]);

        $this->get(route('admin.orders.invoice', $order))->assertRedirect('/login');
    }

    public function test_admin_can_view_invoice_with_details(): void
    {
        $this->seed(RolesAndAdminSeeder::class);
        $admin = User::where('email', 'admin@arezouhome.test')->firstOrFail();
        $customer = User::factory()->create(['name' => 'مشتری تست', 'phone' => '09121234567']);

        $order = Order::create([
            'user_id' => $customer->id,
            'ref_code' => 'AH-TEST-2',
            'status' => 'paid',
            'post_price_rial' => 500000,
            'discount_price_rial' => 0,
            'final_price_rial' => 3000000,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_name' => 'یخچال تست',
            'quantity' => 1,
            'unit_price_rial' => 2500000,
            'total_rial' => 2500000,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.orders.invoice', $order));

        $response->assertOk();
        $response->assertSee('AH-TEST-2');
        $response->assertSee('یخچال تست');
        $response->assertSee('سیصد هزار تومان');
    }

    public function test_customer_without_role_gets_forbidden_on_invoice(): void
    {
        $customer = User::factory()->create();
        $order = Order::create([
            'user_id' => $customer->id,
            'ref_code' => 'AH-TEST-3',
            'status' => 'paid',
            'final_price_rial' => 100000,
        ]);

        $this->actingAs($customer)->get(route('admin.orders.invoice', $order))->assertForbidden();
    }
}
