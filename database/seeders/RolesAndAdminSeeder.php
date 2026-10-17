<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view-admin',
            'manage-catalog',
            'manage-orders',
            'manage-customers',
            'manage-marketing',
            'manage-content',
            'manage-settings',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $roles = [
            'super-admin' => $permissions,
            'manager' => $permissions,
            'seller' => ['view-admin', 'manage-orders', 'manage-customers'],
            'warehouse' => ['view-admin', 'manage-orders'],
        ];

        foreach ($roles as $role => $perms) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web'])
                ->syncPermissions($perms);
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@arezouhome.test'],
            ['name' => 'مدیر فروشگاه', 'password' => 'password'],
        );
        $admin->syncRoles(['super-admin']);
    }
}
