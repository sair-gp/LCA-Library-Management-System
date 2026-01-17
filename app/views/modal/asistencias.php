 <!-- Modal -->
 <div class="modal fade" id="asistenciasModal" tabindex="-1" aria-labelledby="asistenciasModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="asistenciasModalLabel">Registro de Asistencias <span id="asistenciasModalLabelSpan"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Origen</th>
                                    <th>Descripción</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody id="asistenciasTBody">
                            
                            </tbody>
                        </table>
                    
                        <!--div class="alert alert-warning text-center">No se encontraron asistencias para este visitante.</div-->
                   
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
