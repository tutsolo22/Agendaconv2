<x-layouts.app>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                <i class="fa-solid fa-file-invoice-dollar me-2"></i>
                Detalle del Complemento de Pago: {{ $pago->serie }}-{{ $pago->folio }}
            </h2>
            <div class="d-flex gap-1">
                <a href="{{ route('tenant.facturacion.pagos.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left me-2"></i>
                    Volver al Listado
                </a>
                @if ($pago->status == 'timbrado')
                    <a href="{{ route('tenant.facturacion.pagos.download.xml', $pago) }}" class="btn btn-dark" title="Descargar XML">
                        <i class="fa-solid fa-file-code me-2"></i> XML
                    </a>
                    <a href="{{ route('tenant.facturacion.pagos.download.pdf', $pago) }}" class="btn btn-danger" title="Descargar PDF">
                        <i class="fa-solid fa-file-pdf me-2"></i> PDF
                    </a>
                @elseif ($pago->status == 'borrador')
                    <form action="{{ route('tenant.facturacion.pagos.timbrar', $pago) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success"><i class="fa-solid fa-check-circle me-2"></i>Timbrar Complemento</button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-body">
            @include('partials.flash-messages')

            <div class="row mb-4">
                <div class="col-md-6">
                    <h5><i class="fa-solid fa-user me-2"></i>Datos del Receptor</h5>
                    <p><strong>Cliente:</strong> {{ $pago->cliente->nombre_completo }}</p>
                    <p><strong>RFC:</strong> {{ $pago->cliente->rfc }}</p>
                </div>
                <div class="col-md-6">
                    <h5><i class="fa-solid fa-money-bill-wave me-2"></i>Datos del Pago</h5>
                    <p><strong>Fecha de Pago:</strong> {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i:s') }}</p>
                    <p><strong>Forma de Pago:</strong> {{ $pago->forma_pago }} - {{ $formasPago[$pago->forma_pago] ?? 'Desconocida' }}</p>
                    <p><strong>Monto:</strong> ${{ number_format($pago->monto, 2) }} {{ $pago->moneda }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge bg-{{ $pago->status == 'timbrado' ? 'success' : ($pago->status == 'cancelado' ? 'danger' : 'warning') }}">
                            {{ ucfirst($pago->status) }}
                        </span>
                    </p>
                    @if($pago->uuid_fiscal)
                        <p><strong>UUID Fiscal:</strong> {{ $pago->uuid_fiscal }}</p>
                    @endif
                </div>
            </div>
            <hr>
            <h4><i class="fa-solid fa-file-lines me-2"></i>Documentos Relacionados</h4>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>UUID del Documento</th>
                            <th>Serie-Folio</th>
                            <th>Nº Parcialidad</th>
                            <th class="text-end">Saldo Anterior</th>
                            <th class="text-end">Monto Pagado</th>
                            <th class="text-end">Saldo Insoluto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pago->documentos as $docto)
                            <tr>
                                <td>
                                    <span title="{{ $docto->id_documento }}">{{ substr($docto->id_documento, 0, 8) }}...</span>
                                </td>
                                <td>{{ $docto->serie }}-{{ $docto->folio }}</td>
                                <td>{{ $docto->num_parcialidad }}</td>
                                <td class="text-end">${{ number_format($docto->imp_saldo_ant, 2) }}</td>
                                <td class="text-end">${{ number_format($docto->imp_pagado, 2) }}</td>
                                <td class="text-end">${{ number_format($docto->imp_saldo_insoluto, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay documentos relacionados para este pago.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>