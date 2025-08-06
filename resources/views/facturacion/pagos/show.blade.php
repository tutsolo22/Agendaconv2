@extends('layouts.tenant')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Detalles del Complemento de Pago {{ $pago->serie }}-{{ $pago->folio }}</div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Datos del Receptor</h5>
                            <p><strong>Cliente:</strong> {{ $pago->cliente->nombre_completo }}</p>
                            <p><strong>RFC:</strong> {{ $pago->cliente->rfc }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Datos del Pago</h5>
                            <p><strong>Fecha de Pago:</strong> {{ $pago->fecha_pago }}</p>
                            <p><strong>Forma de Pago:</strong> {{ $pago->forma_pago }}</p>
                            <p><strong>Monto:</strong> {{ number_format($pago->monto, 2) }} {{ $pago->moneda }}</p>
                            <p><strong>Status:</strong> <span class="badge bg-{{ $pago->status == 'timbrado' ? 'success' : 'warning' }}">{{ ucfirst($pago->status) }}</span></p>
                        </div>
                    </div>

                    <hr>

                    <h4>Documentos Relacionados</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>UUID del Documento</th>
                                <th>Serie-Folio</th>
                                <th>Nº Parcialidad</th>
                                <th>Saldo Anterior</th>
                                <th>Monto Pagado</th>
                                <th>Saldo Insoluto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pago->documentos as $docto)
                                <tr>
                                    <td>{{ $docto->id_documento }}</td>
                                    <td>{{ $docto->serie }}-{{ $docto->folio }}</td>
                                    <td>{{ $docto->num_parcialidad }}</td>
                                    <td>${{ number_format($docto->imp_saldo_ant, 2) }}</td>
                                    <td>${{ number_format($docto->imp_pagado, 2) }}</td>
                                    <td>${{ number_format($docto->imp_saldo_insoluto, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        @if ($pago->status == 'borrador')
                            <form action="{{ route('tenant.facturacion.pagos.timbrar', $pago) }}" method="POST" style="display: inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-success">Timbrar</button>
                            </form>
                        @endif
                        <a href="{{ route('tenant.facturacion.pagos.index') }}" class="btn btn-secondary">Volver al Listado</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
