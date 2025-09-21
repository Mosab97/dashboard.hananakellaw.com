<?php

namespace App\Http\Requests\CP;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only allow users to update their own profile
        return auth()->id() === auth()->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = auth()->user();

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'mobile' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
            'password_confirmation' => 'nullable|string|min:8',
            'avatar_remove' => 'nullable|in:0,1',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'full name',
            'email' => 'email address',
            'mobile' => 'mobile number',
            'avatar' => 'profile picture',
            'current_password' => 'current password',
            'password' => 'new password',
            'password_confirmation' => 'confirm password',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => t('Your full name is required.'),
            'email.required' => t('Your email address is required.'),
            'email.email' => t('Please enter a valid email address.'),
            'email.unique' => t('This email address is already in use by another account.'),
            'mobile.max' => t('Mobile number should not exceed 20 characters.'),
            'avatar.image' => t('The profile picture must be an image file.'),
            'avatar.mimes' => t('The profile picture must be a JPEG, PNG, JPG, or GIF file.'),
            'avatar.max' => t('The profile picture may not be larger than 2MB.'),
            'current_password.required_with' => t('Your current password is required to set a new password.'),
            'password.min' => t('Your new password must be at least 8 characters.'),
            'password.confirmed' => t('Password confirmation does not match the new password.'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Handle avatar_remove field if present
        if ($this->has('avatar_remove') && $this->input('avatar_remove') == '1') {
            $this->merge(['avatar' => null]);
        }
    }
}
