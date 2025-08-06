<?php

namespace App\Modules\Facturacion\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class DatoFiscal extends Model
{
    use HasFactory, TenantScoped;

    protected $table = 'facturacion_datos_fiscales';

    protected $fillable = [
        'razon_social',
        'rfc',
        'regimen_fiscal_clave',
        'cp_fiscal',
        'path_cer_pem',
        'path_key_pem',
        'password_csd', // Se gestiona a través del mutator
        'valido_hasta',
        'pac_id',
        'en_pruebas',
    ];

protected $casts = [
        'en_pruebas' => 'boolean',
        'password_csd' => 'encrypted',
        'valido_hasta' => 'date',
    ];
    
    // Mutador para encriptar/desencriptar la contraseña del CSD
    protected function passwordCsd(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => isset($value) ? Crypt::decryptString($value) : null,
            set: fn ($value) => isset($value) ? Crypt::encryptString($value) : null
        );
    }
    public function pac(): BelongsTo
    {
        return $this->belongsTo(Pac::class);
    }
}