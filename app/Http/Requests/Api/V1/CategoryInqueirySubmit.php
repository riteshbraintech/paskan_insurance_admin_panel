<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CategoryInqueirySubmit extends FormRequest
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
        
        
        // get form field count from category slug
        $category_id = $this->input('category_id');
        $field_count = 0;
        if ($category_id) {
            $field_count = \App\Models\Categoryformfield::where('category_id', $category_id)->count();
        }
        
        // validation rules
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'choosenAnswer' => ['required', 'array', 'min:' . $field_count],
        ];
        
        // if user_id is null then check user array from request and validate user fields
        if (is_null($this->input('user_id'))) {
            return array_merge($rules,[
                'user.name' => 'required|string|max:255',
                'user.email' => 'required|email|max:255',
                'user.phone' => 'required|string|max:20',
            ]);
        }else{
            return array_merge($rules,[
                'user_id' => 'required',
            ]);
        }
            


        return $rules;
    }

    /**
     * Return JSON on validation failure.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 403)
        );
    }
}
