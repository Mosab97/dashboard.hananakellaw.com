<?php

namespace App\Http\Requests\CP;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRateRequest extends FormRequest
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
            'name' => ['required', 'array'],
            'name.he' => ['nullable', 'string', 'max:255'],
            'name.ar' => ['required', 'string', 'max:255'],
            'description' => ['required', 'array'],
            'description.he' => ['nullable', 'string', 'max:255'],
            'description.ar' => ['required', 'string', 'max:255'],
            'rate' => ['required', 'integer', 'min:1', 'max:5'],
            'active' => ['boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
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
            'name.required' => t('Name is required'),
            'name.array' => t('Name must be provided in multiple languages'),
            'name.ar.required' => t('Arabic name is required'),
            'name.he.string' => t('English name must be a string'),
            'name.ar.string' => t('Arabic name must be a string'),
            'name.*.max' => t('Name must not exceed 255 characters'),
            'description.required' => t('Description is required'),
            'description.array' => t('Description must be provided in multiple languages'),
            'description.ar.required' => t('Arabic description is required'),
            'description.en.string' => t('English description must be a string'),
            'description.ar.string' => t('Arabic description must be a string'),
            'description.*.max' => t('Description must not exceed 255 characters'),
            'rate.required' => t('Rate is required'),
            'rate.integer' => t('Rate must be an integer'),
            'rate.min' => t('Rate must be at least 1'),
            'rate.max' => t('Rate must be at most 5'),
            'active.boolean' => t('Active status must be true or false'),
            'order.integer' => t('Order must be a number'),
            'order.min' => t('Order must be at least 0'),

            'name.required' => t('Name is required'),
            'name.array' => t('Name must be provided in multiple languages'),
            'name.ar.required' => t('Arabic name is required'),
            'name.he.string' => t('English name must be a string'),
            'name.ar.string' => t('Arabic name must be a string'),
            'name.*.max' => t('Name must not exceed 255 characters'),
            'description.required' => t('Description is required'),
            'description.array' => t('Description must be provided in multiple languages'),
            'description.ar.required' => t('Arabic description is required'),
            'description.en.string' => t('English description must be a string'),
            'description.ar.string' => t('Arabic description must be a string'),
            'description.*.max' => t('Description must not exceed 255 characters'),
            'rate.required' => t('Rate is required'),
            'rate.integer' => t('Rate must be an integer'),
            'rate.min' => t('Rate must be at least 1'),
            'rate.max' => t('Rate must be at most 5'),
            'active.boolean' => t('Active status must be true or false'),
            'order.integer' => t('Order must be a number'),
            'order.min' => t('Order must be at least 0'),

        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'active' => $this->has('active') ? true : false,
        ]);
    }
}
