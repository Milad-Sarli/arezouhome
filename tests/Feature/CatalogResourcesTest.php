<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CatalogResourcesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function indexPages(): array
    {
        return [
            'brands' => ['/admin/brands'],
            'categories' => ['/admin/categories'],
            'colors' => ['/admin/colors'],
            'warranties' => ['/admin/warranties'],
            'product attrs' => ['/admin/product-attrs'],
            'products' => ['/admin/products'],
            'provinces' => ['/admin/provinces'],
            'cities' => ['/admin/cities'],
            'shipments' => ['/admin/shipments'],
        ];
    }

    #[DataProvider('indexPages')]
    public function test_admin_can_load_resource_index(string $url): void
    {
        $this->seed(RolesAndAdminSeeder::class);
        $admin = User::where('email', 'admin@arezouhome.test')->firstOrFail();

        $this->actingAs($admin)->get($url)->assertOk();
    }

    public function test_admin_can_open_product_create_wizard(): void
    {
        $this->seed(RolesAndAdminSeeder::class);
        $admin = User::where('email', 'admin@arezouhome.test')->firstOrFail();

        $this->actingAs($admin)->get('/admin/products/create')->assertOk();
    }
}
