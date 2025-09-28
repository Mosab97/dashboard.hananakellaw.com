<?php

namespace App\Http\Requests\CP;

use Illuminate\Foundation\Http\FormRequest;

class ArticleContentRequest extends FormRequest
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
            'title' => 'required|array',
            'title.he' => 'nullable|string|max:255',
            'title.ar' => 'required|string|max:255',
            'features' => 'required|array',
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
            'title.required' => t('Title is required'),
            'title.array' => t('Title must be provided in multiple languages'),
            'title.ar.required' => t('Arabic title is required'),
            'title.he.string' => t('English title must be a string'),
            'title.ar.string' => t('Arabic title must be a string'),
            'features.required' => t('Features are required'),
            'features.array' => t('Features must be provided in multiple languages'),
            'features.*.nullable' => t('Each feature field is required'),

            'active.boolean' => t('Active status must be true or false'),

        ];
    }
    // public function getFeaturesArray(): array
    // {
    //     $features = $this->input('features', []);
    //     return array_column($features, 'text');
    // }

    public function prepareForValidation()
    {
        $this->merge([
            'active' => $this->has('active') ? true : false,
            // 'features' => $this->getFeaturesArray(),
        ]);
    }
}
