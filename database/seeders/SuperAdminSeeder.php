<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Generator;
use App\Models\User;
use App\Models\UserInfo;
use Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Generator $faker)
    {
        $superAdmin = User::create([
            'first_name'        => "FieldOps",
            'last_name'         => "Admin",
            'email'             => 'fieldops.admin@gmail.com',
            'password'          => Hash::make('p@$$w0rd!23'),
            'role_type'         => User::SUPER_ADMIN,
            'email_verified_at' => now(),
            'api_token'         => Hash::make('fieldops.admin@gmail.com'),
        ]);

        $this->addDummyInfo($faker, $superAdmin);        
    }

    private function addDummyInfo(Generator $faker, User $user)
    {
        
        $dummyInfo = [
            "phone"    => $faker->numerify('##########'),
            'website'  => $faker->url,
            'language' => $faker->languageCode,
            'country'  => $faker->countryCode,
        ];

        $info = new UserInfo();
        foreach ($dummyInfo as $key => $value) {
            $info->$key = $value;
        }
        $info->user()->associate($user);
        $info->save();
    }
}
