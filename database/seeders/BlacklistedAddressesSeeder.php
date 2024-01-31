<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BlacklistedAddressImport;
use App\Models\BlacklistedAddress;

class BlacklistedAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BlacklistedAddress::truncate();
        Excel::import(new BlacklistedAddressImport, storage_path('imports/blacklisted-addresses-final-v2.xlsx'));
    }
}
