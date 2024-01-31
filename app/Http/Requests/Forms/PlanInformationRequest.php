<?php

namespace App\Http\Requests\Forms;

use Illuminate\Foundation\Http\FormRequest;

class PlanInformationRequest extends FormRequest
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
            "tax_credit_amount" => ["required"],
            "plan_premium" => ["required"],
            "you_pay" => ["required"],
            "plan_name" => ["required"],
            "plan_id" => ["required"],
            "insurance" => ["required"]
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
            "tax_credit_amount.required" => "Tax credit amount is required",
            "plan_premium.required" => "Plan premium is required",
            "you_pay.required" => "You pay is required",
            "plan_name.required" => "Plan name is required",
            "plan_id.required" => "Plan ID is required",
            "insurance.required" => "Insurance is required"
        ];
    }    
}
