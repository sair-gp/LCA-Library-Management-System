<style>
    /* Estilo para las tablas ordenables */
    #resultModal .tabla-modal {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        background-color: #fff;
        color: #333;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    /* Encabezado de la tabla */
    #resultModal .tabla-modal thead {
        background-color: green;
        color: #fff;
        font-weight: 600;
    }

    #resultModal .tabla-modal th {
        padding: 12px 15px;
        text-align: center;
        vertical-align: middle;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    #resultModal .tabla-modal th:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
    }

    /* Ordenación de las columnas */
    #resultModal .tabla-modal th.sortable::after {
        content: " ▼";
        font-size: 12px;
        padding-left: 5px;
        color: #ddd;
    }

    #resultModal .tabla-modal th.sortable.sort-asc::after {
        content: " ▲";
        color: #ff8c00;
    }

    #resultModal .tabla-modal th.sort-desc::after {
        content: " ▼";
        color: #ff8c00;
    }

    /* Estilos de las filas y celdas */
    #resultModal .tabla-modal td {
        padding: 12px 15px;
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid #ddd;
    }

    #resultModal .tabla-modal tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    #resultModal .tabla-modal tr:hover {
        background-color: #e2e2e2;
    }

    #resultModal .tabla-modal td:hover {
        background-color: #f5f5f5;
    }

    /* Estilos para el modal */
    #resultModal .modal-body {
        max-height: 400px;
        overflow-y: auto;
        padding: 0;
    }

    /* Animación de la tabla */
    @keyframes fadeInTable {
        0% {
            opacity: 0;
            transform: translateY(30px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #resultModal .tabla-modal {
        animation: fadeInTable 0.6s ease-in-out;
    }

    /* Estilos para botones en la tabla */
    #resultModal .tabla-modal .action-buttons button {
        background-color: #007bff;
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    #resultModal .tabla-modal .action-buttons button:hover {
        background-color: #0056b3;
    }
</style>


<!-- Modal -->
<div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resultModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBodyTable" style="max-height: 400px; overflow-y: auto;">
                <!-- Los resultados se cargarán aquí -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Función para mostrar datos en el modal basado en una consulta SQL.
     * @param {string} query - La consulta SQL a ejecutar.
     */
    function showDataModal(query) {
        // Verificar si la consulta necesita ser deserializada
        let queryProcessed;
        try {
            // Intentar deserializar si es una cadena JSON válida
            queryProcessed = JSON.parse(query);
        } catch (e) {
            // Si falla, usar el valor original
            queryProcessed = query;
        }

        // Mostrar un mensaje de carga mientras se obtienen los datos
        $('#modalBodyTable').html('<p>Cargando datos...</p>');

        // Crear una instancia del modal y abrirlo
        const modal = new bootstrap.Modal(document.getElementById('resultModal'));
        modal.show();

        // Hacer la solicitud AJAX para obtener los datos
        $.post('../../../s12f/public/js/ajax/fetch_record.php', {
            query: queryProcessed
        }, function(data) {
            // Actualizar el contenido del modal con los datos recibidos
            $('#modalBodyTable').html(data);
        }).fail(function() {
            // Manejar errores en la solicitud
            $('#modalBodyTable').html('<p>Error al obtener los datos. Por favor, intenta nuevamente.</p>');
        });
    }
    const resultModal = document.getElementById("resultModal");
    resultModal.addEventListener('hidden.bs.modal', () => {
        // Limpia el contenido dinámico del modal
        const modalBody = document.getElementById('modalBodyTable');
        modalBody.innerHTML = '';
    });


    //showDataModal("SELECT * FROM prestamos where id = 7");
</script>


<script>
    // Ordenación de columnas
    document.addEventListener("DOMContentLoaded", function() {
        const tables = document.querySelectorAll('.table-sortable');

        tables.forEach(function(table) {
            const headers = table.querySelectorAll('th.sortable');
            headers.forEach(function(header, index) {
                header.addEventListener('click', function() {
                    const rows = Array.from(table.querySelectorAll('tbody tr'));
                    const isAscending = header.classList.contains('sort-asc');
                    const sortedRows = rows.sort((a, b) => {
                        const cellA = a.children[index].innerText.trim();
                        const cellB = b.children[index].innerText.trim();

                        const compare = (cellA, cellB) => {
                            if (isNaN(cellA) || isNaN(cellB)) {
                                return cellA.localeCompare(cellB);
                            } else {
                                return parseFloat(cellA) - parseFloat(cellB);
                            }
                        };

                        return isAscending ? compare(cellB, cellA) : compare(cellA, cellB);
                    });

                    table.querySelector('tbody').append(...sortedRows);
                    headers.forEach(h => h.classList.remove('sort-asc', 'sort-desc'));
                    if (isAscending) {
                        header.classList.remove('sort-asc');
                        header.classList.add('sort-desc');
                    } else {
                        header.classList.remove('sort-desc');
                        header.classList.add('sort-asc');
                    }
                });
            });
        });
    });
</script>