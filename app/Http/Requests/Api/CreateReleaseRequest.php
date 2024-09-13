<?php

namespace App\Http\Requests\Api;

use App\Services\InitiativeService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateReleaseRequest extends FormRequest
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
        $initiative = InitiativeService::getInitiative(request(), $this->initiative_id);
        if ($initiative->unprocessedRelease) {
            return [
                // 'release_id' => 'required',
                'tags' => 'nullable',
            ];
        }
        return [
            'release_id' => 'nullable',
            'tags' => 'required_without:release_id',
        ];
    }
}
