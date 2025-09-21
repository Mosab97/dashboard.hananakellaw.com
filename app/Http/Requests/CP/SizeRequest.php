<?php

namespace App\Http\Requests\CP;

use Illuminate\Foundation\Http\FormRequest;

class SizeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|array',
            'name.*' => 'required|string',
            'price' => 'required|numeric|min:0',
            'restaurant_id' => 'required|exists:restaurants,id',
            'active' => 'boolean',
        ];

        // Add conditional validation based on context
        if ($this->route('_model')) {
            // Edit mode specific rules
            // No additional unique constraints needed for this model
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => t('Name is required'),
            'name.array' => t('Name must be a valid array format'),
            'name.*.required' => t('Name is required'),
            'name.*.string' => t('Name must be a string'),
            'price.required' => t('Price is required'),
            'price.numeric' => t('Price must be a number'),
            'price.min' => t('Price must be at least 0'),
            'restaurant_id.required' => t('Restaurant is required'),
            'restaurant_id.exists' => t('Selected restaurant is invalid'),
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
