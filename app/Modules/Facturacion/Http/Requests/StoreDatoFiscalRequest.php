<?php

namespace App\Modules\Facturacion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDatoFiscalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rfc' => ['required', 'string', 'regex:/^[A-Z&Ñ]{3,4}\d{6}[A-Z\d]{3}$/i', 'max:13'],
            'razon_social' => 'required|string|max:255',
            'regimen_fiscal_clave' => 'required|string|max:3',
            'cp_fiscal' => 'required|string|digits:5',
            'pac_id' => 'nullable|exists:facturacion_pacs,id',
            'en_pruebas' => 'sometimes|boolean',
            'password_csd' => 'nullable|string|max:255',
            'archivo_cer' => 'nullable|file|mimes:cer',
            'archivo_key' => 'nullable|file|mimes:key',
        ];
    }
}

