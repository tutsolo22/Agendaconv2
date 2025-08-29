<?php

namespace App\Modules\Facturacion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartaPorteRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'facturacion_cfdi_id' => ['required', 'exists:facturacion_cfdis,id'],
            'version' => ['required', 'string', 'max:5'],
            'transp_internac' => ['required', 'string', 'max:2'],
            'id_ccp' => ['required', 'string', 'max:50'],
            // Add more rules as per SAT guide
            // Ubicaciones
            'origen.rfc' => ['required', 'string', 'max:13'],
            'origen.nombre' => ['required', 'string', 'max:255'],
            'origen.fecha_hora_salida' => ['required', 'date'],
            'origen.calle' => ['required', 'string', 'max:255'],
            'origen.numero_exterior' => ['required', 'string', 'max:50'],
            'origen.numero_interior' => ['nullable', 'string', 'max:50'],
            'origen.colonia' => ['required', 'string', 'max:255'],
            'origen.localidad' => ['nullable', 'string', 'max:255'],
            'origen.municipio' => ['required', 'string', 'max:255'],
            'origen.estado' => ['required', 'string', 'max:255'],
            'origen.pais' => ['required', 'string', 'max:3'],
            'origen.codigo_postal' => ['required', 'string', 'max:5'],

            'destino.rfc' => ['required', 'string', 'max:13'],
            'destino.nombre' => ['required', 'string', 'max:255'],
            'destino.fecha_hora_llegada' => ['required', 'date'],
            'destino.calle' => ['required', 'string', 'max:255'],
            'destino.numero_exterior' => ['required', 'string', 'max:50'],
            'destino.numero_interior' => ['nullable', 'string', 'max:50'],
            'destino.colonia' => ['required', 'string', 'max:255'],
            'destino.localidad' => ['nullable', 'string', 'max:255'],
            'destino.municipio' => ['required', 'string', 'max:255'],
            'destino.estado' => ['required', 'string', 'max:255'],
            'destino.pais' => ['required', 'string', 'max:3'],
            'destino.codigo_postal' => ['required', 'string', 'max:5'],

            // Mercancias
            'mercancias' => ['required', 'array'],
            'mercancias.*.bienes_transp' => ['required', 'string', 'max:50'],
            'mercancias.*.descripcion' => ['required', 'string', 'max:255'],
            'mercancias.*.cantidad' => ['required', 'numeric', 'min:0.01'],
            'mercancias.*.clave_unidad' => ['required', 'string', 'max:50'],
            'mercancias.*.peso_en_kg' => ['required', 'numeric', 'min:0.001'],

            // Autotransporte
            'autotransporte.perm_sct' => ['required', 'string', 'max:50'],
            'autotransporte.num_permiso_sct' => ['required', 'string', 'max:50'],
            'autotransporte.nombre_aseg' => ['required', 'string', 'max:255'],
            'autotransporte.num_poliza_seguro' => ['required', 'string', 'max:50'],
            'autotransporte.config_vehicular' => ['required', 'string', 'max:50'],
            'autotransporte.placa_vm' => ['required', 'string', 'max:20'],
            'autotransporte.anio_modelo_vm' => ['required', 'integer', 'min:1900', 'max:2100'],

            // Figura Transporte
            'figura_transporte.tipo_figura' => ['required', 'string', 'max:50'],
            'figura_transporte.rfc_figura' => ['required', 'string', 'max:13'],
            'figura_transporte.nombre_figura' => ['required', 'string', 'max:255'],
        ];
    }
}
