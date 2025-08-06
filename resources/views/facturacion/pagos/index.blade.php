@extends('layouts.tenant')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Complementos de Pago</div>

                <div class="card-body">
                    <a href="{{ route('tenant.facturacion.pagos.create') }}" class="btn btn-primary mb-3">Nuevo Complemento de Pago</a>

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Serie y Folio</th>
                                <th>Cliente</th>
                                <th>Fecha de Pago</th>
                                <th>Monto</th>
                                <th>Status</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pagos as $pago)
                                <tr>
                                    <td>{{ $pago->serie }}-{{ $pago->folio }}</td>
                                    <td>{{ $pago->cliente->nombre_completo }}</td>
                                    <td>{{ $pago->fecha_pago }}</td>
                                    <td>{{ number_format($pago->monto, 2) }} {{ $pago->moneda }}</td>
                                    <td>
                                        <span class="badge bg-{{ $pago->status == 'timbrado' ? 'success' : ($pago->status == 'cancelado' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($pago->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('tenant.facturacion.pagos.show', $pago) }}" class="btn btn-sm btn-info">Ver</a>
                                        @if ($pago->status == 'borrador')
                                            <a href="{{ route('tenant.facturacion.pagos.edit', $pago) }}" class="btn btn-sm btn-primary">Editar</a>
                                            <form action="{{ route('tenant.facturacion.pagos.destroy', $pago) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este borrador?')">Eliminar</button>
                                            </form>
                                        @endif
                                        @if ($pago->status == 'timbrado')
                                            <a href="{{ route('tenant.facturacion.pagos.download.xml', $pago) }}" class="btn btn-sm btn-secondary">XML</a>
                                            <a href="{{ route('tenant.facturacion.pagos.download.pdf', $pago) }}" class="btn btn-sm btn-secondary">PDF</a>
                                            <form action="{{ route('tenant.facturacion.pagos.cancelar', $pago) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('¿Estás seguro de que deseas cancelar este complemento?')">Cancelar</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $pagos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
