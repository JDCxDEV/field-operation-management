<?php

namespace App\Http\Requests\Forms;

use Illuminate\Foundation\Http\FormRequest;

class AdditionalFilesRequest extends FormRequest
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
            "main_id_type" => ["required"],
            "main_id_file" => ["required", "image"],
            "other_file" => ["required", "image"],
            "image_with_form" => ["required", "image"],
            "additional_file_one" => ["required", "image"],
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
            "main_id_type.required" => "Acceptable forms of ID is required",
            "main_id_file.required" => "Main ID is required",
            "other_file.required" => "Other ID is required",
            "image_with_form.required" => "Clear Image of Person with Acknowledgment Form is required",
            "additional_file_one.required" => "Additional file is required",
        ];
    }
}
