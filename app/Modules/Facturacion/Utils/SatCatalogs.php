<?php

namespace App\Modules\Facturacion\Utils;

class SatCatalogs
{
    public static function cleanRazonSocial(string $razonSocial): string
    {
        $regimenes = [
            'S.A. DE C.V.', 'SA DE CV', 'S.A.', 'SA', 'C.V.', 'CV',
            'S.C.', 'SC', 'S.A.S.', 'SAS', 'S. DE R.L. DE C.V.', 'S DE RL DE CV'
        ];
        $pattern = '/\s*,\s*|\s+(' . implode('|', array_map('preg_quote', $regimenes)) . ')\s*$/i';
        return trim(preg_replace($pattern, '', $razonSocial));
    }

    public static function getRegimenesFiscales(): array
    {
        // Idealmente, esto vendría de la base de datos.
        return [
            '601' => '601 - General de Ley Personas Morales',
            '612' => '612 - Personas Físicas con Actividades Empresariales y Profesionales',
            '626' => '626 - Régimen Simplificado de Confianza',
        ];
    }
}