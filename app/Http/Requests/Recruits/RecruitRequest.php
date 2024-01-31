<?php

namespace App\Http\Requests\Recruits;

use Illuminate\Foundation\Http\FormRequest;

class RecruitRequest extends FormRequest
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
        $this->id = $this->route('id');
        $required = $this->route('id') ? 'nullable': 'required';

        return [
            "name" => ["required"],
            'phone' => ["required", "min: 10"],
            "email" => [$required, 'email','unique:recruits,id,'. $this->id],
            "company_id" => ["required"],
            "market_id" => ["required"]
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
            "phone.required" => "Phone Number is required",
            "phone.min" => "Invalid phone number",
            "company_id.required" => "Company is required",
            "market_id.required" => "Market is required",
        ];
    }
}
