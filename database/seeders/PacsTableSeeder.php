<?php

namespace Database\Seeders;

use App\Modules\Facturacion\Models\Configuracion\Pac;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PacsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usamos updateOrCreate para poder ejecutar el seeder múltiples veces sin duplicar datos.
        // Busca por el campo 'driver' que es nuestro identificador único para el servicio.

        Pac::updateOrCreate(
            ['driver' => 'sw_sapiens'],
            [
                'nombre' => 'SW Sapiens',
                'rfc' => 'SFE0807172W8',
                'url_produccion' => 'http://services.sw.com.mx',
                'url_pruebas' => 'http://services.test.sw.com.mx',
                'credentials' => [
                    'token' => 'TU_TOKEN_DE_SW_SAPIENS_AQUI'
                ],
                'is_active' => true,
            ]
        );

                Pac::updateOrCreate(
            ['driver' => 'edicom'],
            [
                'nombre' => 'EDICOM',
                'rfc' => 'EPE0511231S3',
                'url_produccion' => 'https://cfdiws.edicom.mx/TimbreCFDI/service.php',
                'url_pruebas' => 'https://cfdiws-pruebas.edicom.mx/TimbreCFDI/service.php',
                'credentials' => [
                    'user' => 'TU_USUARIO_DE_EDICOM',
                    'password' => 'TU_PASSWORD_DE_EDICOM'
                ],
                'is_active' => true,
            ]
        );

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
