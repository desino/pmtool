<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TimeBookingForUnBillableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'initiative_id' => 'required',
            'project_id' => 'required',
            'hours' => [
                'required',
                'numeric',
                'regex:/^(0\.[1-9][0-9]?|[1-9][0-9]{0,5}(\.[0-9]{1,2})?)$/'
            ],
            'comments' => 'nullable|string|max:500',
            'booked_date' => 'required|date',
            'user_id' => 'nullable|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'hours.regex' => __('validation.time_booking.unbillable.hours.regex'),
        ];
    }
}
