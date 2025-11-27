<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Languages;
use App\Models\CategoryFieldFormTranslation;
use App\Models\Categoryformfield;

class CategoryFormFieldsRequest extends FormRequest
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

        // dd($this->all());
        $pageId = $this->route('id');
        // dd($pageId);
       
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255', 
                Rule::unique(Categoryformfield::class, 'name')->ignore($pageId)
            ],
            'type' => 'required|string',
            'parent_field_id' => 'nullable',
            'is_required' => 'nullable',
            'trans' => ['required', 'array', 'min:1'],
        ];

        if(is_null($pageId)){
            $rules['category_id'] = 'required|exists:categories,id';
        }

        // Define common translation rules
        $translationRules = [
            'place_holder' => 'nullable|string|max:255',
        ];

        // Apply per-language title uniqueness and other fields
        foreach ($this->input('trans', []) as $langCode => $fields) {
            // dd($langCode, $fields);

            // Unique title per language (in cms_page_translations table)
            $rules["trans.$langCode.label"] = [
                'required',
                'string',
                'max:255',
                Rule::unique(CategoryFieldFormTranslation::class, 'label')
                    ->ignore($this->getTranslationId($langCode)) // ignore existing record when updating
                    ->where(fn($query) => $query->where('lang_code', $langCode))
            ];

            foreach ($translationRules as $key => $rule) {
                $rules["trans.$langCode.$key"] = $rule;
            }
        }



        return $rules;
    }

    /**
     * Dynamically fetch the translation ID (if available in form data)
     * so we can ignore it during update validation.
     */
    protected function getTranslationId($langCode)
    {
        return $this->input("trans.$langCode.id");
    }


    // public function messages()
    // {
    //     $languages = Languages::getLanguages(); // ['en' => 'English', 'th' => 'Thai']
    //     $messages = [
    //         'title.required' => 'The title field is required.',
    //         'slug.required' => 'The slug field is required.',
    //         'is_published.required' => 'The published status is required.',
    //     ];

    //     foreach ($languages as $code => $name) {
    //         $messages["trans.$code.title.required"] = "The $name title field is required.";
    //         $messages["trans.$code.title.unique"] = "The $name title must be unique.";
    //         $messages["trans.$code.content.required"] = "The $name content field is required.";
    //         $messages["trans.$code.meta_title.required"] = "The $name meta title field is required.";
    //         $messages["trans.$code.meta_description.required"] = "The $name meta description field is required.";
    //         $messages["trans.$code.meta_keywords.required"] = "The $name meta keywords field is required.";
    //     }

    //     return $messages;
    // }

}
