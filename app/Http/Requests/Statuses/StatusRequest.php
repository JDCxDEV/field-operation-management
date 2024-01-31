<?php

namespace App\Http\Requests\Statuses;

use Illuminate\Foundation\Http\FormRequest;

class StatusRequest extends FormRequest
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
            "status" => "required",
            "definition" => "required",
        ];
    }

    public function messages() 
    {
        return [
            "status.required" => "Status is required",
            "definition.required" => "Definition is required",
        ];
    }
}
