<?php
$registrosVariable = 10;

$consultaCount = "SELECT COUNT(*) AS total FROM libros where delete_at = 1;";

$consultaPaginacion = "SELECT libros.isbn, libros.titulo, GROUP_CONCAT(DISTINCT CONCAT(autores.nombre, ' ', autores.apellido) SEPARATOR ', ') AS autores, libros.anio, editorial.nombre AS editorialN, libros.edicion, GROUP_CONCAT(DISTINCT categorias.nombre SEPARATOR ', ') AS categorias, (SELECT COUNT(*) FROM ejemplares WHERE ejemplares.isbn_copia = libros.isbn) AS cantidad_ejemplares FROM libros JOIN libro_autores ON libros.isbn = libro_autores.isbn_libro JOIN autores ON libro_autores.id_autor = autores.id JOIN editorial ON libros.editorial = editorial.id JOIN libro_categoria ON libros.isbn = libro_categoria.isbn_libro JOIN categorias ON libro_categoria.id_categoria = categorias.id WHERE libros.delete_at = 1 GROUP BY libros.isbn ORDER BY libros.isbn LIMIT ?, ?";

include "app/controller/c_paginacion.php";
include "modal/retornar.php";
?>


<div class="vista-tabla">

    <div class="tabla-header">

        <div class="header-titulo">
            <h2><img style="height: 5vh;" src="public/img/reciclaje.gif" alt="libros">Desincorporados</h2>
            <p></p>
        </div>

        <div class="header-herramientas">
            <div class="busqueda">
                <input type="text" id="inputTermino" class="input-busqueda" placeholder="Buscar libros...">
                <i class="bi bi-search"></i>
            </div>

            <button type="button">
                <img style="height: 5vh;" src="public/img/icon_pdf.gif" alt="generar PDF"></button>

        </div>


    </div>

    <div class="tabla-contenedor">
        <table class="tabla-general table-sortable">
            <thead>
                <tr>
                    <th id="isbnTh" class="th-sort-asc">ISBN</th>
                    <th class="flecha-arriba">TITULO</th>
                    <th>AUTOR</th>
                    <th>AÑO</th>
                    <th>EDITORIAL</th>
                    <th>EDICION</th>
                    <th>CATEGORIA</th>
                    <th>ACCION</th>
                </tr>
            </thead>



            <tbody id="tablaPapelera">
                <?php
                // Verificamos si la sesión existe y si contiene los datos
                if (isset($resultado)) {
                    $result = $resultado;
                ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td id="isbn" class="notEditable"><?php echo $row['isbn']; ?></td>
                            <td id="tituloTd" class="editable"><?php echo $row['titulo']; ?></td>
                            <td id="nombreAutorTd" class="notEditable"><?php echo $row['nombre'] . " " . $row['apellido']; ?>
                            </td>
                            <td id="añoTd" class="editable"><?php echo $row['anio']; ?></td>
                            <td id="editorialTd" class="notEditable"><?php echo $row['editorialN']; ?></td>
                            <td id="edicionTd" class="editable"><?php echo $row['edicion']; ?></td>
                            <td id="categoriaTd" class="notEditable"><?php echo $row['categoria']; ?></td>
                            <td>
                                <button type="button" class="btn btn-success modalbtnRetornar" data-bs-toggle="modal"
                                    data-bs-target="#retornarEjemplar">
                                    <i class=" bi bi-recycle"></i>
                                </button>
                            </td>



                        </tr>
                    <?php endwhile; ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="9">No se encontraron registros.</td>
                    </tr>
                <?php } ?>
            </tbody>



        </table>
    </div>

    <div class="paginacion-contenedor">
        <div class="col-sm-5">
            <?php
            echo "<p> Total de registros: ($total_registros)</p>";
            ?>
        </div>

        <!-- Paginación a la derecha -->
        <?php
        echo $paginacion->generarPaginacion($pagina_actual, $total_paginas);
        ?>

    </div>

</div>


<script>
    function retornarLibro(isbn) {

    }

    $(document).on('click', '.modalbtnRetornar', function() {
        $tr = $(this).closest('tr');

        var datos = $tr.children("td").map(function() {
            return $(this).text();
        });

        // Asignar el valor al input con id 'inputISBN'
        $('#isbnRetornar').val(datos[0]);



    });
</script>