<?php

namespace App\Http\Requests\Forms;

use Illuminate\Foundation\Http\FormRequest;

class SpousalRequest extends FormRequest
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
            "spousal_first_name" => ["required"],
            "spousal_last_name" => ["required"],
            "spousal_dob" => ["required", "date", "before:-18 years"],
            "spousal_ssn" => ["nullable", "min:9", "max:9"],
            "spousal_employer_name" => ["required"],
            "spousal_employer_phone" => ["nullable", "min:10"],
            "spousal_income" => ["required"],
            "spousal_type" => ["required"],
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
            "spousal_first_name.required" => "Spousal first name is required",
            "spousal_last_name.required" => "Spousal last name is required",
            "spousal_dob.required" => "Spousal date of birth is required",
            "spousal_dob.before" => "Spousal age must be 18 years above",
            // "spousal_ssn.required" => "Spousal SSN is required",
            "spousal_ssn.min" => "Spousal SSN is invalid",
            "spousal_ssn.max" => "Spousal SSN is invalid",
            "spousal_employer_name.required" => "Spousal employer name is required",
            "spousal_employer_phone.required" => "Spousal employer phone is required",
            "spousal_employer_phone.min" => "Spousal employer phone is invalid",
            "spousal_employer_phone.max" => "Spousal employer phone is invalid",
            "spousal_income.required" => "Spousal income is required",
            "spousal_type.required" => "Spousal relationship type is required",  
        ];
    }
}
