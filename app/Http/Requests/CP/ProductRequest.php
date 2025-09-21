<?php

namespace App\Http\Requests\CP;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|array',
            'description' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'active' => 'boolean',
            'order' => 'integer|min:0',
            'restaurant_id' => 'required|exists:restaurants,id',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => t('Name is required'),
            'name.array' => t('Name must be a valid array format'),
            'category_id.required' => t('Category is required'),
            'category_id.exists' => t('Selected category is invalid'),
            'restaurant_id.required' => t('Restaurant is required'),
            'restaurant_id.exists' => t('Selected restaurant is invalid'),
            'price.required' => t('Price is required'),
            'price.numeric' => t('Price must be a number'),
            'price.min' => t('Price must be at least 0'),
            'order.integer' => t('Order must be an integer'),
            'order.min' => t('Order must be at least 0'),
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'active' => $this->has('active') ? true : false,
        ]);
        $this->merge([
            'order' => $this->has('order') ? $this->order : 0,
        ]);
        $this->merge([
            'restaurant_id' => getFirstRestaurant()->id ?? null,
        ]);
    }
}
