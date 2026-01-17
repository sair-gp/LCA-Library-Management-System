<?php
$registrosVariable = 10;

// Consulta para contar registros totales
$consultaCount = "SELECT COUNT(*) AS total FROM historial;";

// Consulta para obtener los registros del historial con JOIN a la tabla acciones
$consultaPaginacion = "
    SELECT h.id, h.fecha, a.descripcion AS accion, h.detalles, CONCAT(u.nombre, ' ' ,u.apellido) as nombre
    FROM historial h
    JOIN acciones a ON h.accion_id = a.id
    JOIN usuarios as u ON u.cedula = h.cedula_responsable ORDER BY id DESC
    LIMIT ?, ?
";

include "app/controller/c_paginacion.php";
include "modal/reportePdf.php";
?>


<div class="vista-tabla">

  <div class="tabla-header">
    <div class="header-titulo">
      <h2>Historial</h2>
      <p>Visualiza el historial de manera rápida y efectiva</p>
    </div>

    <div class="header-herramientas">
      <div class="busqueda">
        <input type="text" id="inputTermino" class="input-busqueda" placeholder="Buscar acción...">
        <i class="bi bi-search"></i>
      </div>
      <button type="button" data-bs-toggle="modal" data-bs-target="#reporteModal">
        <img style="height: 5vh;" src="public/img/icon_pdf.gif" alt="generar PDF">
      </button>
    </div>
  </div>


  <div class="tabla-contenedor">

    <table class="tabla-general table-sortable">
      <thead>
        <tr>
          <th style="display: none;" class="th-sort-asc">ID</th>
          <th>Fecha</th>
          <th>Acción</th>
          <th>Detalles</th>
          <th>Responsable</th>
        </tr>
      </thead>
      <tbody id="tablaHistorial">
        <?php
        // Verificamos si la sesión existe y contiene los datos
        if (isset($resultado)) {
          $result = $resultado;
          while ($row = mysqli_fetch_assoc($result)):
        ?>
            <tr>
              <td style="display: none;"><?php echo $row['id']; ?></td>
              <td><?php echo $row['fecha']; ?></td>
              <td><?php echo $row['accion']; ?></td>
              <td style="font-size: 15.51px;"><?php echo $row['detalles']; ?></td>
              <td><?php echo $row['nombre']; ?></td>
            </tr>
          <?php
          endwhile;
        } else {
          ?>
          <tr>
            <td colspan="4">No se encontraron registros.</td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>


  <div class="paginacion-contenedor">

    <div class="col-sm-5">
      <?php
      echo "<p> Total de registros: ($total_registros)</p>";
      // echo $inicio . " " . $registros_por_pagina . " " . $total_paginas;
      ?>
    </div>


    <?php echo $paginacion->generarPaginacion($pagina_actual, $total_paginas); ?>
  </div>
</div>