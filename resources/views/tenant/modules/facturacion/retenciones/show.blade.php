<x-layouts.app>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                <i class="fa-solid fa-file-signature me-2"></i>
                Detalle de Retención: {{ $retencion->serie }}-{{ $retencion->folio }}
            </h2>
            <a href="{{ route('tenant.facturacion.retenciones.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-end gap-2">
                @if($retencion->status === 'borrador')
                    <a href="#" class="btn btn-success disabled" title="Timbrar (En desarrollo)"><i class="fa-solid fa-check me-1"></i> Timbrar</a>
                    <a href="{{ route('tenant.facturacion.retenciones.edit', $retencion) }}" class="btn btn-warning" title="Editar"><i class="fa-solid fa-pen-to-square me-1"></i> Editar</a>
                    <form action="{{ route('tenant.facturacion.retenciones.destroy', $retencion) }}" method="POST" onsubmit="return confirm('¿Está seguro de que desea eliminar este borrador?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" title="Eliminar Borrador"><i class="fa-solid fa-trash me-1"></i> Eliminar</button>
                    </form>
                @elseif($retencion->status === 'timbrado')
                    <a href="#" class="btn btn-secondary disabled" title="Descargar XML (En desarrollo)"><i class="fa-solid fa-file-code me-1"></i> XML</a>
                    <a href="#" class="btn btn-danger disabled" title="Descargar PDF (En desarrollo)"><i class="fa-solid fa-file-pdf me-1"></i> PDF</a>
                    <a href="#" class="btn btn-warning disabled" title="Cancelar (En desarrollo)"><i class="fa-solid fa-ban me-1"></i> Cancelar</a>
                @endif
            </div>
        </div>
        <div class="card-body">
            @include('partials.flash-messages')

            <div class="row">
                <div class="col-md-8">
                    <h5>Datos del Receptor</h5>
                    <p><strong>Cliente:</strong> {{ $retencion->cliente->nombre_completo }}</p>
                    <p><strong>RFC:</strong> {{ $retencion->cliente->rfc }}</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <h5>Datos del Comprobante</h5>
                    <p><strong>Serie y Folio:</strong> {{ $retencion->serie }}-{{ $retencion->folio }}</p>
                    <p><strong>Fecha de Expedición:</strong> {{ \Carbon\Carbon::parse($retencion->fecha_exp)->format('d/m/Y H:i:s') }}</p>
                    <p><strong>UUID:</strong> {{ $retencion->uuid_fiscal ?? 'N/A' }}</p>
                    <p><strong>Estatus:</strong> <span class="badge bg-{{ $retencion->status == 'timbrado' ? 'success' : ($retencion->status == 'cancelado' ? 'danger' : 'warning') }}">{{ ucfirst($retencion->status) }}</span></p>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Clave de Retención:</strong> {{ $retencion->cve_retenc }}</p>
                    <p><strong>Descripción:</strong> {{ $retencion->desc_retenc ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Monto Total Operación:</strong> ${{ number_format($retencion->monto_total_operacion, 2) }}</p>
                    <p><strong>Monto Total Retenido:</strong> ${{ number_format($retencion->monto_total_retenido, 2) }}</p>
                </div>
            </div>

            <hr>

            <h5>Impuestos Retenidos</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Base del Impuesto</th>
                            <th>Impuesto</th>
                            <th>Tipo de Pago</th>
                            <th class="text-end">Monto Retenido</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($retencion->impuestos as $impuesto)
                            <tr>
                                <td>${{ number_format($impuesto->base_ret, 2) }}</td>
                                <td>{{ $impuesto->impuesto }}</td>
                                <td>{{ $impuesto->tipo_pago_ret }}</td>
                                <td class="text-end">${{ number_format($impuesto->monto_ret, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>