<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SuperAdminSeeder::class,
            BlacklistedAddressesSeeder::class,
            FormTemplatesSeeder::class,
            FaqSeeder::class,
            StatusesSeeder::class,
        ]);
    }
}
