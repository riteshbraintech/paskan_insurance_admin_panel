<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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

        // base rules for categories table
        $rules = [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique(Category::class, 'title')->ignore($pageId)
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique(Category::class, 'slug')->ignore($pageId)
            ],
            'is_active' => 'nullable|boolean',
            'is_link' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            // translation main array required
            'trans' => ['required', 'array', 'min:1'],
        ];

        // Translation rules per language
        foreach ($this->input('trans', []) as $langCode => $fields) {

            $rules["trans.$langCode.title"] = [
                'required',
                'string',
                'max:255',
                Rule::unique(CategoryTranslation::class, 'title')
                    ->ignore($this->getTranslationId($langCode))
                    ->where(fn($query) => $query->where('lang_code', $langCode))
            ];

            $rules["trans.$langCode.description"] = 'required|string';
            $rules["trans.$langCode.meta_title"] = 'required|string|max:255';
            $rules["trans.$langCode.meta_description"] = 'required|string|max:255';
            $rules["trans.$langCode.meta_keywords"] = 'required|string';
            $rules["trans.$langCode.slug"] = [
                'nullable',
                'string',
                'max:255',
                Rule::unique(CategoryTranslation::class, 'slug')
                    ->ignore($this->getTranslationId($langCode))
                    ->where(fn($query) => $query->where('lang_code', $langCode))
            ];
        }

        return $rules;
    }

    protected function getTranslationId($langCode)
    {
        return $this->input("trans.$langCode.id");
    }
}
