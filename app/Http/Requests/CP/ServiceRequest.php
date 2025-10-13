<?php

namespace App\Http\Requests\CP;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
            'description.he' => 'nullable|string',//|max:20000
            'description.ar' => 'nullable|string',//|max:20000
            'short_description' => 'nullable|array',
            'short_description.he' => 'nullable|string',//|max:20000
            'short_description.ar' => 'nullable|string',//|max:20000
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'active' => 'boolean',
            'features' => ['required', 'array'],
            // 'features.en' => ['nullable', 'array'],
            // 'features.ar' => ['nullable', 'array'],
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
        $messages = [
            'title.required' => t('Title is required'),
            'title.array' => t('Title must be provided in multiple languages'),
            'title.ar.required' => t('Arabic title is required'),
            'title.he.string' => t('English title must be a string'),
            'title.ar.string' => t('Arabic title must be a string'),
            'description.array' => t('Description must be provided in multiple languages'),
            'description.en.string' => t('English description must be a string'),
            'description.ar.string' => t('Arabic description must be a string'),
            'short_description.array' => t('Short description must be provided in multiple languages'),
            'short_description.en.string' => t('English short description must be a string'),
            'short_description.ar.string' => t('Arabic short description must be a string'),
            'title.*.max' => t('Title must not exceed 255 characters'),

            'icon.image' => t('Icon must be an image file'),
            'icon.mimes' => t('Icon must be a file of type: jpeg, png, jpg, gif, svg, webp'),
            'icon.max' => t('Icon file size must not exceed 2MB'),

            'active.boolean' => t('Active status must be true or false'),


            'features.required' => t('At least one feature is required'),
            'features.en.required' => t('Each feature field is required'),
            'features.ar.required' => t('Each feature field is required'),
            'features.en.array' => t('Each feature field must be an array'),
            'features.ar.array' => t('Each feature field must be an array'),

        ];
        // dd($messages);
        return $messages;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'active' => $this->has('active') ? true : false,
            // 'features' => $this->getFeaturesArray(),
        ]);
    }

    // /**
    //  * Get the features as a simple array for storage
    //  *
    //  * @return array
    //  */
    // public function getFeaturesArray(): array
    // {
    //     $features_request = collect($this->input('features', []));
    //     return[
    //         'en' => $features_request->pluck('en')->toArray() ?? [],
    //         'ar' => $features_request->pluck('ar')->toArray() ?? [],
    //     ];
    // }
}
