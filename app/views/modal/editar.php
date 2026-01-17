<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="editar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form action="app/includes/functions.php" method="POST">

                    <div class="form-floating mb-3">

                        <input type="hidden" name="isbn" id="isbnEditar" class="form-control">
                        <label for="autor" class="floatingInput">ISBN</label>

                    </div>

                    <div class="form-floating mb-3">

                        <input type="text" name="titulo" id="tituloEditar" class="form-control">
                        <label for="autor" class="floatingInput">Titulo</label>

                    </div>

                    <div class="form-floating mb-3">
                        <select name="autor">
                            <option value="0">Seleccionar Autor</option>
                            <?php
                            $tabla = "autores";
                            $columna1 = "nombre";
                            $c2 = "apellido";
                            $select->select_dinamico($tabla, $conexion, $columna1, $c2);

                            ?>
                        </select>

                    </div>

                    <div class="form-floating mb-3">

                        <input type="text" name="anio" id="añoEditar" class="form-control">
                        <label for="autor" class="floatingInput">Año</label>

                    </div>

                    <div class="form-floating mb-3">
                        <select name="editorial">
                            <option value="0">Seleccionar Editorial</option>
                            <?php
                            $tabla = "editorial";
                            $columna1 = "nombre";
                            // $c2 = "";
                            $select->select_dinamico($tabla, $conexion, $columna1, "");

                            ?>
                        </select>

                    </div>

                    <div class="form-floating mb-3">

                        <input type="text" name="edicion" id="edicionEditar" class="form-control">
                        <label for="autor" class="floatingInput">Edicion</label>

                    </div>


                    <div class="form-floating mb-3">
                        <select name="categoria">
                            <option value="0">Seleccionar Categoria</option>
                            <?php
                            $tabla = "categorias";
                            $columna1 = "nombre";
                            // $c2 = "";
                            $select->select_dinamico($tabla, $conexion, $columna1);

                            ?>
                        </select>

                    </div>


                    <input type="hidden" name="accion" value="editar">


                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Editar</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    </div>

            </div>


            </form>
        </div>
    </div>
</div>