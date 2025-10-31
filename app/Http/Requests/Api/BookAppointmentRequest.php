<?php

namespace App\Http\Requests\Api;

use App\Enums\BookType;
use Illuminate\Foundation\Http\FormRequest;

class BookAppointmentRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'appointment_type_id' => ['required', 'exists:appointment_types,id'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'book_type' => ['required', 'in:' . implode(',', BookType::toArray())],
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
            'phone.required' => t('Phone is required'),
            'city.required' => t('City is required'),
            'appointment_type_id.required' => t('Appointment type is required'),
            'appointment_type_id.exists' => t('Appointment type does not exist'),
            'date.required' => t('Date is required'),
            'date.date' => t('Date is not a valid date'),
            'time.required' => t('Time is required'),
            'time.date_format' => t('Time is not a valid time'),
            'book_type.required' => t('Book type is required'),
            'book_type.in' => t('Book type is not valid'),
        ];
    }
}
