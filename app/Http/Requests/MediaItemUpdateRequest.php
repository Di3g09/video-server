<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaItemUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'file'  => [
                'nullable',
                'file',
                'mimetypes:video/mp4',
                'max:5242880',
            ],
            'notes' => ['nullable', 'string'],
            'active'=> ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'title.max'      => 'El título no puede superar los 150 caracteres.',

            'file.file'      => 'El archivo seleccionado no es válido.',
            'file.mimetypes' => 'Solo se permiten videos en formato MP4.',
            'file.max'       => 'El tamaño máximo permitido es de 200 MB.',

            'notes.string'   => 'Las notas deben ser texto.',
        ];
    }
}
