<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantSetting extends Model
{
    use HasFactory, TenantScoped;

    protected $fillable = ['tenant_id', 'sucursal_id', 'group', 'key', 'value'];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }
}