<?php

namespace App\Modules\Facturacion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNominaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // O la lógica de autorización que necesites
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // Datos Generales
            'tipo_nomina' => ['required', 'string', 'in:O,E'],
            'fecha_pago' => ['required', 'date'],
            'fecha_inicial_pago' => ['required', 'date'],
            'fecha_final_pago' => ['required', 'date', 'after_or_equal:fecha_inicial_pago'],
            'num_dias_pagados' => ['required', 'numeric', 'min:0'],
            'periodicidad_pago' => ['required', 'string', 'max:2'], // Validar contra catálogo

            // Empleado
            'empleado_id' => ['required', 'exists:nominas_empleados,id'],

            // Percepciones
            'percepciones' => ['required', 'array', 'min:1'],
            'percepciones.*.clave' => ['required', 'string'], // Validar contra catálogo
            'percepciones.*.concepto' => ['required', 'string', 'max:100'],
            'percepciones.*.importe_gravado' => ['required', 'numeric', 'min:0'],
            'percepciones.*.importe_exento' => ['required', 'numeric', 'min:0'],

            // Deducciones
            'deducciones' => ['nullable', 'array'],
            'deducciones.*.clave' => ['required_with:deducciones', 'string'], // Validar contra catálogo
            'deducciones.*.concepto' => ['required_with:deducciones', 'string', 'max:100'],
            'deducciones.*.importe' => ['required_with:deducciones', 'numeric', 'min:0'],

            // Otros Pagos
            'otrospagos' => ['nullable', 'array'],
            'otrospagos.*.clave' => ['required_with:otrospagos', 'string'], // Validar contra catálogo
            'otrospagos.*.concepto' => ['required_with:otrospagos', 'string', 'max:100'],
            'otrospagos.*.importe' => ['required_with:otrospagos', 'numeric', 'min:0'],

            // Incapacidades
            'incapacidades' => ['nullable', 'array'],
            'incapacidades.*.tipo_incapacidad' => ['required_with:incapacidades', 'string'], // Validar contra catálogo
            'incapacidades.*.dias' => ['required_with:incapacidades', 'integer', 'min:1'],
            'incapacidades.*.descuento' => ['required_with:incapacidades', 'numeric', 'min:0'],
        ];
    }
}
