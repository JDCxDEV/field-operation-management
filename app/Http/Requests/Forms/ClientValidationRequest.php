<?php

namespace App\Http\Requests\Forms;

use Illuminate\Foundation\Http\FormRequest;

class ClientValidationRequest extends FormRequest
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
            "client_first_name" => ["required"],
            "client_phone" => ["required", "min:10"],
            "client_last_name" => ["required"],
            "client_dob" => ["required", "date", "before:-18 years"],
            "client_ssn" => ["required"],
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
            "client_first_name.required" => "Client first name is required",
            "client_last_name.required" => "Client last name is required",
            "client_dob.required" => "Client birthdate is required",
            "client_dob.before" => "Client age must be 18 years above",
            "client_dob.date" => "Client birthdate is invalid",
            "client_ssn.required" => "Client ssn is required",
            "client_phone.required" => "Client phone is required",
            "client_phone.min" => "Invalid client phone",            
        ];
    }    
}
