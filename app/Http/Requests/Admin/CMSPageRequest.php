<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Languages;
use App\Models\CMSPageTranslation;

class CMSPageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $pageId = $this->route('cmspage'); // For update mode

        $rules = [
            // 'title' => ['required', 'string', 'max:255'],
            // 'slug' => [
            //     'required',
            //     'string',
            //     'max:255',
            //     Rule::unique('cms_pages', 'slug')->ignore($pageId),
            // ],
            // 'is_published' => ['required', 'boolean'],
            'trans' => ['required', 'array', 'min:1'],
        ];

        // Define common translation rules
        $translationRules = [
            'content' => 'required|string',
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'required|string|max:500',
            'meta_keywords' => 'required|string|max:500',
        ];

        // Apply per-language title uniqueness and other fields
        foreach ($this->input('trans', []) as $langCode => $fields) {
            // Unique title per language (in cms_page_translations table)
            $rules["trans.$langCode.title"] = [
                'required',
                'string',
                'max:255',
                Rule::unique(CMSPageTranslation::class, 'title')
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

    public function messages()
    {
        $languages = Languages::getLanguages(); // ['en' => 'English', 'th' => 'Thai']
        $messages = [
            'title.required' => 'The title field is required.',
            'slug.required' => 'The slug field is required.',
            'is_published.required' => 'The published status is required.',
        ];

        foreach ($languages as $code => $name) {
            $messages["trans.$code.title.required"] = "The $name title field is required.";
            $messages["trans.$code.title.unique"] = "The $name title must be unique.";
            $messages["trans.$code.content.required"] = "The $name content field is required.";
            $messages["trans.$code.meta_title.required"] = "The $name meta title field is required.";
            $messages["trans.$code.meta_description.required"] = "The $name meta description field is required.";
            $messages["trans.$code.meta_keywords.required"] = "The $name meta keywords field is required.";
        }

        return $messages;
    }
}
