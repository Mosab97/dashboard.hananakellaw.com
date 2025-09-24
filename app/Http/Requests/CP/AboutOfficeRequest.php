<?php

namespace App\Http\Requests\CP;

use Illuminate\Foundation\Http\FormRequest;

class AboutOfficeRequest extends FormRequest
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
            'description' => 'nullable|array',
            'description.he' => 'nullable|string|max:255',
            'description.ar' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'active' => 'boolean',
            'order' => 'nullable|integer|min:0',
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
            'title.required' => t('Slider title is required'),
            'title.array' => t('Slider title must be provided in multiple languages'),
            'title.ar.required' => t('Arabic title is required'),
            'title.he.string' => t('English title must be a string'),
            'title.ar.string' => t('Arabic title must be a string'),
            'description.array' => t('Description must be provided in multiple languages'),
            'description.en.string' => t('English description must be a string'),
            'description.ar.string' => t('Arabic description must be a string'),
            'title.*.max' => t('Slider title must not exceed 255 characters'),

            'image.image' => t('Image must be an image file'),
            'image.mimes' => t('Image must be a file of type: jpeg, png, jpg, gif, svg, webp'),
            'image.max' => t('Image file size must not exceed 2MB'),

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
