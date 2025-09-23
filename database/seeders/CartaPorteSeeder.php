<?php

namespace Database\Seeders;

use App\Modules\Facturacion\Models\Cfdi;
use App\Modules\Facturacion\Models\CartaPorte\CartaPorte;
use App\Modules\Facturacion\Models\CartaPorte\Ubicacion;
use App\Modules\Facturacion\Models\CartaPorte\Mercancias;
use App\Modules\Facturacion\Models\CartaPorte\Mercancia;
use App\Modules\Facturacion\Models\CartaPorte\Autotransporte;
use App\Modules\Facturacion\Models\CartaPorte\FiguraTransporte;
use Illuminate\Database\Seeder;

class CartaPorteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $cfdi = Cfdi::where('serie', 'T')->where('folio', 1)->first();

        if ($cfdi) {
            $cartaPorte = CartaPorte::create([
                'facturacion_cfdi_id' => $cfdi->id,
                'version' => '3.0',
                'transp_internac' => 'No',
                'id_ccp' => 'CCC123456789',
            ]);

            Ubicacion::create([
                'carta_porte_id' => $cartaPorte->id,
                'tipo_ubicacion' => 'Origen',
                'rfc_remitente_destinatario' => 'XAXX010101000',
                'nombre_remitente_destinatario' => 'Cliente de Prueba',
                'fecha_hora_salida_llegada' => now(),
                'calle' => 'Calle Falsa',
                'numero_exterior' => '123',
                'colonia' => 'Centro',
                'municipio' => 'Cuauhtemoc',
                'estado' => 'Ciudad de Mexico',
                'pais' => 'MEX',
                'codigo_postal' => '06000',
            ]);

            $mercanciasContainer = Mercancias::create([
                'carta_porte_id' => $cartaPorte->id,
                'peso_bruto_total' => 100.00,
                'unidad_peso' => 'KGM',
                'peso_neto_total' => 95.00,
                'num_total_mercancias' => 1,
            ]);

            $mercanciasContainer->mercancia()->create([
                'bienes_transp' => '01010101',
                'descripcion' => 'Producto de prueba',
                'cantidad' => 1,
                'clave_unidad' => 'H87',
                'peso_en_kg' => 95.00,
            ]);

            Autotransporte::create([
                'carta_porte_id' => $cartaPorte->id,
                'perm_sct' => 'TPAF01',
                'num_permiso_sct' => 'PERMISO-123',
                'nombre_aseg' => 'Aseguradora Patito',
                'num_poliza_seguro' => 'POLIZA-456',
                'config_vehicular' => 'C2',
                'placa_vm' => 'ABC-123',
                'anio_modelo_vm' => 2020,
            ]);

            FiguraTransporte::create([
                'carta_porte_id' => $cartaPorte->id,
                'tipo_figura' => '01',
                'rfc_figura' => 'XAXX010101000',
                'nombre_figura' => 'Operador de Prueba',
            ]);
        }
    }
}