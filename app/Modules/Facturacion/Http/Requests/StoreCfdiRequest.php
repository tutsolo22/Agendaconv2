<?php

namespace App\Modules\Facturacion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCfdiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // --- SECCIÓN DATOS GENERALES ---
            'cliente_id' => 'required|exists:clientes,id',
            'serie' => ['required', 'string', Rule::exists('facturacion_series_folios', 'serie')->where('activo', true)], // Verifica que la serie exista y esté activa
            'forma_pago' => ['required', 'string', Rule::exists('sat_cfdi_40_formas_pago', 'id')],
            'metodo_pago' => ['required', 'string', Rule::exists('sat_cfdi_40_metodos_pago', 'id')],
            'uso_cfdi' => ['required', 'string', Rule::exists('sat_cfdi_40_usos_cfdi', 'id')],

            // --- SECCIÓN CONCEPTOS ---
            'conceptos' => 'required|array|min:1',
            'conceptos.*.cantidad' => 'required|numeric|gt:0', // gt:0 (greater than 0) es más legible y estricto.
            'conceptos.*.producto' => 'required|string|max:255',
            'conceptos.*.descripcion' => 'required|string|max:1000',
            'conceptos.*.clave_prod_serv' => ['required', 'string', Rule::exists('sat_cfdi_40_productos_servicios', 'id')],
            'conceptos.*.valor_unitario' => 'required|numeric|min:0',
            // El campo 'importe' no se valida porque es de solo lectura y se calcula en el backend.

            // --- CAMPO DE ACCIÓN ---
            'action' => ['required', 'string', Rule::in(['guardar', 'timbrar'])],
        ];
    }
}