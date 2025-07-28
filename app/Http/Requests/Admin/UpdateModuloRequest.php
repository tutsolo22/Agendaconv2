<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateModuloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtenemos el ID del módulo desde la ruta para ignorarlo en la regla 'unique'.
        $moduloId = $this->route('modulo')->id;

        return [
            'nombre' => ['required', 'string', 'max:255', Rule::unique('modulos')->ignore($moduloId)],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('modulos')->ignore($moduloId)],
            'descripcion' => ['nullable', 'string'],
            'icono' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}