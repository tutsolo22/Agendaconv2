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
    private const CCP_31_PREFIX = 'sat_ccp_31_';
    private const NOMINA_PREFIX = 'sat_nomina_'; // Nuevo prefijo

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

    // --- MÉTODOS DE CATÁLOGOS DE NÓMINA ---

    public function getTiposNomina() { return $this->readCatalog(self::NOMINA_PREFIX . 'tipos_nominas', 'id', 'texto', 'sat.nomina.tipos_nomina'); }
    public function getPeriodicidadesPago() { return $this->readCatalog(self::NOMINA_PREFIX . 'periodicidades_pagos', 'id', 'texto', 'sat.nomina.periodicidades_pago'); }
    public function getTiposContrato() { return $this->readCatalog(self::NOMINA_PREFIX . 'tipos_contratos', 'id', 'texto', 'sat.nomina.tipos_contrato'); }
    public function getTiposRegimen() { return $this->readCatalog(self::CFDI_40_PREFIX . 'regimenes_fiscales', 'id', 'texto', 'sat.cfdi40.regimenes_fiscales'); } // Reutiliza el de CFDI40
    public function getTiposJornada() { return $this->readCatalog(self::NOMINA_PREFIX . 'tipos_jornadas', 'id', 'texto', 'sat.nomina.tipos_jornada'); }
    public function getRiesgosPuesto() { return $this->readCatalog(self::NOMINA_PREFIX . 'riesgos_puestos', 'id', 'texto', 'sat.nomina.riesgos_puesto'); }
    public function getBancos() { return $this->readCatalog(self::NOMINA_PREFIX . 'bancos', 'id', 'texto', 'sat.nomina.bancos'); }
    public function getTiposPercepcion() { return $this->readCatalog(self::NOMINA_PREFIX . 'tipos_percepciones', 'id', 'texto', 'sat.nomina.tipos_percepcion'); }
    public function getTiposDeduccion() { return $this->readCatalog(self::NOMINA_PREFIX . 'tipos_deducciones', 'id', 'texto', 'sat.nomina.tipos_deduccion'); }
    public function getTiposOtroPago() { return $this->readCatalog(self::NOMINA_PREFIX . 'tipos_otros_pagos', 'id', 'texto', 'sat.nomina.tipos_otro_pago'); }
    public function getTiposIncapacidad() { return $this->readCatalog(self::NOMINA_PREFIX . 'tipos_incapacidades', 'id', 'texto', 'sat.nomina.tipos_incapacidad'); }

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
            self::RET_20_PREFIX . 'claves_retencion', 
            'id', 
            'texto', 
            'sat.ret20.claves_retencion'
        );
    }

    // Métodos para catálogos de Carta Porte 3.1
    public function getAutorizacionesNavieroCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'autorizaciones_naviero',
            'id',
            'texto',
            'sat.ccp31.autorizaciones_naviero'
        );
    }

    public function getClavesUnidadesCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'claves_unidades',
            'id',
            'texto',
            'sat.ccp31.claves_unidades'
        );
    }

    public function getCodigosTransporteAereoCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'codigos_transporte_aereo',
            'id',
            'texto',
            'sat.ccp31.codigos_transporte_aereo'
        );
    }

    public function getColoniasCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'colonias',
            'colonia',
            'texto',
            'sat.ccp31.colonias'
        );
    }

    public function getCondicionesEspecialesCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'condiciones_especiales',
            'id',
            'texto',
            'sat.ccp31.condiciones_especiales'
        );
    }

    public function getConfiguracionesAutotransporteCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'configuraciones_autotransporte',
            'id',
            'texto',
            'sat.ccp31.configuraciones_autotransporte'
        );
    }

    public function getConfiguracionesMaritimasCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'configuraciones_maritimas',
            'id',
            'texto',
            'sat.ccp31.configuraciones_maritimas'
        );
    }

    public function getContenedoresCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'contenedores',
            'id',
            'texto',
            'sat.ccp31.contenedores'
        );
    }

    public function getContenedoresMaritimosCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'contenedores_maritimos',
            'id',
            'texto',
            'sat.ccp31.contenedores_maritimos'
        );
    }

    public function getDerechosDePasoCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'derechos_de_paso',
            'id',
            'texto',
            'sat.ccp31.derechos_de_paso'
        );
    }

    public function getDocumentosAduanerosCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'documentos_aduaneros',
            'id',
            'texto',
            'sat.ccp31.documentos_aduaneros'
        );
    }

    public function getEstacionesCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'estaciones',
            'id',
            'texto',
            'sat.ccp31.estaciones'
        );
    }

    public function getFigurasTransporteCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'figuras_transporte',
            'id',
            'texto',
            'sat.ccp31.figuras_transporte'
        );
    }

    public function getFormasFarmaceuticasCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'formas_farmaceuticas',
            'id',
            'texto',
            'sat.ccp31.formas_farmaceuticas'
        );
    }

    public function getLocalidadesCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'localidades',
            'localidad',
            'texto',
            'sat.ccp31.localidades'
        );
    }

    public function getMaterialesPeligrososCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'materiales_peligrosos',
            'id',
            'texto',
            'sat.ccp31.materiales_peligrosos'
        );
    }

    public function getMunicipiosCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'municipios',
            'municipio',
            'texto',
            'sat.ccp31.municipios'
        );
    }

    public function getPartesTransporteCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'partes_transporte',
            'id',
            'texto',
            'sat.ccp31.partes_transporte'
        );
    }

    public function getProductosServiciosCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'productos_servicios',
            'id',
            'texto',
            'sat.ccp31.productos_servicios'
        );
    }

    public function getRegimenesAduanerosCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'regimenes_aduaneros',
            'id',
            'texto',
            'sat.ccp31.regimenes_aduaneros'
        );
    }

    public function getTiposPermisoCcp31()
    {
        return $this->readCatalog(
            self::CCP_31_PREFIX . 'tipos_permiso',
            'id',
            'texto',
            'sat.ccp31.tipos_permiso'
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

    public function getCodigoPostalInfo(string $codigoPostal)
    {
        $cacheKey = 'sat.ccp31.codigopostal.' . $codigoPostal;

        return Cache::rememberForever($cacheKey, function () use ($codigoPostal) {
            $cpData = DB::table(self::CCP_31_PREFIX . 'codigos_postales as cp')
                ->where('cp.id', $codigoPostal)
                ->join(self::CCP_31_PREFIX . 'estados as est', 'cp.estado', '=', 'est.id')
                ->join(self::CCP_31_PREFIX . 'municipios as mun', 'cp.municipio', '=', 'mun.id')
                ->leftJoin(self::CCP_31_PREFIX . 'localidades as loc', 'cp.localidad', '=', 'loc.id')
                ->select(
                    'est.texto as estado',
                    'mun.texto as municipio',
                    'loc.texto as localidad'
                )
                ->first();

            if (!$cpData) {
                return null;
            }

            $colonias = DB::table(self::CCP_31_PREFIX . 'colonias as col')
                ->where('col.codigo_postal', $codigoPostal)
                ->select('col.id', 'col.texto')
                ->get();

            return [
                'estado' => $cpData->estado,
                'municipio' => $cpData->municipio,
                'localidad' => $cpData->localidad,
                'colonias' => $colonias,
            ];
        });
    }
}