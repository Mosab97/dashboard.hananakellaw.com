<?php

namespace App\Http\Requests\CP;

use Illuminate\Foundation\Http\FormRequest;

class ArticleTypeRequest extends FormRequest
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
        $rules = [
            'name' => 'required|array',
            'name.he' => 'nullable|string|max:255',
            'name.ar' => 'required|string|max:255',
            'active' => 'boolean',
        ];

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => t('Article type name is required'),
            'name.array' => t('Article type name must be provided in multiple languages'),
            'name.ar.required' => t('Arabic name is required'),
            'name.he.string' => t('English name must be a string'),
            'name.ar.string' => t('Arabic name must be a string'),
            'name.*.max' => t('Article type name must not exceed 255 characters'),
            'active.boolean' => t('Active status must be true or false'),

        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'active' => $this->has('active') ? true : false,
        ]);
    }
}
