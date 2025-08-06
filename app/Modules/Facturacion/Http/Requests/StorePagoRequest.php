<?php

namespace App\Modules\Facturacion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePagoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // La autorización se maneja a nivel de controlador o middleware de tenant
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
            'serie_folio_id' => 'required|exists:facturacion_series_folios,id',
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_pago' => 'required|date_format:Y-m-d\TH:i',
            'forma_pago' => 'required|string|in:01,02,03,04,05,06,08,12,13,14,15,17,23,24,25,26,27,28,29,30,99',
            'moneda' => 'required|string|in:MXN,USD',
            'monto' => 'required|numeric|min:0.01',
            'doctosRelacionados' => 'required|array|min:1',
            'doctosRelacionados.*.id_documento' => 'required|string|uuid',
            'doctosRelacionados.*.serie' => 'nullable|string|max:25',
            'doctosRelacionados.*.folio' => 'required|string|max:40',
            'doctosRelacionados.*.moneda_dr' => 'required|string|in:MXN,USD',
            'doctosRelacionados.*.num_parcialidad' => 'required|integer|min:1',
            'doctosRelacionados.*.imp_saldo_ant' => 'required|numeric|min:0',
            'doctosRelacionados.*.imp_pagado' => 'required|numeric|min:0.01',
        ];
    }
}
