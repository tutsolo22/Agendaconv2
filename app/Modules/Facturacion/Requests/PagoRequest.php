<?php

namespace App\Modules\Facturacion\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['required', 'exists:clientes,id'],
            'fecha_pago' => ['required', 'date'],
            'forma_pago_p' => ['required', 'string', 'max:2'],
            'moneda_p' => ['required', 'string', 'size:3'],
            'monto' => ['required', 'numeric', 'min:0.01'],

            'doctos_relacionados' => ['required', 'array', 'min:1'],
            'doctos_relacionados.*.id_documento' => ['required', 'string', 'uuid'],
            'doctos_relacionados.*.serie' => ['nullable', 'string'],
            'doctos_relacionados.*.folio' => ['nullable', 'string'],
            'doctos_relacionados.*.moneda_dr' => ['required', 'string', 'size:3'],
            'doctos_relacionados.*.num_parcialidad' => ['required', 'integer', 'min:1'],
            'doctos_relacionados.*.imp_saldo_ant' => ['required', 'numeric'],
            'doctos_relacionados.*.imp_pagado' => ['required', 'numeric', 'min:0.01'],
            'doctos_relacionados.*.imp_saldo_insoluto' => ['required', 'numeric'],
        ];
    }
}