<?php

namespace App\Http\Requests\Api\HexaFac;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacturaApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization logic will be handled by the middleware
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'cliente' => 'required|array',
            'cliente.id_externo' => 'nullable|string|max:255',
            'cliente.crear_si_no_existe' => 'required|boolean',
            'cliente.rfc' => 'required|string|max:13',
            'cliente.razon_social' => 'required|string|max:255',
            'cliente.cfdi_uso' => 'required|string|max:255',
            'cliente.regimen_fiscal' => 'required|string|max:255',
            'cliente.domicilio.codigo_postal' => 'required|string|max:5',

            'conceptos' => 'required|array|min:1',
            'conceptos.*.clave_sat' => 'required|string|max:255',
            'conceptos.*.id_externo' => 'nullable|string|max:255',
            'conceptos.*.descripcion' => 'required|string|max:1000',
            'conceptos.*.cantidad' => 'required|numeric|min:0.000001',
            'conceptos.*.valor_unitario' => 'required|numeric|min:0',
            'conceptos.*.clave_unidad' => 'required|string|max:255',
            'conceptos.*.impuestos.iva_tasa' => 'nullable|numeric',

            'opciones' => 'required|array',
            'opciones.tipo_comprobante' => 'required|string|max:1',
            'opciones.moneda' => 'required|string|max:3',
            'opciones.forma_pago' => 'required|string|max:2',
            'opciones.metodo_pago' => 'required|string|max:3',
        ];
    }
}
