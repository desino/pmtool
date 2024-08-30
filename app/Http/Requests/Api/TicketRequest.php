<?php

namespace App\Http\Requests\Api;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TicketRequest extends FormRequest
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
            'functionality_id' => 'nullable|exists:functionalities,id',
            'initial_estimation_development_time' => 'required|numeric|min:0',
            'initiative_id' => 'nullable',
            'type' => 'required',
            'auto_wait_for_client_approval' => 'nullable'
        ];
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);
        $data['functionality_id'] = $data['functionality_id']['id'] ?? null;
        return $data;
    }
}
