<?php

namespace App\Modules\Facturacion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCfdiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Asumimos que cualquier usuario autenticado del tenant puede crear una factura.
        // Se puede añadir lógica más específica si es necesario.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'serie_folio_id' => 'required|exists:facturacion_series_folios,id',
            'forma_pago' => 'required|string|max:2',
            'metodo_pago' => 'required|string|max:3',
            'uso_cfdi' => 'required|string|max:4',
            'conceptos' => 'required|array|min:1',
            'conceptos.*.clave_prod_serv' => 'required|string|max:8',
            'conceptos.*.cantidad' => 'required|numeric|min:0.01',
            'conceptos.*.clave_unidad' => 'required|string|max:3',
            'conceptos.*.descripcion' => 'required|string|max:255',
            'conceptos.*.valor_unitario' => 'required|numeric|min:0.01',
            'related_uuid' => 'nullable|uuid',
            'relation_type' => 'nullable|string|size:2',
        ];
    }
}