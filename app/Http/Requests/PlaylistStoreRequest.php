<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaylistStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // luego podemos filtrar por roles si quieres
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->boolean('is_default')) {
                $validator->errors()->add(
                    'is_default',
                    'No puedes marcar la playlist como predeterminada al crearla. ' .
                        'Primero guárdala y luego agrega al menos un video activo.'
                );
            }
        });
    }
}
