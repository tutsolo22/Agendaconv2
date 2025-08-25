<?php

namespace App\Rules;

use App\Models\Licencia;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class UserLimitPerTenant implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = Auth::user();

        if (!$user || !$tenantId = $user->tenant_id) {
            // Esta regla solo se aplica en el contexto de un tenant.
            return;
        }

        // 1. Calcular el límite total de usuarios para el tenant.
        // Sumamos los límites de todas las licencias activas y no expiradas.
        $userLimit = Licencia::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where('fecha_fin', '>', now())
            ->sum('limite_usuarios');

        // 2. Contar el número actual de usuarios del tenant.
        $currentUserCount = User::where('tenant_id', $tenantId)->count();

        // 3. Comparar y fallar si se ha alcanzado el límite.
        if ($currentUserCount >= $userLimit) {
            $fail("Ha alcanzado el límite de {$userLimit} usuarios permitido por su licencia.");
        } elseif ($userLimit === 0) {
            $fail('No tiene una licencia activa que permita agregar usuarios.');
        }
    }
}