<?php

namespace App\Models\HexaFac;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class HexaFacClientApplication extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'hexafac_client_applications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Get the tenant that owns the application.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * The API keys that belong to the application.
     */
    public function apiKeys()
    {
        return $this->hasMany(HexaFacApiKey::class, 'application_id');
    }
}