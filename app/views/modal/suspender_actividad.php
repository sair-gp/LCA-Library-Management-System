<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="suspender" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">SUSPENCION DE ACTIVIDAD</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form action="app/controller/reg_actividades/c_suspender_actividad.php" method="POST">
                    <h2>¿Estas seguro que deseas suspender esta actividad?</h2>
                    <div class="col-12">
                        <input type="hidden" name="idSuspender" id="idActividad" class="form-control">
                    </div>
                    <input type="hidden" name="accion" value="suspender">


                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Suspender</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>

            </div>


            </form>
        </div>
    </div>
</div>