<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SalesResourcesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function indexPages(): array
    {
        return [
            'dashboard' => ['/admin'],
            'orders' => ['/admin/orders'],
            'order create' => ['/admin/orders/create'],
            'transactions' => ['/admin/transactions'],
            'discount codes' => ['/admin/discount-codes'],
            'amazings' => ['/admin/amazings'],
            'installment plans' => ['/admin/installment-plans'],
            'return requests' => ['/admin/return-requests'],
            'tickets' => ['/admin/tickets'],
            'blogs' => ['/admin/blogs'],
            'blog categories' => ['/admin/blog-categories'],
            'sliders' => ['/admin/sliders'],
            'sms topics' => ['/admin/sms-topics'],
            'sms messages' => ['/admin/sms-messages'],
            'users' => ['/admin/users'],
            'users create' => ['/admin/users/create'],
            'comments' => ['/admin/comments'],
            'stock movements' => ['/admin/stock-movements'],
            'settings' => ['/admin/settings'],
        ];
    }

    #[DataProvider('indexPages')]
    public function test_admin_can_load_page(string $url): void
    {
        $this->seed(RolesAndAdminSeeder::class);
        $admin = User::where('email', 'admin@arezouhome.test')->firstOrFail();

        $this->actingAs($admin)->get($url)->assertOk();
    }
}
