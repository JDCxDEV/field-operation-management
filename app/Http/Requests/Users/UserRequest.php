<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\PhoneRule;

class UserRequest extends FormRequest
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
        $required = $this->route('id') ? 'nullable': 'required';

        return [
            "first_name" => ["required"],
            "last_name" => ["required"],
            "email" => [$required,'email', 'unique:users,email,'. $id],
            "phone" => ["required", "min:10", 'unique:user_infos,phone,'. $id, new PhoneRule],
            // "website" => ["required"],
            "company_id" => ["required"],
            "market_id" => ["required"],
            // "password" => [$required, 'string', 'min:8']
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
            "phone.required" => "Phone number is required",
            "phone.min" => "Invalid phone number",            
            "company_id.required" => "Company is required",
            "market_id.required" => "Market is required",
        ];
    }
}
