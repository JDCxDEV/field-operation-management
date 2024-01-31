<?php

namespace App\Http\Requests\Forms;

use Illuminate\Foundation\Http\FormRequest;

class PaymentInformationRequest extends FormRequest
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
            "plan_selected" => ["required"],
            "date_processed" => ["required"],
            "cc_number" => ["required", "min:13"],
            "cc_expiration_date" => ["required"],
            "cc_cvc" => ["required"],
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
            "plan_selected.required" => "Plan selected is required",
            "date_processed.required" => "Date processed is required",
            "cc_number.required" => "Credit Card is required",
            "cc_number.min" => "Credit Card is invalid",
            "cc_expiration_date.required" => "Credit Card Expiration Date is required",
            "cc_cvc.required" => "Credit Card CVC is required",
        ];
    }
}
