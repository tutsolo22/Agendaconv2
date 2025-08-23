<?php

namespace App\Modules\Facturacion\Http\Controllers\Retencion;

use App\Http\Controllers\Controller;
use App\Modules\Facturacion\Models\Configuracion\SerieFolio;
use App\Modules\Facturacion\Models\Retencion\Retencion;
use App\Modules\Facturacion\Services\RetencionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RetencionController extends Controller
{
    /**
     * Muestra una lista de las retenciones.
     */
    public function index(): View
    {
        $retenciones = Retencion::with('cliente')->latest()->paginate(15);
        return view('facturacion::retenciones.index', compact('retenciones'));
    }

    /**
     * Muestra el formulario para crear una nueva retención.
     */
    public function create(): View
    {
        $series = SerieFolio::where('tipo_comprobante', 'R')->where('is_active', true)->get();
        return view('facturacion::retenciones.create', compact('series'));
    }

    /**
     * Guarda un nuevo borrador de retención en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'serie_folio_id' => ['required', Rule::exists('facturacion_series_folios', 'id')->where('tipo_comprobante', 'R')],
            'fecha_exp' => 'required|date',
            'cve_retenc' => 'required|string|max:2',
            'desc_retenc' => 'nullable|string|max:255',
            'monto_total_operacion' => 'required|numeric|min:0',
            'monto_total_retenido' => 'required|numeric|min:0',
            'impuestos' => 'required|array|min:1',
            'impuestos.*.base_ret' => 'required|numeric|min:0',
            'impuestos.*.impuesto' => 'required|string|max:2',
            'impuestos.*.tipo_pago_ret' => 'required|string',
            'impuestos.*.monto_ret' => 'required|numeric|min:0',
        ]);

        try {
            $retencion = DB::transaction(function () use ($validated) {
                $serieFolio = SerieFolio::findOrFail($validated['serie_folio_id']);
                $folio = $serieFolio->folio_actual + 1;

                $retencion = Retencion::create([
                    'cliente_id' => $validated['cliente_id'],
                    'serie_folio_id' => $validated['serie_folio_id'],
                    'serie' => $serieFolio->serie,
                    'folio' => $folio,
                    'fecha_exp' => $validated['fecha_exp'],
                    'cve_retenc' => $validated['cve_retenc'],
                    'desc_retenc' => $validated['desc_retenc'],
                    'monto_total_operacion' => $validated['monto_total_operacion'],
                    'monto_total_retenido' => $validated['monto_total_retenido'],
                    'status' => 'borrador',
                ]);

                foreach ($validated['impuestos'] as $impuestoData) {
                    $retencion->impuestos()->create($impuestoData);
                }

                $serieFolio->update(['folio_actual' => $folio]);
                return $retencion;
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear el borrador de la retención: ' . $e->getMessage());
        }

        return redirect()->route('tenant.facturacion.retenciones.show', $retencion)
            ->with('success', 'Borrador de la retención creado exitosamente.');
    }

    /**
     * Muestra los detalles de una retención específica.
     */
    public function show(Retencion $retencion): View
    {
        $retencion->load('cliente', 'impuestos');
        return view('facturacion::retenciones.show', compact('retencion'));
    }

    /**
     * Muestra el formulario para editar una retención.
     */
    public function edit(Retencion $retencion): View|RedirectResponse
    {
        if ($retencion->status !== 'borrador') {
            return redirect()->route('tenant.facturacion.retenciones.show', $retencion)
                ->with('error', 'Solo se pueden editar retenciones en estado de borrador.');
        }

        $series = SerieFolio::where('tipo_comprobante', 'R')->where('is_active', true)->get();
        $retencion->load('impuestos', 'cliente');

        return view('facturacion::retenciones.edit', compact('retencion', 'series'));
    }

    /**
     * Actualiza una retención en la base de datos.
     */
    public function update(Request $request, Retencion $retencion): RedirectResponse
    {
        if ($retencion->status !== 'borrador') {
            return redirect()->route('tenant.facturacion.retenciones.show', $retencion)
                ->with('error', 'Solo se pueden editar retenciones en estado de borrador.');
        }

        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            // La serie y folio no se actualizan en un borrador existente
            'fecha_exp' => 'required|date',
            'cve_retenc' => 'required|string|max:2',
            'desc_retenc' => 'nullable|string|max:255',
            'monto_total_operacion' => 'required|numeric|min:0',
            'monto_total_retenido' => 'required|numeric|min:0',
            'impuestos' => 'required|array|min:1',
            'impuestos.*.base_ret' => 'required|numeric|min:0',
            'impuestos.*.impuesto' => 'required|string|max:2',
            'impuestos.*.tipo_pago_ret' => 'required|string',
            'impuestos.*.monto_ret' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($retencion, $validated) {
                // Actualizar el registro principal de la retención
                $retencion->update($validated);

                // Sincronizar los impuestos: eliminar los antiguos y crear los nuevos
                $retencion->impuestos()->delete();
                foreach ($validated['impuestos'] as $impuestoData) {
                    $retencion->impuestos()->create($impuestoData);
                }
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar el borrador de la retención: ' . $e->getMessage());
        }

        return redirect()->route('tenant.facturacion.retenciones.show', $retencion)
            ->with('success', 'Borrador de la retención actualizado exitosamente.');
    }

    /**
     * Elimina una retención (solo si es borrador).
     */
    public function destroy(Retencion $retencion): RedirectResponse
    {
        if ($retencion->status !== 'borrador') {
            return redirect()->route('tenant.facturacion.retenciones.show', $retencion)
                ->with('error', 'Solo se pueden eliminar retenciones en estado de borrador.');
        }

        try {
            // La restricción de clave foránea con onDelete('cascade') en la migración
            // se encargará de eliminar los impuestos asociados automáticamente.
            $retencion->delete();
        } catch (\Exception $e) {
            return redirect()->route('tenant.facturacion.retenciones.index')
                ->with('error', 'Error al eliminar el borrador de la retención: ' . $e->getMessage());
        }

        return redirect()->route('tenant.facturacion.retenciones.index')
            ->with('success', 'Borrador de la retención eliminado exitosamente.');
    }

    /**
     * Timbra una retención.
     */
    public function timbrar(Retencion $retencion, RetencionService $retencionService): RedirectResponse
    {
        try {
            $resultado = $retencionService->timbrar($retencion);

            if (!$resultado->success) {
                return back()->with('error', 'Error al timbrar: ' . $resultado->message);
            }

            return redirect()->route('tenant.facturacion.retenciones.show', $retencion)
                ->with('success', $resultado->message);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al timbrar: ' . $e->getMessage());
        }
    }
}