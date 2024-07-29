<?php

namespace App\Http\Requests\Api;

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
            'name' => 'required|string',
            'initiative_name' => 'required|string',
            'ballpark_development_hours' => 'required|numeric',
            'is_sold' => 'nullable',
            'status' => 'nullable',
        ];
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);

        $data['status'] = $data['is_sold'] ? 'Ongoing' : 'Opportunity';
        return $data;
    }
}
