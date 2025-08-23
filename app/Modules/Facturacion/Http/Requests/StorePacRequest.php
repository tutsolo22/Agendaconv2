<?php

namespace App\Modules\Facturacion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePacRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $pacId = $this->route('pac') ? $this->route('pac')->id : null;

        return [
            'nombre' => ['required', 'string', 'max:255'],
            'driver' => [
                'required',
                'string',
                Rule::in(['sw_sapiens', 'edicom']),
                Rule::unique('facturacion_pacs', 'driver')->where('tenant_id', tenant('id'))->ignore($pacId)
            ],
            'rfc' => [
                'required',
                'string',
                'max:13',
                Rule::unique('facturacion_pacs', 'rfc')->where('tenant_id', tenant('id'))->ignore($pacId)
            ],
            'url_produccion' => ['required', 'url'],
            'url_pruebas' => ['nullable', 'url'],
            'is_active' => ['nullable', 'boolean'],

            // --- Credenciales Condicionales ---
            'credentials' => ['required', 'array'],
            'credentials.token' => ['required_if:driver,sw_sapiens', 'nullable', 'string', 'max:1000'],
            'credentials.user' => ['required_if:driver,edicom', 'nullable', 'string', 'max:255'],
            'credentials.password' => [$pacId ? 'nullable' : 'required_if:driver,edicom', 'string', 'max:255'],
        ];
    }
}