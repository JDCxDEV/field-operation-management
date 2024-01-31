<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CompaniesSeeder::class,
            MarketsSeeder::class,
            UsersSeeder::class,
            RecruitsSeeder::class,
            CoordinatorsSeeder::class,
            BlacklistedAddressesSeeder::class,
            FormTemplatesSeeder::class,
            FaqSeeder::class,
            StatusesSeeder::class 
            // PermissionsSeeder::class,
            // RolesSeeder::class,
        ]);
    }
}
