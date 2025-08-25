<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\ViewServiceProvider::class,

    // Providers de Módulos
    App\Modules\Facturacion\Services\FacturacionServiceProvider::class,
    App\Modules\Salud\SaludServiceProvider::class,
];
