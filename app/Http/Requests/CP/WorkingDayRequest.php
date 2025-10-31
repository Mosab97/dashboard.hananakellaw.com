<?php

namespace App\Http\Requests\CP;

use Illuminate\Foundation\Http\FormRequest;

class WorkingDayRequest extends FormRequest
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
        return [
            'day' => ['required', 'string', 'in:'.implode(',', \App\Enums\Day::toArray())],
            'hours' => ['required', 'array', 'min:1'],
            'hours.*.start_time' => ['required', 'date_format:H:i'],
            'hours.*.end_time' => ['required', 'date_format:H:i'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $hours = $this->input('hours', []);
            
            foreach ($hours as $index => $hour) {
                if (!empty($hour['start_time']) && !empty($hour['end_time'])) {
                    if (strtotime($hour['end_time']) <= strtotime($hour['start_time'])) {
                        $validator->errors()->add("hours.{$index}.end_time", t('End time must be after start time'));
                    }
                }
            }
        });
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
            'hours.required' => t('At least one working hour is required'),
            'hours.min' => t('At least one working hour is required'),
            'hours.*.start_time.required' => t('Start time is required'),
            'hours.*.start_time.date_format' => t('Start time must be a valid time'),
            'hours.*.end_time.required' => t('End time is required'),
            'hours.*.end_time.date_format' => t('End time must be a valid time'),
            'hours.*.end_time.after' => t('End time must be after start time'),
        ];
    }
}

