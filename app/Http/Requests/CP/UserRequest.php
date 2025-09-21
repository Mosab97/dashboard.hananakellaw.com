<?php

namespace App\Http\Requests\CP;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // auth()->user()->can('user_access');
    }

    public function rules(): array
    {
        $isUpdate = ! empty($this->user_id);

        // dd($isUpdate);
        return [
            'name' => ['required', 'array'],
            'name.*' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                $isUpdate
                    ? Rule::unique('users')->ignore($this->user_id)
                    : Rule::unique('users'),
            ],
            'mobile' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            // 'active' => ['boolean'],
            'role_name' => ['nullable', 'exists:roles,name'],
            'custom_permissions' => ['nullable', 'array'],
            'custom_permissions.*' => ['exists:permissions,name'],
            'password' => $isUpdate ? ['nullable', 'min:6'] : ['required', 'min:6'],
        ];
    }

    public function messages(): array
    {
        $messages = [
            'name.*.required' => t('Name is required'),
            'email.required' => t('Email is required'),
            'email.unique' => t('Email already exists'),
            'email.email' => t('Invalid email format'),
            'password.required' => t('Password is required'),
            'password.min' => t('Password must be at least 6 characters'),
            'role_name.exists' => t('Invalid role'),
            'avatar.image' => t('Avatar must be an image'),
            'avatar.mimes' => t('Avatar must be jpeg, png, or jpg'),
            'avatar.max' => t('Avatar must not exceed 2MB'),
        ];

        foreach (config('app.locales') as $locale) {
            $messages["name.$locale.required"] = t('Name in :locale is required', ['locale' => strtoupper($locale)]);
        }

        return $messages;
    }
}
