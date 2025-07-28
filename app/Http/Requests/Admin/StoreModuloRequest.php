<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuloRequest extends FormRequest
{
    public function authorize(): bool
    {
        // La autorización ya está manejada por el middleware 'role:Super-Admin' en la ruta.
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', 'unique:modulos,nombre'],
            // alpha_dash permite letras, números, guiones y guiones bajos.
            'slug' => ['required', 'string', 'max:255', 'unique:modulos,slug', 'alpha_dash'],
            'descripcion' => ['nullable', 'string'],
            'icono' => ['nullable', 'string', 'max:100'],
            // 'sometimes' asegura que solo se valide si está presente en la petición.
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}