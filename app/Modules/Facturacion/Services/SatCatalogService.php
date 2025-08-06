<?php

namespace App\Modules\Facturacion\Services;

use App\Models\Sat\FormaPago;
use App\Models\Sat\MetodoPago;
use App\Models\Sat\Mes;
use App\Models\Sat\RegimenFiscal;
use App\Models\Sat\Periodicidad;
use App\Models\Sat\UsoCfdi;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class SatCatalogService
{
    /**
     * Obtiene el catálogo de Usos de CFDI desde la base de datos.
     * Usa caché para evitar consultas repetidas.
     */
    public function getUsosCfdi(): Collection
    {
        return Cache::rememberForever('sat.usos_cfdi', function () {
            return UsoCfdi::where('vigencia_fin', '0') // '0' indica que está vigente según el poblador
                ->pluck('texto', 'id');
        });
    }

    /**
     * Obtiene el catálogo de Formas de Pago desde la base de datos.
     */
    public function getFormasPago(): Collection
    {
        return Cache::rememberForever('sat.formas_pago', function () {
            return FormaPago::where('vigencia_fin', '0')
                ->pluck('texto', 'id');
        });
    }

    /**
     * Obtiene el catálogo de Métodos de Pago desde la base de datos.
     */
    public function getMetodosPago(): Collection
    {
        return Cache::rememberForever('sat.metodos_pago', function () {
            return MetodoPago::where('vigencia_fin', '0')
                ->pluck('texto', 'id');
        });
    }

    /**
     * Obtiene el catálogo de Periodicidades desde la base de datos.
     */
    public function getPeriodicidades(): Collection
    {
        return Cache::rememberForever('sat.periodicidades', function () {
            return Periodicidad::where('vigencia_fin', '0')
                ->pluck('texto', 'id');
        });
    }

    /**
     * Obtiene el catálogo de Meses desde la base de datos.
     */
    public function getMeses(): Collection
    {
        return Cache::rememberForever('sat.meses', function () {
            return Mes::orderBy('id')->pluck('texto', 'id');
        });
    }

    /**
     * Obtiene el catálogo de Regímenes Fiscales desde la base de datos.
     */
    public function getRegimenesFiscales(): Collection
    {
        return Cache::rememberForever('sat.regimenes_fiscales', function () {
            // Filtramos para personas físicas y morales, excluyendo los que ya no están vigentes
            return RegimenFiscal::where('vigencia_fin', '0')
                ->orderBy('id')
                ->pluck('texto', 'id');
        });
    }
}