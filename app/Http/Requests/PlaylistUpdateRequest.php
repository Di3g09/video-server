<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaylistUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'is_default'  => ['sometimes', 'boolean'],
            'active'      => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la playlist es obligatorio.',
            'name.max'      => 'El nombre no puede superar los 150 caracteres.',
        ];
    }
}
