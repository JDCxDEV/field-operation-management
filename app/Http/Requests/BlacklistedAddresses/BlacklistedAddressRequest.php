<?php

namespace App\Http\Requests\BlacklistedAddresses;

use Illuminate\Foundation\Http\FormRequest;

class BlacklistedAddressRequest extends FormRequest
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
            "address_1" => "required",
            "city" => "required",
            "state" => "required",
            "zip" => "required"
        ];
    }

    public function messages() 
    {
        return [
            "address_1.required" => "Address Line 1 is required",
            "city.required" => "City is required",
            "state.required" => "State is required",
            "zip.required" => "Zip is required",          
        ];
    }
}
