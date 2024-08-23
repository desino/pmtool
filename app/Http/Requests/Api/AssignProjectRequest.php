<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AssignProjectRequest extends FormRequest
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
            'project_id' => [
                function ($attribute, $value, $fail) {
                    if (!empty($value) && !empty($this->input('project_name'))) {
                        $fail(__('validation.task.assign_project.mutual_exclusivity.project_id'));
                    }
                    if (empty($value) && empty($this->input('project_name'))) {
                        $fail(__('validation.task.assign_project.mutual_exclusivity.project_id.required'));
                    }
                },
            ],
            'project_name' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (!empty($value) && !empty($this->input('project_id'))) {
                        $fail(__('validation.task.assign_project.mutual_exclusivity.project_name'));
                    }
                },
            ],
        ];
    }
}
