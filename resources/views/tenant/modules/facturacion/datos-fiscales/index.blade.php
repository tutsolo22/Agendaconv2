<x-layouts.app>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                <i class="fa-solid fa-id-card me-2"></i>
                Gestión de Datos Fiscales
            </h2>
            {{-- Solo mostrar el botón de crear si no existen datos fiscales --}}
            @if($datosFiscales->isEmpty())
                <a href="{{ route('tenant.facturacion.configuracion.datos-fiscales.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-2"></i>Configurar Datos Fiscales
                </a>
            @endif
        </div>
    </x-slot>

    @include('partials.flash-messages')

    <div class="alert alert-info">
        <i class="fa-solid fa-circle-info me-2"></i>
        Aquí se configura la información fiscal de la empresa (emisor) que se utilizará para generar los CFDI. Solo puede existir un registro.
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>RFC</th>
                            <th>Razón Social</th>
                            <th>Régimen Fiscal</th>
                            <th>C.P. Fiscal</th>
                            <th>PAC Asignado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($datosFiscales as $datoFiscal)
                            <tr>
                                <td>{{ $datoFiscal->rfc }}</td>
                                <td>{{ $datoFiscal->razon_social }}</td>
                                <td>{{ $datoFiscal->regimen_fiscal_clave }}</td>
                                <td>{{ $datoFiscal->cp_fiscal }}</td>
                                <td>{{ $datoFiscal->pac->nombre ?? 'No asignado' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('tenant.facturacion.configuracion.datos-fiscales.edit', $datoFiscal) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fa-solid fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('tenant.facturacion.configuracion.datos-fiscales.destroy', $datoFiscal) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea eliminar los datos fiscales? Esta acción no se puede deshacer.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No hay datos fiscales configurados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
