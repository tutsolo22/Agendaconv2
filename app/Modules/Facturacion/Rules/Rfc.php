<?php

namespace App\Modules\Facturacion\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Rfc implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /**
         * Expresión regular para RFC de persona física (13 caracteres) o moral (12 caracteres).
         * - [A-ZÑ&]{4} para personas físicas o [A-ZÑ&]{3} para morales.
         * - [0-9]{6} para fecha (YYMMDD).
         * - [A-Z0-9]{3} para la homoclave.
         */
        $pattern = '/^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$/';

        if (!preg_match($pattern, strtoupper($value))) {
            $fail('El campo :attribute no tiene un formato de RFC válido.');
        }
    }
}