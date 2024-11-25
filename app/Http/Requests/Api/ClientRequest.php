<?php

namespace App\Http\Requests\Api;

use App\Models\Initiative;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ClientRequest extends FormRequest
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
            'name' => 'required|string|unique:clients|max:255',
            'initiative_name' => 'required|string|unique:initiatives,name|max:255',
            'ballpark_development_hours' => [
                'required',
                'numeric',
                'regex:/^(0(\.[0-9]{1,2})?|[1-9][0-9]{0,5}(\.[0-9]{1,2})?)$/'
            ],
            'is_sold' => 'nullable',
            'status' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'ballpark_development_hours.regex' => __('validation.client.ballpark_development_hours.regex'),
        ];
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);

        $data['status'] = $data['is_sold'] ? Initiative::getStatusOngoing() : Initiative::getStatusOpportunity();
        return $data;
    }
}
