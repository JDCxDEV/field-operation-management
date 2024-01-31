<?php

namespace App\Http\Requests\Account;

use App\Rules\MatchOldPassword;
use Illuminate\Foundation\Http\FormRequest;

class SettingsEmailRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {

        $id = $this->route('id');
                
        return [
            'email'            => 'required|string|email|max:255|unique:users,email,'. $id,
            'current_password' => ['required', new MatchOldPassword],
        ];
    }
}
