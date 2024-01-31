<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Market;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoordinatorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::get();
        $exemptions = ["agent@demo.com", "market@demo.com", "company@demo.com", "admin@demo.com", "demo@demo.com"];
        foreach($companies as $key => $company) {
            $users = $company->users->whereNotIn("email", $exemptions)->pluck("user_id")->toArray();
            $id = $users[rand(0, count($users) - 1)];
            $company->coordinators()->syncWithoutDetaching($id);
            if($id) {
                User::where("id", $id)->update(["role_type" => User::COMPANY_COORDINATOR]);
            }
        }

        $markets = Market::get();
        foreach($markets as $key => $market) {
            $users = $market->users->whereNotIn("email", $exemptions)->pluck("user_id")->toArray();
            $id = $users[rand(0, count($users) - 1)];
            $market->directors()->syncWithoutDetaching($id);

            if($id) {
                $isCompanyCoordinator = User::where(["id" => $id, "role_type" => User::COMPANY_COORDINATOR])->first();
                if($isCompanyCoordinator) {
                    $isCompanyCoordinator->update(["role_type" => User::COMPANY_AND_MARKET_COORDINATOR]);
                } else {
                    User::where("id", $id)->update(["role_type" => User::MARKET_DIRECTOR]);
                }
            }

        }
    }
}
