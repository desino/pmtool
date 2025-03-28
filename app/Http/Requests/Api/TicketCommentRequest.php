<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TicketCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() ? true : false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $appendRules = [
            'comment' => 'required|string',
        ];
        if (isset($this->comment_type)) {
            $appendRules = [
                'comment' => 'nullable|string',
            ];
        }
        return [
            // 'comment' => 'required|string',
            'tagged_users' => 'array',
            'tagged_users.*' => 'exists:users,id',
        ] + $appendRules;
    }
}
