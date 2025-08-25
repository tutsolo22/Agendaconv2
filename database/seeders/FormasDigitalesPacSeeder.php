<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Facturacion\Models\Configuracion\Pac;

class FormasDigitalesPacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Pac::updateOrCreate(
            ['driver' => 'formas_digitales'],
            [
                'nombre' => 'Formas Digitales',
                'rfc' => 'FCG840618N51', // RFC del PAC, obtenido de la documentación
                'url_produccion' => 'https://v33.facturacfdi.mx/WSTimbradoCFDIService?wsdl',
                'url_pruebas' => 'https://dev33.facturacfdi.mx/WSTimbradoCFDIService?wsdl',
                'credentials' => [
                    'user' => '',
                    'password' => '',
                ],
                'is_active' => false, // No lo activamos por defecto para no interferir con configuraciones existentes
            ]
        );
    }
}