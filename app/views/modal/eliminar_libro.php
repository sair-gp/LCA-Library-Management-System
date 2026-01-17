<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="eliminar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form action="app/controller/libros/c_eliminar_libro.php" method="POST">
                    <h2>¿Estas seguro que deseas eliminar este registro?</h2>
                    <div class="col-12">
                        <input type="hidden" name="isbnEliminar" id="isbnEliminar" class="form-control">
                        <input type="hidden" name="tituloEliminar" id="tituloEliminar" class="form-control">
                        <input type="hidden" name="responsableEliminar" id="responsableEliminar" class="form-control">

                    </div>
                    <input type="hidden" name="accion" value="eliminar">


                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>

            </div>


            </form>
        </div>
    </div>
</div>