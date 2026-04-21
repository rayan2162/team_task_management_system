<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class JoinProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string'],
        ];
    }
}
