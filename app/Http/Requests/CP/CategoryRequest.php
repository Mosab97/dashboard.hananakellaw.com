<?php

namespace App\Http\Requests\CP;

use Illuminate\Foundation\Http\FormRequest;

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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'name' => 'required|array',
            'name.he' => 'nullable|string|max:255',
            'name.ar' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'active' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'restaurant_id' => 'required|exists:restaurants,id',
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
            'name.required' => t('Category name is required'),
            'name.array' => t('Category name must be provided in multiple languages'),
            'name.ar.required' => t('Arabic name is required'),
            'name.he.string' => t('English name must be a string'),
            'name.ar.string' => t('Arabic name must be a string'),
            'name.*.max' => t('Category name must not exceed 255 characters'),

            'image.image' => t('Image must be an image file'),
            'image.mimes' => t('Image must be a file of type: jpeg, png, jpg, gif, svg'),
            'image.max' => t('Image file size must not exceed 2MB'),

            'icon.image' => t('Icon must be an image file'),
            'icon.mimes' => t('Icon must be a file of type: jpeg, png, jpg, gif, svg'),
            'icon.max' => t('Icon file size must not exceed 1MB'),

            'active.boolean' => t('Active status must be true or false'),

            'order.integer' => t('Order must be a number'),
            'order.min' => t('Order must be at least 0'),

            'restaurant_id.required' => t('Restaurant is required'),
            'restaurant_id.exists' => t('Selected restaurant does not exist'),
        ];
    }
    public function prepareForValidation()
    {
        $this->merge([
            'active' => $this->has('active') ? true : false,
            'restaurant_id' => getFirstRestaurant()->id ?? null,
        ]);
    }
}
