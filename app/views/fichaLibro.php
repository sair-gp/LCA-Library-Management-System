<?php

//echo "<br><br><br>";
include_once "app/config/database.php";

$conexion = conexion();

require_once "app/controller/libros/c_setear_ficha_obra.php";

include_once "modal/prestar.php";

if ($rol !== "Bibliotecario"){
  include_once "modal/agregar_ejemplar.php";
  include_once "modal/desincorporarEjemplar.php";
}



?>


<div id="divPrincipalDetalleLibro">

  <div id="divBodyDetalleLibro">
    <div class="book-details-container">
      <div class="book-header">
        <div class="book-image">
          <img src="<?php echo $bookData["image"]; ?>" alt="Portada del libro" id="book-image" />
          <div class='vol-p' style='
            margin-top: 10px;
            margin-left: 10px;'>
            
            <?php
          if ($bookData['es_obra_completa'] === 1 && $volumenes[0]['copias'] >= 1){
            echo "
            <p><strong>Copias: </strong><span id='vol-copias'>".$volumenes[0]['copias']."</span></p>
            <p><strong>En circulación: </strong><span id='vol-circulacion'>".$volumenes[0]['circulacion']."</span></p>
            <p><strong>Disponibles: </strong><span id='vol-disponibes'>".$volumenes[0]['disponibles']."</span></p>
            ";
          } 

          ?>
          
          </div>
          
          
        </div>
        <div class="book-info">
          <h1 id="book-title"><?php echo $bookData["title"];  ?></h1>
          <p class="author">
            <strong>Por:</strong> <span id="book-author"><?php echo $bookData["author"]; ?></span>
          </p>
          <p>
            <strong>Editorial:</strong>
            <span id="book-publisher"><?php echo $bookData["publisher"]; ?></span>
          </p>
          <p>
            <strong>Edición:</strong> <span id="book-edition"><?php echo $bookData["edition"]; ?></span>
          </p>
          <!--p>
            <strong>Descripción:</strong>
            <span id="book-description"><?php //echo $bookData["description"]; ?></span>
          </p-->
          <p>
            <strong>ISBN:</strong> <span id="book-isbn"><?php echo $bookData["isbn"]; ?></span>
          </p>
          <p>
            <strong>Categorías:</strong>
            <span id="book-subjects"><?php echo $bookData["subjects"]; ?></span>
          </p>
          <p>
            <strong>Clasificación DDC:</strong>
            <span id="book-ddc"><?php echo $bookData["ddc"]; ?></span>
          </p>
        </div>

      </div>




      <div id="botonesAcciones" style="<?php echo !empty($volumenes) && $bookData["es_obra_completa"] === 0 ? "margin-left: 87%" : "margin-left: 69%" ?>; margin-top: 35px;">
        <?php

          //para agregar volumenes, la obra debe ser seriada y solo puede hacerlo el admin
        if ($bookData["es_obra_completa"] === 0 && $rol === "Admin"){
          echo '<button id="btnAction" type="button" onclick="window.location.href=' . "'index.php?vista=registrar_volumen&isbn_serie=". $bookData['isbn'] ."'" . '" style="display: inline-block;">Agregar Volumen</button>';
        }
        
        ?>
        
        <?php if (empty($volumenes) && $bookData["es_obra_completa"] === 1 && $bookData["disponibles"] >= 1) {
          echo <<<HTML
              <button type="button" class="btn btn-danger" style="display: inline-block;" data-bs-toggle="modal" data-bs-target="#registrarPrestamo">Prestar</button>
            HTML;
        }
        
        if ($bookData["es_obra_completa"] === 1 && $rol !== "Bibliotecario"){
          echo <<<HTML
          <button type="button" id="modalAgregarEjemplarObraCompleta" class="btn" style="display: inline-block; background-color: dodgerblue; color: white;" data-bs-toggle="modal" data-bs-target="#nuevoEjemplar">Nuevo ejemplar</button>
          HTML;
          if($bookData["copias"] > 0){
            
            echo <<<HTML
            <button id='desincoporarEjemplarVolUnico' class="btn btn" style="display: inline-block; background-color: red; color: white; margin-left: 5px;" data-bs-toggle="modal" data-bs-target="#desincorporarModal">Desincorporar ejemplar.</button>
            HTML;
            
          } 
         
        }
        
        ?>

      </div>

      <div class="holdings-section">
        <?php
        if ($bookData['es_obra_completa'] === 1 && $volumenes[0]['copias'] === 0) {
          echo "<p id='noHayCopiasP'><strong>No hay copias registradas de este libro</strong></p>";
        }

        if ($bookData["holdings"] > 0){
          echo "<h2>Prestados (<span id='holdings-count'>". $bookData["holdings"] ."</span>)</h2>";
        }
        ?>
        
        <?php if ($resultado->num_rows > 0 && $bookData['es_obra_completa'] === 0) {
          echo <<<HTML
          <div class="vol-table-container">
          <table class="volumes-table">
          <thead>
            <tr>
              <th>Portada</th>
              <th>Título de volumen</th>
              <th>Volumen</th>
              <!--th>Ubicación</th-->
              <!--th>Clasificación DDC</th-->
              <th>Copias</th>
              <th>En Circulacion</th>
              <th>Disponibles</th>
              <th {$displayNone}>Nueva copia</th>
              <th {$displayNone}>Desincorporar</th>
              <th>Detalles</th>
            </tr>
          </thead>
          <tbody>
          HTML;
          foreach ($volumenes as $vol) {
            echo <<<HTML
        <tr>
        <td class="isbnVolReg" style="display: none;">{$vol['isbn_vol']}</td>
        <td class="isbnObraReg" style="display: none;">{$vol['isbn_obra']}</td>
        <td class="idVolReg" style="display: none;">{$vol['id']}</td>
        <td class="cotaVolReg" style="display: none;">{$bookData['ddc']}</td>
        <td><img src="{$vol['portada']}" alt="Portada" style="max-width: 100px;"></td>
        <td>{$vol['nombre']}</td>
        <td>{$vol['numero']}</td>
        <!--td>{$vol['localizacion']}</td-->
        <!--td>{$bookData['ddc']}</td-->
        <td class="copiasVolTD">{$vol['copias']} </td>
        <td class="copiasEnCirculacionTD">{$vol['circulacion']}</td>
        <td class="disponiblesVolTD">{$vol['disponibles']}</td>

        <td {$displayNone}><center><button type="button" id="modalAgregarEjemplar" class="btn btn" style="display: inline-block; background-color: #0275d8; color: white;" data-bs-toggle="modal" data-bs-target="#nuevoEjemplar"><i class="bi bi-plus"></button></center></td>

        <td {$displayNone}><center><button id="desModalBtn" class="btn btn" style="display: inline-block; background-color: red; color: white;" data-bs-toggle="modal" data-bs-target="#desincorporarModal"><i class="bi bi-dash"></button></center></td>

        <td><a href="index.php?vista=detallesVolumen&volumeId={$vol['id']}" style="background-color: gray;" class="details-btn">Ver detalles</a></td>
        <td class="cotasVolReg" style="display: none;">{$vol['cotasVol']}</td>

    </tr>
    HTML;
          }
          echo <<<HTML
          </tbody>
        </table>
        </div>
        HTML;
        }
        ?>
      </div>

      <?php if (empty($volumenes) && $bookData["es_obra_completa"] === 1) {
        $dspNone = $bookData["ubicacion"] != NULL ? "" : "style='display: none;'";
        echo '
        <div>
        <p '. $dspNone .'><strong>Localización actual:</strong>'. $bookData['ubicacion'] .'</p>
        <p><strong>Copias:</strong> '.$bookData['copias'] .'</p>
        <p><strong>Disponibles:</strong> '.$bookData['disponibles'].'</p>
        </div>
        </div>
        ';
      } ?>

    </div>


  </div>

</div>

















<link rel="stylesheet" href="public/css/fichaLibro.css">

<script>
  $(document).on('click', '#modalAgregarEjemplar', function() {
        $tr = $(this).closest('tr');

        var datos = $tr.children("td").map(function() {
            return $(this).text();
        });

        // Asignar el valor al input con id 'inputISBN'
        $('#idEjemplarAgregar').val(datos[2]);
        $('#isbnObraCompleta').val(datos[1]);
        $('#cotaEjemplar').val(datos[3]);

        console.log(datos[1] + " " + datos[0])

    });

    $(document).on('click', '#desModalBtn', function() {
        $tr = $(this).closest('tr');

        var datos = $tr.children("td").map(function() {
            return $(this).text();
        });

        // Asignar el valor al input con la cota
        $('#cotaVolumen').val(datos[13]);
        $("#isbnDes").val(datos[0]);

      

    });

    $(document).on('click', '#desincoporarEjemplarVolUnico', function() {

      $("#cotaVolumen").val(<?= $volumenes[0]['cotasVol'] ?>);

    });



    $(document).on('click', '#modalAgregarEjemplarObraCompleta', function() {
        
        // Asignar el valor al input con id 'inputISBN'
        $('#idEjemplarAgregar').val('<?= $volumenes[0]["id"] ?? "1" ?>');
        $('#isbnObraCompleta').val(document.getElementById("book-isbn").textContent);
        $('#cotaEjemplar').val(document.getElementById("book-ddc").textContent);

        //console.log()

    });


document.getElementById("agregarEjemplarForm").addEventListener("submit", function(event) {
    event.preventDefault();
    let formData = new FormData(this);

    fetch("public/js/ajax/agregarEjemplar.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            // Cerrar el modal
           // const modal = bootstrap.Modal.getInstance(document.getElementById('nuevoEjemplar'));
           // modal.hide();
            
            // Obtener la URL base sin parámetros de hash
            let currentUrl = window.location.href.split('#')[0];
            
            // Eliminar parámetros de toast existentes si los hay
            currentUrl = currentUrl.replace(/[&?]toast=[^&]*/, '').replace(/[&?]mensaje=[^&]*/, '');
            
            // Determinar el carácter a usar (? o &) según si ya hay parámetros
            const separator = currentUrl.includes('?') ? '&' : '?';
            
            // Construir nueva URL con parámetros de toast
            const newUrl = `${currentUrl}${separator}toast=success&mensaje=Ejemplares registrados correctamente`;
            
            // Recargar la página con los nuevos parámetros
            window.location.href = newUrl;
            
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error en la solicitud:", error);
        alert("Error al procesar la solicitud");
    });
});

</script>