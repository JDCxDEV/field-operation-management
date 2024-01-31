<?php

namespace Database\Seeders;

use App\Models\Market;
use Illuminate\Database\Seeder;
use Faker\Generator;

class MarketsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Generator $faker)
    {
        $markets = Market::factory(10)->create();
    }
}
