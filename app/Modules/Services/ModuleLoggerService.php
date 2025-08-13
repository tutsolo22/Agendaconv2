<?php

namespace App\Modules\Services;

use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class ModuleLoggerService
{
    /**
     * Registra un mensaje en el canal del módulo especificado.
     * Si el canal no existe, utiliza el canal por defecto.
     *
     * @param string $module El nombre del canal (ej. 'facturacion').
     * @param string $level El nivel del log (ej. 'info', 'error', 'warning').
     * @param string $message El mensaje a registrar.
     * @param array $context Datos adicionales para el log.
     */
    public function log(string $module, string $level, string $message, array $context = []): void
    {
        try {
            // Intenta usar el canal específico del módulo
            Log::channel($module)->$level($message, $context);
        } catch (InvalidArgumentException $e) {
            // Si el canal no está definido en config/logging.php,
            // usa el canal por defecto para no perder el log.
            Log::stack(['stack'])->$level("[Módulo: {$module}] " . $message, $context);
        }
    }

    /**
     * Helper para registrar un mensaje de nivel 'info'.
     */
    public function info(string $module, string $message, array $context = []): void
    {
        $this->log($module, 'info', $message, $context);
    }

    /**
     * Helper para registrar un mensaje de nivel 'error'.
     */
    public function error(string $module, string $message, array $context = []): void
    {
        $this->log($module, 'error', $message, $context);
    }

    /**
     * Helper para registrar un mensaje de nivel 'warning'.
     */
    public function warning(string $module, string $message, array $context = []): void
    {
        $this->log($module, 'warning', $message, $context);
    }
}
