<?php

namespace App\Http\Requests\Api;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TitcketRequest extends FormRequest
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
            'name' => 'required|string',
            'functionality_id' => 'required|exists:functionalities,id',
            'initial_estimation_development_time' => 'required|numeric',
            'initiative_id' => 'nullable',
            'type' => 'nullable',
        ];
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);
        $data['functionality_id'] = $data['functionality_id']['id'] ?? "";
        $data['type'] = Ticket::getTypeFeatureImprovement();
        return $data;
    }
}