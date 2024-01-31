<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserInfo;
use App\Models\Company;
use App\Models\Market;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Generator $faker)
    {
        $demoUser = User::create([
            'first_name'        => $faker->firstName,
            'last_name'         => $faker->lastName,
            'email'             => 'demo@demo.com',
            'password'          => Hash::make('demo'),
            'role_type'         => User::SUPER_ADMIN,
            'email_verified_at' => now(),
            'api_token'         => Hash::make('demo@demo'),
        ]);

        $this->addDummyInfo($faker, $demoUser, true);

        $demoUser2 = User::create([
            'first_name'        => $faker->firstName,
            'last_name'         => $faker->lastName,
            'email'             => 'admin@demo.com',
            'password'          => Hash::make('demo'),
            'email_verified_at' => now(),
            'role_type'         => User::SUPER_ADMIN,
            'api_token'         => Hash::make('admin@demo'),
        ]);

        $this->addDummyInfo($faker, $demoUser2, true);

        $companyCoodinator = User::create([
            'first_name'        => $faker->firstName,
            'last_name'         => $faker->lastName,
            'email'             => 'company@demo.com',
            'password'          => Hash::make('demo'),
            'email_verified_at' => now(),
            'role_type'         => User::COMPANY_COORDINATOR,
            'api_token'         => Hash::make('company@demo.com'),
        ]);
        $this->addDummyInfo($faker, $companyCoodinator, false);

        $marketCoodinator = User::create([
            'first_name'        => $faker->firstName,
            'last_name'         => $faker->lastName,
            'email'             => 'market@demo.com',
            'password'          => Hash::make('demo'),
            'email_verified_at' => now(),
            'role_type'         => User::MARKET_DIRECTOR,
            'api_token'         => Hash::make('market@demo.com'),
        ]);

        $this->addDummyInfo($faker, $marketCoodinator, false);

        $agent = User::create([
            'first_name'        => $faker->firstName,
            'last_name'         => $faker->lastName,
            'email'             => 'agent@demo.com',
            'password'          => Hash::make('demo'),
            'email_verified_at' => now(),
            'role_type'         => User::AGENT,
            'api_token'         => Hash::make('agent@demo.com'),
        ]);

        $this->addDummyInfo($faker, $agent, false);

        User::factory(100)->create()->each(function (User $user) use ($faker) {
            $this->addDummyInfo($faker, $user);
        });
        
        User::factory(10)->create()->each(function (User $user) use ($faker) {
            $this->addDummyInfo($faker, $user, true);
        });
    }

    private function addDummyInfo(Generator $faker, User $user, $noCompany = false)
    {
        $company = $noCompany ? null : $faker->numberBetween(1, 10);
        $market = null;

        if($company) {
            $companyData = Company::find($company);
            if($user->email == "company@demo.com") {
                $companyData->coordinators()->sync([$user->id]);
            }
            $marketIds = $companyData->markets->pluck("id")->toArray();
            if(count($marketIds)) {
                $market = $marketIds[rand(0, count($marketIds) - 1)];
                if($user->email == "market@demo.com") {
                    $marketData = Market::find($market);
                    $marketData->directors()->sync([$user->id]);
                }
            }
        }
        
        $dummyInfo = [
            'company_id'  => $company,
            'market_id'  => $market,
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
