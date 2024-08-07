<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FunctionalityRequest extends FormRequest
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
            "section_id" => "required",
            "name" => "required|string",
            "description" => "nullable",
            "functionality_id" => "nullable",
        ];
    }

    public function messages(): array{
        return [
            "section_id.required" => __('message.solution_design.functionality.section_id.required'),
        ];
    }
}