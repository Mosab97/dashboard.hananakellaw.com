<?php

namespace App\Http\Requests\CP;

use Illuminate\Foundation\Http\FormRequest;

class RestaurantRequest extends FormRequest
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
            'name.en' => 'nullable|string|max:255',
            'name.ar' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string|max:1000',
            'description.ar' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'slug' => 'nullable|string|max:255|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'active' => 'boolean',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'opening_hours' => 'nullable|array',
            'delivery_available' => 'boolean',
            'pickup_available' => 'boolean',
            'dine_in_available' => 'boolean',
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
            'name.required' => t('Restaurant name is required'),
            'name.array' => t('Restaurant name must be provided in multiple languages'),
            'name.ar.required' => t('Arabic name is required'),
            'name.en.string' => t('English name must be a string'),
            'name.ar.string' => t('Arabic name must be a string'),
            'name.*.max' => t('Restaurant name must not exceed 255 characters'),

            'description.array' => t('Description must be provided in multiple languages'),
            'description.*.string' => t('Description must be a string'),
            'description.*.max' => t('Description must not exceed 1000 characters'),

            'logo.image' => t('Logo must be an image file'),
            'logo.mimes' => t('Logo must be a file of type: jpeg, png, jpg, gif, svg'),
            'logo.max' => t('Logo file size must not exceed 2MB'),

            'slug.unique' => t('This slug is already taken'),
            'slug.regex' => t('Slug must contain only lowercase letters, numbers, and hyphens'),
            'slug.max' => t('Slug must not exceed 255 characters'),

            'active.boolean' => t('Active status must be true or false'),

            'address.string' => t('Address must be a string'),
            'address.max' => t('Address must not exceed 500 characters'),

            'phone.string' => t('Phone must be a string'),
            'phone.max' => t('Phone must not exceed 20 characters'),

            'email.email' => t('Please provide a valid email address'),
            'email.unique' => t('This email is already taken'),
            'email.max' => t('Email must not exceed 255 characters'),

            'website.url' => t('Please provide a valid website URL'),
            'website.max' => t('Website URL must not exceed 255 characters'),

            'opening_hours.array' => t('Opening hours must be a valid structure'),
            'opening_hours.*.*.open.date_format' => t('Opening time must be in HH:MM format'),
            'opening_hours.*.*.close.date_format' => t('Closing time must be in HH:MM format'),
            'opening_hours.*.*.close.after' => t('Closing time must be after opening time'),

            'delivery_available.boolean' => t('Delivery availability must be true or false'),
            'pickup_available.boolean' => t('Pickup availability must be true or false'),
            'dine_in_available.boolean' => t('Dine-in availability must be true or false'),
        ];
    }
}
