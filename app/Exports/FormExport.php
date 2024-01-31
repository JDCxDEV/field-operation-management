<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FormExport implements FromView, ShouldAutoSize
{
    private $forms = [];

    public function __construct($forms) 
    {
        $this->forms = $forms;
    }

    public function view(): View
    {
        return view('exports.forms-excel', [
            'forms' => $this->forms
        ]);
    }
}
