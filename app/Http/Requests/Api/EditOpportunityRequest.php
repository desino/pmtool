<?php

namespace App\Http\Requests\Api;

use App\Models\Initiative;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EditOpportunityRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('initiatives')->ignore($this->id)->where(function ($query) {
                    return $query->where('client_id', $this->client_id);
                }),
            ],
            'ballpark_development_hours' => [
                'required',
                'numeric',
                'regex:/^(0(\.[0-9]{1,2})?|[1-9][0-9]{0,5}(\.[0-9]{1,2})?)$/'
            ],
            'share_point_url' => 'nullable|url',
            'functional_owner_id' => 'nullable|exists:users,id',
            'quality_owner_id' => 'nullable|exists:users,id',
            'technical_owner_id' => 'nullable|exists:users,id',
            'environments.*.type' => 'nullable',
            'environments.*.name' => 'nullable|string',
            'environments.*.url' => 'nullable|url',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('validation.initiative.name'),
            'ballpark_development_hours.regex' => __('validation.initiative.ballpark_development_hours.regex'),
            'environments.*.name.required' => __('validation.environments.name.required'),
            'environments.*.name.string' => __('validation.environments.name.string'),
            'environments.*.url.required' => __('validation.environments.url.required'),
            'environments.*.url.string' => __('validation.environments.url.string'),
            'environments.*.url.url' => __('validation.environments.url.url'),
        ];
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);

        $data['status'] = $data['is_sold'] ? Initiative::getStatusOngoing() : Initiative::getStatusOpportunity();
        return $data;
    }
}
