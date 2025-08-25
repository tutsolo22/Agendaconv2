<?php

namespace App\Modules\Facturacion\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SatCatalogService
{
    /**
     * Prefijo para las tablas de catálogos de CFDI 4.0.
     * Se define aquí para facilitar futuras actualizaciones a nuevas versiones (ej. CFDI 5.0).
     */
    private const CFDI_40_PREFIX = 'sat_cfdi_40_';
    private const RET_20_PREFIX = 'sat_ret_20_';

    /**
     * Lee un catálogo del SAT desde la base de datos, lo transforma y lo cachea.
     *
     * @param string $tableName El nombre de la tabla en la base de datos (ej. 'sat_formas_pago')
     * @param string $keyColumn El nombre de la columna que contiene el ID (ej. 'id')
     * @param string $textColumn El nombre de la columna que contiene la descripción (ej. 'descripcion')
     * @param string $cacheKey La clave única para el caché de este catálogo.
     * @param array|null $filters Filtros adicionales para la vigencia.
     * @return \Illuminate\Support\Collection
     */
    private function readCatalog(string $tableName, string $keyColumn, string $textColumn, string $cacheKey, ?array $filters = [])
    {
        // Se eliminó el uso de Cache::tags() porque el driver de caché por defecto ('file') no lo soporta.
        // La caché seguirá funcionando con rememberForever, pero no se podrán limpiar los catálogos por etiqueta.
        // Para limpiar la caché, se debe usar `php artisan cache:clear`.

        return Cache::rememberForever($cacheKey, function () use ($tableName, $keyColumn, $textColumn, $filters) {
            $query = DB::table($tableName);

            // Lógica de vigencia (muy común en catálogos del SAT)
            if (!empty($filters)) {
                $now = now()->toDateString();
                if (isset($filters['start_date_col'])) {
                    $query->where($filters['start_date_col'], '<=', $now);
                }
                if (isset($filters['end_date_col'])) {
                    $query->where(function ($q) use ($filters, $now) {
                        $q->whereNull($filters['end_date_col'])
                          ->orWhere($filters['end_date_col'], '>=', $now);
                    });
                }
            }

            // 1. Obtenemos los datos con sus nombres de columna originales.
            $results = $query->orderBy($keyColumn)->get([$keyColumn, $textColumn]);

            // 2. Mapeamos los resultados para estandarizar el formato de salida.
            // Esto garantiza que la API siempre devuelva {id, texto}.
            return $results->map(fn($item) => ['id' => $item->{$keyColumn}, 'texto' => $item->{$textColumn}]);
        });
    }

    public function getFormasPago()
    {
        return $this->readCatalog(
            self::CFDI_40_PREFIX . 'formas_pago',
            'id',
            'texto',
            'sat.cfdi40.formas_pago'
        );
    }

    public function getMetodosPago()
    {
        return $this->readCatalog(
            self::CFDI_40_PREFIX . 'metodos_pago',
            'id',
            'texto',
            'sat.cfdi40.metodos_pago'
        );
    }

    public function getUsosCfdi()
    {
        return $this->readCatalog(
            self::CFDI_40_PREFIX . 'usos_cfdi',
            'id',
            'texto',
            'sat.cfdi40.usos_cfdi'
        );
    }

    public function getMonedas()
    {
        return $this->readCatalog(
            self::CFDI_40_PREFIX . 'monedas',
            'id',
            'texto',
            'sat.cfdi40.monedas'
        );
    }

    public function getRegimenesFiscales()
    {
        return $this->readCatalog(
            self::CFDI_40_PREFIX . 'regimenes_fiscales',
            'id',
            'texto',
            'sat.cfdi40.regimenes_fiscales'
        );
    }

    public function getTiposDeComprobante()
    {
        return $this->readCatalog(
            self::CFDI_40_PREFIX . 'tipos_comprobantes', 
            'id',
            'texto',
            'sat.cfdi40.tipos_comprobantes'
        );
    }

    public function getObjetosImpuesto()
    {
        return $this->readCatalog(
            self::CFDI_40_PREFIX . 'objetos_impuestos',
            'id',
            'texto',
            'sat.cfdi40.objetos_impuestos');
    }

    public function getClavesUnidad()
    {
        return $this->readCatalog(
            self::CFDI_40_PREFIX . 'claves_unidades', 
            'id', 
            'texto', 
            'sat.cfdi40.claves_unidades');
    }

    public function getPeriodicidades()
    {
        return $this->readCatalog(self::CFDI_40_PREFIX . 'periodicidades', 
        'id', 
        'texto', 
        'sat.cfdi40.periodicidades');
    }

    public function getMeses()
    {
        return $this->readCatalog(self::CFDI_40_PREFIX . 'meses', 
        'id', 
        'texto', 
        'sat.cfdi40.meses');
    }

    public function getTiposRelacion()
    {   
        return $this->readCatalog(
            self::CFDI_40_PREFIX . 'tipos_relaciones', 
            'id', 
            'texto', 
            'sat.cfdi40.tipos_relaciones');
    }

    public function getRetenciones()
    {
        return $this->readCatalog(
            self::RET_20_PREFIX . 'retenciones', 
            'id', 
            'descripcion', 
            'sat.cfdi40.retenciones'
        );
    }

    /**
     * Busca productos o servicios en la base de datos de catálogos del SAT.
     *
     * @param string $query
     * @return \Illuminate\Support\Collection
     */
    public function searchProductosServicios(string $query)
    {
        if (empty(trim($query))) {
            return collect([]);
        }

        $query = mb_strtolower($query);

        // La búsqueda es ahora increíblemente eficiente.
        return DB::table(self::CFDI_40_PREFIX . 'productos_servicios')
            ->where(function ($dbQuery) use ($query) {
                $dbQuery->where(DB::raw('lower(texto)'), 'like', "%{$query}%")
                      ->orWhere('id', 'like', "{$query}%");
            })
            ->take(50)
            ->get(['id', 'texto'])
            ->map(fn($item) => ['id' => $item->id, 'texto' => "{$item->id} - {$item->texto}"]);
    }
}