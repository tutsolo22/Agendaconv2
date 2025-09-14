<?php

namespace App\Modules\Facturacion\Models\Complemento\Nomina;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $table = 'nominas_empleados';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'rfc',
        'curp',
        'nss',
        'email',
        'telefono',
        'calle',
        'numero_exterior',
        'numero_interior',
        'colonia',
        'localidad',
        'municipio',
        'estado',
        'pais',
        'codigo_postal',
        'puesto',
        'fecha_inicio_rel_laboral',
        'sat_tipo_contrato_id',
        'sat_tipo_regimen_id',
        'sat_tipo_jornada_id',
        'sat_riesgo_puesto_id',
        'salario_base_cotizacion_apor',
        'salario_diario_integrado',
        'sat_banco_id',
        'cuenta_bancaria',
        'is_active',
    ];

    public function recibos()
    {
        return $this->hasMany(Recibo::class, 'nominas_empleado_id');
    }
}
