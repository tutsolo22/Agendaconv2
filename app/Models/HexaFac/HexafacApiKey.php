<?php

namespace App\Models\HexaFac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HexafacApiKey extends Model
{
    use HasFactory;

    protected $table = 'hexafac_api_keys';

    protected $fillable = [
        'application_id',
        'key',
        'scopes',
        'last_used_at',
        'expires_at',
        'active',
    ];

    protected $casts = [
        'scopes' => 'json',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function application()
    {
        return $this->belongsTo(HexafacClientApplication::class, 'application_id');
    }
}
