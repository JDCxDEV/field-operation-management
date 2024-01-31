<?php

namespace App\Http\Requests\Forms;

use Illuminate\Foundation\Http\FormRequest;

class DependentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "dependent_first_name" => ["required"],
            "dependent_last_name" => ["required"],
            "dependent_sex" => ["required"],
            "dependent_dob" => ["required", "date"],
            "dependent_ssn" => ["required", "min:9", "max:9"],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function messages()
    {
        return [
            "dependent_first_name.required" => "Dependent first name is required",
            "dependent_last_name.required" => "Dependent last name is required",
            "dependent_sex.required" => "Dependent gender is required",
            "dependent_dob.required" => "Dependent date of birth is required",
            "dependent_dob.date" => "Dependent date of birth is invalid",
            "dependent_ssn.required" => "Dependent SSN is required",
            "dependent_ssn.min" => "Dependent SSN is invalid",
            "dependent_ssn.max" => "Dependent SSN is invalid",    
        ];
    }
}
