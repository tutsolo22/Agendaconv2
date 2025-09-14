import TomSelect from 'tom-select';

document.addEventListener('DOMContentLoaded', function () {
    // =========================================================================
    // Carga de Catálogos
    // =========================================================================
    fetch(window.nominaApiUrls.catalogs)
        .then(response => response.json())
        .then(data => {
            populateSelect('tipo_nomina', data.tipos_nomina);
            populateSelect('periodicidad_pago', data.periodicidades_pago);
            // ... poblar los demás catálogos
        })
        .catch(error => console.error('Error al cargar los catálogos de nómina:', error));

    // =========================================================================
    // Búsqueda de Empleados con TomSelect
    // =========================================================================
    new TomSelect('#empleado_id', {
        valueField: 'id',
        labelField: 'nombre_completo',
        searchField: ['nombre_completo', 'rfc'],
        load: function(query, callback) {
            if (query.length < 2) return callback();
            fetch(`${window.nominaApiUrls.searchEmpleados}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(json => {
                    callback(json);
                }).catch(()=>{
                    callback();
                });
        },
        render: {
            option: function(item, escape) {
                return `<div><strong>${escape(item.nombre_completo)}</strong><br><small class="text-muted">${escape(item.rfc)}</small></div>`;
            },
            item: function(item, escape) {
                return `<div>${escape(item.nombre_completo)}</div>`;
            }
        }
    });

    // =========================================================================
    // Funciones de Ayuda
    // =========================================================================

    /**
     * Rellena un <select> con opciones desde un array de catálogos.
     * @param {string} selectId El ID del elemento <select>.
     * @param {Array} items El array de objetos, cada uno con 'id' y 'texto'.
     */
    function populateSelect(selectId, items) {
        const select = document.getElementById(selectId);
        if (!select) return;

        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = `${item.id} - ${item.texto}`;
            select.appendChild(option);
        });
    }

    // =========================================================================
    // Lógica para Percepciones, Deducciones y Otros Pagos (Simplificado)
    // =========================================================================
    document.getElementById('add-percepcion').addEventListener('click', function() {
        addDynamicRow('percepciones-table', ['Tipo', 'Clave', 'Concepto', 'Gravado', 'Exento']);
    });

    document.getElementById('add-deduccion').addEventListener('click', function() {
        addDynamicRow('deducciones-table', ['Tipo', 'Clave', 'Concepto', 'Importe']);
    });

    document.getElementById('add-otropago').addEventListener('click', function() {
        addDynamicRow('otrospagos-table', ['Tipo', 'Clave', 'Concepto', 'Importe']);
    });

    /**
     * Agrega una fila a una tabla con celdas de input.
     * @param {string} tableId 
     * @param {Array} fields 
     */
    function addDynamicRow(tableId, fields) {
        const tableBody = document.querySelector(`#${tableId} tbody`);
        const newRow = document.createElement('tr');
        
        fields.forEach(field => {
            const cell = document.createElement('td');
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'form-control';
            input.placeholder = field;
            cell.appendChild(input);
            newRow.appendChild(cell);
        });

        const actionsCell = document.createElement('td');
        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'btn btn-sm btn-danger';
        deleteButton.innerHTML = '<i class="fa-solid fa-trash"></i>';
        deleteButton.onclick = function() {
            newRow.remove();
        };
        actionsCell.appendChild(deleteButton);
        newRow.appendChild(actionsCell);

        tableBody.appendChild(newRow);
    }
});
