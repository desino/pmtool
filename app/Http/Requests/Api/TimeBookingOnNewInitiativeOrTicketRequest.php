<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TimeBookingOnNewInitiativeOrTicketRequest extends FormRequest
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
            'initiative_id' => 'required',
            'ticket_id' => 'nullable',
            'hours' => 'required|numeric|between:1,99999.99',
            'comments' => 'nullable|string|max:500',
            'booked_date' => 'required|date',
        ];
    }
}
