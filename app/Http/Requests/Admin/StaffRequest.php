<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StaffRequest extends FormRequest
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
            'name' => 'required|string',
            "gender" => 'required',
            "date_of_birth" => 'required|date',
            "phone" => 'required|between:6,10',
            "dial_code"=> 'required',
            "country"=> 'required',
            "state"=> 'required',
            "image"=>'nullable'

        ];
    }
}
