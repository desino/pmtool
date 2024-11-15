<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BulkCreateTicketsRequest extends FormRequest
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
            'sections.*.functionality.*.initial_estimation_development_time' => [
                'nullable',
                'numeric',
                'regex:/^(0\.[1-9][0-9]?|[1-9][0-9]{0,5}(\.[0-9]{1,2})?)$/'
            ],
        ];
    }

    public function messages()
    {
        return [
            'sections.*.functionality.*.initial_estimation_development_time.numeric' => __('validation.bulk_create_tickets.initial_estimation_development_time.numeric'),
            'sections.*.functionality.*.initial_estimation_development_time.regex' => __('validation.bulk_create_tickets.initial_estimation_development_time.regex'),
        ];
    }
}
