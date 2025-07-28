<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'id',
    ];

    public $incrementing = false;

    public function modulos(): BelongsToMany
    {
        return $this->belongsToMany(Modulo::class, 'modulo_tenant');
    }

    public function licencias(): HasMany
    {
        return $this->hasMany(Licencia::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}