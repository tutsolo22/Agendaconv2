<?php

namespace App\Models\HexaFac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HexafacWebhookConfiguration extends Model
{
    use HasFactory;

    protected $table = 'hexafac_webhook_configurations';

    protected $fillable = [
        'application_id',
        'url',
        'secret',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function application()
    {
        return $this->belongsTo(HexafacClientApplication::class, 'application_id');
    }
}
