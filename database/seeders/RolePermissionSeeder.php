<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Admin;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء الصلاحيات
        $permissions = [
            'dashboard_index',

            'categories_index',
            'categories_create',
            'categories_edit',
            'categories_delete',

            'subcategories_index',
            'subcategories_create',
            'subcategories_edit',
            'subcategories_delete',

            'products_index',
            'products_create',
            'products_edit',
            'products_delete',
            'products_show',

            'orders_index',
            'orders_edit',
            'orders_delete',
            'orders_show',

            'coupons_index',
            'coupons_create',
            'coupons_edit',
            'coupons_delete',

            'contact_us_index',
            'contact_us_delete',

            'notifications_index',
            'notifications_delete',
            'notifications_markAsRead',

            'team_members_index',
            'team_members_create',
            'team_members_edit',
            'team_members_delete',

            'service_items_index',
            'service_items_create',
            'service_items_edit',
            'service_items_delete',

            'services_intro_index',
            'services_intro_edit',

            'shipping_index',
            'shipping_update',

            'footer_images_index',
            'footer_images_create',
            'footer_images_delete',

            'social_links_index',
            'social_links_edit',
            'social_links_delete',

            'stay_in_touch_index',
            'stay_in_touch_edit',

            'production_companies_index',
            'production_companies_create',
            'production_companies_edit',
            'production_companies_delete',

            'permissions_index',
            'permissions_create',
            'permissions_edit',
            'permissions_delete',

            'roles_index',
            'roles_create',
            'roles_edit',
            'roles_delete',

            'admins_index',
            'admins_create',
            'admins_edit',
            'admins_delete',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'admin']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'admin']);
        $adminRole->syncPermissions($permissions);

        $admin = Admin::first();
        if ($admin) {
            $admin->assignRole($adminRole);
        }
    }
}
