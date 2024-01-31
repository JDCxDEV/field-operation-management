<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\BlacklistedAddress;

class BlacklistedAddressExport implements FromView, ShouldAutoSize
{
    public function view(): View
    {
        return view('exports.blacklisted-address', [
            'addresses' => BlacklistedAddress::where("address_1", "!=", "")->get()
        ]);
    }
}
