<?php

namespace App\Http\Requests\Api;

use App\Models\Initiative;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
            'name' => 'required',
            'ballpark_development_hours' => 'required|numeric|between:1,99999.99',
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
