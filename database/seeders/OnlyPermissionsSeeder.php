<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class OnlyPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'about_edit',
            'create_admins',
            'create_banners',
            'create_categories',
            'create_coupons',
            'create_footer_images',
            'create_permissions',
            'create_production_companies',
            'create_products',
            'delete_admins',
            'delete_banners',
            'delete_categories',
            'delete_contact',
            'delete_coupons',
            'delete_footer_images',
            'delete_notifications',
            'delete_orders',
            'delete_permissions',
            'delete_production_companies',
            'delete_products',
            'edit_admins',
            'edit_banners',
            'edit_categories',
            'edit_coupons',
            'edit_orders',
            'edit_permissions',
            'edit_production_companies',
            'edit_products',
            'mark_notifications',
            'roles_create',
            'roles_delete',
            'roles_edit',
            'service_items_create',
            'service_items_delete',
            'service_items_edit',
            'services_intro_edit',
            'social_links_delete',
            'social_links_edit',
            'stay_in_touch_edit',
            'subcategories_create',
            'subcategories_delete',
            'subcategories_edit',
            'team_members_create',
            'team_members_delete',
            'team_members_edit',
            'view_orders',
            'view_products',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin', // ← هنا التعديل
            ]);
        }
    }
}
