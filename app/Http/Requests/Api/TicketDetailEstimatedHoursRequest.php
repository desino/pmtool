<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TicketDetailEstimatedHoursRequest extends FormRequest
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
            'dev_estimation_time' => [
                'required',
                'numeric',
                'regex:/^(0(\.[0-9]{1,2})?|[1-9][0-9]{0,5}(\.[0-9]{1,2})?)$/'
            ],
        ];
    }

    public function messages()
    {
        return [
            'dev_estimation_time.regex' => __('validation.dev_estimation_time.regex'),
        ];
    }
}
