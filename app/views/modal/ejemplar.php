<div class="modal" tabindex="-1" id="modalEjemplares">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">EJEMPLARES</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <input type="hidden" name="isbnEjemplar" id="isbnEjemplar" disabled>

                <div id="ejemplarTabla">
                    <table class="miyazaki" id="ejemplaresTable">
                        <thead>
                            <tr>
                                <th>TITULO </th>
                                <th>COTA </th>
                                <th>ESTADO </th>
                            </tr>
                        </thead>
                        <tbody id="contenidoEjemplares">
                            <!-- Aquí puedes agregar filas de la tabla si es necesario -->
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>

<script>

    function cargarEjemplares(isbn) {
        let content = document.getElementById('contenidoEjemplares');
        fetch('public/js/ajax/ejemplares.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/jason'
            },
            body: JSON.stringify({ currentISBN: isbn })
        }).then(response => {
            if (!response.ok) {
                throw new Error('No respondio la red');
            }
            return response.json();
        }).then(data => {
            console.log('El servidor respondio', data);
            content.innerHTML = data;

        }).catch(error => {
            console.error('Error', error);
        });

    }

</script>