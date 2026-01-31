<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Admin;

class AssignPermissionsToSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Get super_admin role
        $role = Role::where('name', 'super_admin')->where('guard_name', 'admin')->first();

        // Get all permissions for admin guard
        $permissions = Permission::where('guard_name', 'admin')->pluck('name')->toArray();

        // Sync permissions with role
        $role->syncPermissions($permissions);

        // Assign role to user (messi - id 5)
        $user = Admin::find(5); // You can also use: Admin::where('email', 'messi@gmail.com')->first();
        if ($user) {
            $user->assignRole($role);
        }

        // Clear cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        echo "Permissions assigned to super_admin and linked to user messi.\n";
    }
}
