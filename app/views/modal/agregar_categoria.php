<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="agregarCategoria" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Registrar Categoria</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form action="app/controller/categorias/c_categorias.php" method="POST">

                    <div class="col-12">
                        <label for="cota">Nombre de la categoria</label>
                        <input class="form-control" type="text" name="nombreCategoria" placeholder="Categoria">

                        <input type="hidden" name="accion" value="agregar">
                    </div>




                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Agregar</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>

            </div>


            </form>
        </div>
    </div>
</div>