<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Define the validation rules dynamically.
     */
    public function rules()
    {
        $userId = $this->user()->id ?? null;
        $rules = [];

        if ($this->has('name')) {
            $rules['name'] = 'string|max:255';
        }

        if ($this->has('email')) {
            $rules['email'] = 'email|unique:users,email,' . $userId;
        }

        if ($this->has('phone')) {
            $rules['phone'] = 'string|max:20';
        }

        if ($this->has('gender')) {
            $rules['gender'] = 'in:male,female,other';
        }

        if ($this->has('dob')) {
            $rules['dob'] = 'date';
        }

        if ($this->has('address')) {
            $rules['address'] = 'string|max:500';
        }

        if ($this->has('postcode')) {
            $rules['postcode'] = 'string|max:20';
        }

        if ($this->has('marital_status')) {
            $rules['marital_status'] = 'string|max:30';
        }

        if ($this->has('nationality')) {
            $rules['nationality'] = 'string|max:80';
        }

        if ($this->hasFile('image')) {
            $rules['image'] = 'image|mimes:jpg,jpeg,png|max:2048';
        }

        return $rules;
    }

    /**
     * Custom messages for each validation rule.
     */
    public function messages()
    {
        $messages = [];

        if ($this->has('name')) {
            $messages['name.string'] = 'Name must be a valid string.';
            $messages['name.max'] = 'Name cannot exceed 255 characters.';
        }

        if ($this->has('email')) {
            $messages['email.email'] = 'Please provide a valid email address.';
            $messages['email.unique'] = 'This email is already registered.';
        }

        if ($this->has('phone')) {
            $messages['phone.string'] = 'Phone number must be a valid string.';
            $messages['phone.max'] = 'Phone number cannot exceed 20 characters.';
        }

        if ($this->has('gender')) {
            $messages['gender.in'] = 'Gender must be one of: male, female, or other.';
        }

        if ($this->has('dob')) {
            $messages['dob.date'] = 'Please enter a valid date of birth.';
        }

        if ($this->has('address')) {
            $messages['address.string'] = 'Address must be a valid string.';
            $messages['address.max'] = 'Address cannot exceed 500 characters.';
        }

        if ($this->has('postcode')) {
            $messages['postcode.string'] = 'Postcode must be a valid string.';
            $messages['postcode.max'] = 'Postcode cannot exceed 20 characters.';
        }

        if ($this->hasFile('image')) {
            $messages['image.image'] = 'Please upload a valid image.';
            $messages['image.mimes'] = 'Image must be a JPG, JPEG, or PNG file.';
            $messages['image.max'] = 'Image size cannot exceed 2MB.';
        }

        return $messages;
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
