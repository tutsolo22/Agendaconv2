<?php

namespace App\Rules;

use App\Models\Licencia;
use App\Models\User;
use Carbon\Carbon;
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
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        if ($currentUser->is_super_admin) {
            return; // Los Super Admins no están sujetos a este límite.
        }

        if (!$currentUser->tenant_id) {
            $fail('No estás asociado a ningún tenant.');
            return;
        }

        // Buscamos el límite más alto de usuarios en todas las licencias activas del tenant.
        $maxUsers = Licencia::where('tenant_id', $currentUser->tenant_id)
            ->where('is_active', true)
            ->whereDate('fecha_expiracion', '>', Carbon::now())
            ->max('max_usuarios');

        // Si no hay licencia, no se pueden crear usuarios.
        if (is_null($maxUsers)) {
            $fail('No se ha encontrado una licencia activa que defina el límite de usuarios.');
            return;
        }

        $currentUserCount = User::where('tenant_id', $currentUser->tenant_id)->count();

        if ($currentUserCount >= $maxUsers) {
            $fail('Ha alcanzado el límite máximo de ' . $maxUsers . ' usuarios permitidos por su licencia.');
        }
    }
}