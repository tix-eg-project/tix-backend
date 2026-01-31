<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            //AdminSeeder::class,
            // UserSeeder::class,
            //CategorySeed::class,
            //CountrySeed::class,
            //CitySeed::class,
            //VendorSeeder::class,
            // AllTablesSeeder::class

            RolePermissionSeeder::class,
            OnlyPermissionsSeeder::class,
            // AssignPermissionsToSuperAdminSeeder::class,


        ]);
    }
}
