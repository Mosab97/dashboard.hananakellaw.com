<?php

namespace App\Http\Requests\CP;

use Illuminate\Foundation\Http\FormRequest;

class WorkingHourRequest extends FormRequest
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
            'day' => ['required', 'string', 'in:'.implode(',', \App\Enums\Day::toArray())],
            'start_time' => ['required', 'string', 'date_format:H:i'],
            'end_time' => ['required', 'string', 'date_format:H:i'],
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
            'day.required' => t('Day is required'),
            'day.in' => t('Day must be a valid day'),
            'start_time.required' => t('Start time is required'),
            'start_time.date_format' => t('Start time must be in H:i format'),
            'end_time.required' => t('End time is required'),
            'end_time.date_format' => t('End time must be in H:i format'),   
            'day.in' => t('Day must be a valid day'),
        ];
    }
}

