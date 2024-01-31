<?php

namespace App\Http\Requests\Forms;

use Illuminate\Foundation\Http\FormRequest;

class ClientAddressValidationRequest extends FormRequest
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
            "client_sex" => ["required"],
            "client_email" => ["required", "email"],
            "client_address_line_1" => ["required"],
            "client_city" => ["required"],
            "client_state" => ["required"],
            "client_zip" => ["required"],
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
            "client_sex.required" => "Client sex is required",
            "client_email.required" => "Client email is required",
            "client_address_line_1.required" => "Client address line one is required",
            "client_city.required" => "Client city is required",
            "client_state.required" => "Client state is required",
            "client_zip.required" => "Client zip is required",
        ];
    }
}
