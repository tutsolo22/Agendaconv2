<?php

namespace App\Models\HexaFac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Tenant;

class HexafacClientApplication extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'hexafac_client_applications';

    protected $fillable = [
        'uuid',
        'tenant_id',
        'name',
        'active',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function apiKeys()
    {
        return $this->hasMany(HexafacApiKey::class, 'application_id');
    }

    public function webhooks()
    {
        return $this->hasMany(HexafacWebhookConfiguration::class, 'application_id');
    }
}
