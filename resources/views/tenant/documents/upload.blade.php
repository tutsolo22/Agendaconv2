<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Subir Documentos de Cliente') }}
        </h2>
    </x-slot>

    @include('partials.flash-messages')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('tenant.documents.upload.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Buscador de Clientes --}}
                <div class="mb-3">
                    <label for="search-client" class="form-label">Buscar Cliente (por nombre o RFC)</label>
                    <div class="input-group">
                        <input type="text" id="search-client-input" class="form-control" placeholder="Escriba para buscar...">
                    </div>
                    <div id="search-results" class="list-group mt-2" style="max-height: 200px; overflow-y: auto;"></div>
                </div>

                {{-- Campo oculto para el ID del cliente y nombre visible --}}
                <input type="hidden" name="cliente_id" id="cliente_id">
                <div id="selected-client-info" class="alert alert-info d-none">
                    <strong>Cliente seleccionado:</strong> <span id="selected-client-name"></span>
                </div>

                {{-- Selector de Módulo --}}
                <div class="mb-3">
                    <label for="modulo_id" class="form-label">Asociar a Módulo</label>
                    <select name="modulo_id" id="modulo_id" class="form-select @error('modulo_id') is-invalid @enderror" required>
                        <option value="">Seleccione un módulo...</option>
                        @foreach($modulos as $modulo)
                            <option value="{{ $modulo->id }}">{{ $modulo->nombre }}</option>
                        @endforeach
                    </select>
                    @error('modulo_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Campo de Descripción --}}
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción del Documento</label>
                    <input type="text" name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Ej: Rayos X panorámicos, Factura de compra, etc.">
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Campo de Subida de Archivo --}}
                <div class="mb-3">
                    <label for="documento" class="form-label">Archivo</label>
                    <input type="file" name="documento" id="documento" class="form-control @error('documento') is-invalid @enderror" required>
                    @error('documento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary" id="submit-button" disabled>
                        <i class="fa-solid fa-upload me-2"></i>Subir Documento
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search-client-input');
            const resultsContainer = document.getElementById('search-results');
            const clienteIdInput = document.getElementById('cliente_id');
            const selectedClientInfo = document.getElementById('selected-client-info');
            const selectedClientName = document.getElementById('selected-client-name');
            const submitButton = document.getElementById('submit-button');

            let searchTimeout;

            searchInput.addEventListener('keyup', function () {
                clearTimeout(searchTimeout);
                const term = this.value;

                if (term.length < 3) {
                    resultsContainer.innerHTML = '';
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('tenant.documents.search.clients') }}?term=${term}`)
                        .then(response => response.json())
                        .then(data => {
                            resultsContainer.innerHTML = '';
                            data.forEach(cliente => {
                                const item = document.createElement('a');
                                item.href = '#';
                                item.className = 'list-group-item list-group-item-action';
                                item.textContent = `${cliente.nombre_completo} (${cliente.rfc || 'Sin RFC'})`;
                                item.dataset.id = cliente.id;
                                item.addEventListener('click', function (e) {
                                    e.preventDefault();
                                    clienteIdInput.value = this.dataset.id;
                                    selectedClientName.textContent = this.textContent;
                                    selectedClientInfo.classList.remove('d-none');
                                    resultsContainer.innerHTML = '';
                                    searchInput.value = '';
                                    submitButton.disabled = false;
                                });
                                resultsContainer.appendChild(item);
                            });
                        });
                }, 300);
            });
        });
    </script>
    @endpush
</x-layouts.app>