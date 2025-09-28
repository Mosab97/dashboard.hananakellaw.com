<?php

namespace App\Http\Requests\CP;

use Illuminate\Foundation\Http\FormRequest;

class SucessStoryRequest extends FormRequest
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
            'owner_name' => 'required|array',
            'owner_name.he' => 'nullable|string|max:255',
            'owner_name.ar' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            // 'rate' => 'nullable|integer|min:1|max:5',
            'description' => 'required|array',
            'description.en' => 'nullable|string|max:255',
            'description.ar' => 'nullable|string|max:255',
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
            'owner_name.required' => t('Owner name is required'),
            'owner_name.array' => t('Owner name must be provided in multiple languages'),
            'owner_name.ar.required' => t('Arabic owner name is required'),
            'owner_name.he.string' => t('English owner name must be a string'),
            'owner_name.ar.string' => t('Arabic owner name must be a string'),
            'owner_name.*.max' => t('Owner name must not exceed 255 characters'),

            //     'rate.required' => t('Rate is required'),
            // 'rate.integer' => t('Rate must be an integer'),
            // 'rate.min' => t('Rate must be at least 1'),
            // 'rate.max' => t('Rate must be at most 5'),

            'description.required' => t('Description is required'),
            'description.array' => t('Description must be provided in multiple languages'),
            'description.ar.required' => t('Arabic description is required'),
            'description.en.string' => t('English description must be a string'),
            'description.ar.string' => t('Arabic description must be a string'),
            'description.*.max' => t('Description must not exceed 255 characters'),

            'active.boolean' => t('Active status must be true or false'),
            'url.required' => t('Video url is required'),
            'url.string' => t('Video url must be a string'),
            'url.max' => t('Video url must not exceed 255 characters'),
            'thumbnail.image' => t('Thumbnail must be an image file'),
            'thumbnail.mimes' => t('Thumbnail must be a file of type: jpeg, png, jpg, gif, svg, webp'),
            'thumbnail.max' => t('Thumbnail file size must not exceed 2MB'),

        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'active' => $this->has('active') ? true : false,
        ]);
    }
}
