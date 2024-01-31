<?php

namespace App\Http\Requests\Forms;

use Illuminate\Foundation\Http\FormRequest;

class EmploymentDataRequest extends FormRequest
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
            "employer_name" => ["required"],
            "employer_phone" => ["nullable", "min:10"],
            "income" => ["required"],
            "income_frequency" => ["required"]
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
            "employer_name.required" => "Employer name is required",
            "employer_phone.min" => "Invalid employer phone number",            
            "income.required" => "Income is required",
            "income_frequency.required" => "Income frequency is required",
        ];
    }
}
