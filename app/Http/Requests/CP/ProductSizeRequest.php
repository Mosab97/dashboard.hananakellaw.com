<?php

namespace App\Http\Requests\CP;

use Illuminate\Foundation\Http\FormRequest;

class ProductSizeRequest extends FormRequest
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
            'size_id' => 'required|exists:sizes,id',
            'price' => 'required|numeric|min:0',
            'order' => 'required|integer|min:0',
            'active' => 'boolean',
            'product_id' => 'required|exists:products,id',
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
            'size_id.required' => t('Size is required'),
            'size_id.exists' => t('Size does not exist'),
            'price.required' => t('Price is required'),
            'price.numeric' => t('Price must be a number'),
            'price.min' => t('Price must be greater than 0'),
            'order.required' => t('Order is required'),
            'order.integer' => t('Order must be an integer'),
            'order.min' => t('Order must be greater than 0'),
            'active.boolean' => t('Active status must be true or false'),
            'product_id.required' => t('Product is required'),
            'product_id.exists' => t('Product does not exist'),

        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'active' => $this->has('active'),
            'product_id' => $this->route('product'),
        ]);
    }
}
