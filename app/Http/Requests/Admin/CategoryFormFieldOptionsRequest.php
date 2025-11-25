<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\CategoryFieldFormOptionsTranslation;

class CategoryFormFieldOptionsRequest extends FormRequest
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
        $pageId = $this->route('id');
        // dd($pageId);
        
        $rules = [
            'field_id' => 'required',
            'parent_option_id' => 'nullable',
            'value' => 'required',
            'trans' => ['required', 'array', 'min:1'],
        ];
        
        // Define common translation rules
        $translationRules = [
            'images' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2043',
        ];
        
        // Apply per-language title uniqueness and other fields
        foreach ($this->input('trans', []) as $langCode => $fields) {
            // dd($langCode, $fields);

            // Unique title per language (in cms_page_translations table)
            $rules["trans.$langCode.label"] = [
                'required',
                'string',
                'max:255',
                // Rule::unique(CategoryFieldFormOptionsTranslation::class, 'label')
                // ->ignore($this->getTranslationId($langCode)) // ignore existing record when updating
                // ->where(fn($query) => $query->where('lang_code', $langCode))
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
}
