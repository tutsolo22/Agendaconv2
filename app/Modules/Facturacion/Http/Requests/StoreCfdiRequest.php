<?php

namespace App\Modules\Facturacion\Http\Requests;

use App\Modules\Facturacion\Rules\Rfc;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCfdiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Aquí se puede añadir lógica de permisos si es necesario.
        // Por ahora, permitimos la solicitud si el usuario está autenticado.
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
            // --- SECCIÓN DATOS GENERALES DEL COMPROBANTE ---
            'cliente_id' => ['required', 'exists:clientes,id'],
            'serie' => ['required', 'string', Rule::exists('facturacion_series_folios', 'serie')->where('activo', true)],
            'forma_pago' => ['required', 'string', Rule::exists('sat_cfdi_40_formas_pago', 'id')],
            'metodo_pago' => ['required', 'string', Rule::exists('sat_cfdi_40_metodos_pago', 'id')],
            'uso_cfdi' => ['required', 'string', Rule::exists('sat_cfdi_40_usos_cfdi', 'id')],
            'tipo_comprobante' => ['required', 'string', Rule::in(['I', 'E'])], // Ingreso o Egreso

            // --- SECCIÓN DATOS DEL RECEPTOR (CFDI 4.0) ---
            // La validación debe hacerse sobre los datos que se enviarán al PAC.
            'receptor.rfc' => ['required', 'string', 'uppercase', new Rfc()],
            'receptor.nombre' => ['required', 'string', 'uppercase', 'max:255'], // El nombre/razón social debe ser mayúsculas.
            'receptor.domicilio_fiscal_receptor' => ['required', 'string', 'digits:6', Rule::exists('sat_codigos_postales', 'id')],
            'receptor.regimen_fiscal_receptor' => ['required', 'string', Rule::exists('sat_cfdi_40_regimenes_fiscales', 'id')],

            // --- SECCIÓN CONCEPTOS ---
            'conceptos' => ['required', 'array', 'min:1'],
            'conceptos.*.clave_prod_serv' => ['required', 'string', Rule::exists('sat_cfdi_40_claves_productos_servicios', 'id')],
            'conceptos.*.cantidad' => ['required', 'numeric', 'min:0.000001'],
            'conceptos.*.valor_unitario' => ['required', 'numeric', 'min:0'],
            'conceptos.*.descripcion' => ['required', 'string', 'max:1000'],
            'conceptos.*.clave_unidad' => ['required', 'string', Rule::exists('sat_cfdi_40_claves_unidades', 'id')],
            'conceptos.*.objeto_imp' => ['required', 'string', Rule::exists('sat_cfdi_40_objetos_impuestos', 'id')],
        ];
    }
}