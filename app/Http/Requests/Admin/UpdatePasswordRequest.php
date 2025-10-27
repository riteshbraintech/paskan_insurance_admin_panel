<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordRequest extends FormRequest
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
        return [
            'update_type'=>'required',
            'old_password' => ['required', function ($attribute, $value, $fail) {
                if (!hash::check($value, admin()->user()->password)) {
                    $fail('The '.$attribute.' is invalid.');
                }
            }],
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ];
    }
}
