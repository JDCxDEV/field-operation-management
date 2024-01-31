<?php

namespace App\Http\Requests\Markets;

use Illuminate\Foundation\Http\FormRequest;

class MarketRequest extends FormRequest
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
        $id = $this->route('id');

        return [
            "company_id" => "required",
            "market_name" => "required",
            "email" => "email|required",
            "phone" => "required|min:10|unique:markets,phone,". $id,
            "address_1" => "required",
            "city" => "required",
            "state" => "required",
            "zip" => "required"
        ];
    }

    public function messages() 
    {
        return [
            "company_id.required" => "Company is required",
            "market_name.required" => "Market name is required",
            "phone.required" => "Phone number is required",
            "phone.min" => "Invalid phone number",
            "address_1.required" => "Address Line 1 is required",
            "city.required" => "City is required",
            "state.required" => "State is required",
            "zip.required" => "Zip is required",
        ];
    }
}
