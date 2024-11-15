<?php

namespace App\Http\Requests\Api;

use App\Models\Initiative;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InitiativeRequest extends FormRequest
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
            'client_id' => 'required|exists:clients,id',
            'name' => 'required',
            'ballpark_development_hours' => [
                'required',
                'numeric',
                'regex:/^(0\.[1-9][0-9]?|[1-9][0-9]{0,5}(\.[0-9]{1,2})?)$/'
            ],
            'is_sold' => 'nullable',
            'status' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'ballpark_development_hours.regex' => __('validation.initiative.ballpark_development_hours.regex'),
            'client_id.required' => __('validation.initiative.client_id.required'),
            'client_id.exists' => __('validation.initiative.client_id.exists'),
            'name.required' => __('validation.initiative.name'),
        ];
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);

        $data['status'] = $data['is_sold'] ? Initiative::getStatusOngoing() : Initiative::getStatusOpportunity();
        return $data;
    }
}
