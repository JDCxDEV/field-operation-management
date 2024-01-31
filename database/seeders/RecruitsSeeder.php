<?php

namespace Database\Seeders;

use App\Models\Recruit;
use Illuminate\Database\Seeder;
use Faker\Generator;

class RecruitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Generator $faker)
    {
        Recruit::factory(50)->create();
    }
}
