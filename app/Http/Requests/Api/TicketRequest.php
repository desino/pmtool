<?php

namespace App\Http\Requests\Api;

use App\Models\Ticket;
use App\Models\TicketAction;
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
            'initial_estimation_development_time' => 'required|numeric|min:0|between:1,99999.99',
            'initiative_id' => 'nullable',
            'type' => 'required',
            'auto_wait_for_client_approval' => 'nullable',
            'project_id' => 'nullable',
            'ticket_actions.*.user_id' => 'required|exists:users,id',
            'ticket_actions.*.action' => 'required',
            'ticket_actions.*.status' => 'nullable',
            'ticket_actions.*.is_checked' => 'nullable',
            'ticket_actions.*.is_disabled' => 'nullable',
        ];
    }

    public function messages(): array
    {
        $messages = [
            'ticket_actions.*.user_id.required' => __('validation.ticket_actions.user_id.required'),
            'ticket_actions.*.exists.required' => __('validation.ticket_actions.user_id.exists'),
        ];
        return $messages;
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);
        $data['functionality_id'] = $data['functionality_id']['id'] ?? null;
        $data['ticket_actions'] = array_column($data['ticket_actions'], null, 'action');
        $data['ticket_actions'] = array_filter($data['ticket_actions'], function ($action) {
            return $action['is_checked'];
        });
        return $data;
    }
}
