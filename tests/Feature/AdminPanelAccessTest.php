<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_user_without_role_gets_forbidden(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_super_admin_can_access_panel(): void
    {
        $this->seed(RolesAndAdminSeeder::class);
        $admin = User::where('email', 'admin@arezouhome.test')->firstOrFail();

        $this->actingAs($admin)->get('/admin')->assertOk();
    }
}
