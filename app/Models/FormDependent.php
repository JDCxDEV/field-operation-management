<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Form;

class FormDependent extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "form_id",
        "dependent_first_name",
        "dependent_last_name",
        "dependent_sex",
        "dependent_dob",
        "dependent_covered",
        "dependent_coverage_type",
        "dependent_ssn"
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
