<?php

namespace App\Http\Requests\Companies;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
            "company" => "required",
            "phone" => "required|min:10|unique:companies,phone,". $id,
            "address_1" => "required",
            "city" => "required",
            "state" => "required",
            "zip" => "required"
        ];
    }

    public function messages() {
        return [
            "company.required" => "Company name is required",
            "phone.required" => "Phone number is required",
            "phone.min" => "Invalid phone number",
            "address_1.required" => "Address Line 1 is required",
            "city.required" => "City is required",
            "state.required" => "State is required",
            "zip.required" => "Zip is required",          
        ];
    }
}
