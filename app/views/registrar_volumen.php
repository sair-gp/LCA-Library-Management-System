<?php
include "app/config/database.php";
$conexion = conexion();

$isbnSerie = NULL;
if (isset($_GET["isbn_serie"])){
    
    $consulta = "SELECT l.titulo, l.portada, GROUP_CONCAT(DISTINCT CONCAT(a.nombre) SEPARATOR ', ') AS autores, (SELECT v.numero FROM volumen v WHERE v.isbn_obra = ? ORDER BY CAST(`v`.`numero` AS UNSIGNED) DESC LIMIT 1) AS ultimoVol, (SELECT COUNT(*) FROM volumen v WHERE v.isbn_obra = ?) AS totalVolumenes, l.cota from libros l JOIN libro_autores la ON la.isbn_libro = l.isbn JOIN autores a ON a.id = la.id_autor WHERE l.isbn = ?;";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("sss", $_GET["isbn_serie"], $_GET["isbn_serie"], $_GET["isbn_serie"]);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows > 0){
        $fila = $resultado->fetch_assoc();
        $serie = [
            "titulo" => $fila["titulo"],
            "portada" => $fila["portada"],
            "autores" => $fila["autores"],
            "ultimoVol" => intval($fila["ultimoVol"]),
            "nuevoVol" => intval($fila["ultimoVol"] + 1),
            "totalVolumenes" => intval($fila["totalVolumenes"]),
            "cotaObra" => $fila["cota"]
        ];
        $isbnSerie = $fila["titulo"] == NULL ? NULL : TRUE;
        $nuevoVol = convertirARomano($serie["ultimoVol"] + 1) ?? convertirARomano(1);



    } else {
        $nuevoVol = convertirARomano(1);
       //$isbnSerie = NULL;
        $_GET["isbn_serie"] = NULL;
    }
    
}

function convertirARomano($numero) {
    $valores = array(
        'M'  => 1000,
        'CM' => 900,
        'D'  => 500,
        'CD' => 400,
        'C'  => 100,
        'XC' => 90,
        'L'  => 50,
        'XL' => 40,
        'X'  => 10,
        'IX' => 9,
        'V'  => 5,
        'IV' => 4,
        'I'  => 1
    );
    $resultado = '';
    foreach ($valores as $romano => $valor) {
        $repeticiones = intval($numero / $valor);
        $resultado .= str_repeat($romano, $repeticiones);
        $numero = $numero % $valor;
    }
    return $resultado;
}

/*<p><?= $serie["titulo"] ?? "" ?> <img src="<?= $serie["portada"] ?>" alt="portada de serie" style="width: 50px; height: 50px; border-radius: 10px;"></p>*/

?>
<br><br>
<section class="registro-volumen-container">
    <aside class="registro-volumen-info">
    
    <?php
    if ($isbnSerie != NULL){
        //var_dump($isbnSerie);
        echo '<h4><strong>Información de la Serie</strong></h4>
        <p><strong>Título de la Serie:</strong> '. $serie["titulo"] .'</p>
        <p><strong>Autor:</strong> '. $serie["autores"] .'</p>
        <p><strong>Total de Volúmenes:</strong> '. $serie["totalVolumenes"] .'</p>';
    }
    ?>
        <h3>¿Cómo registrar un volumen?</h3>
        <p>Completa los campos con la información del volumen, incluyendo su numeración, ISBN y portada.</p>
        <!--p>Puedes añadir múltiples volúmenes usando el botón "Añadir Volumen".</p-->
        <h3>Consejos</h3>
        <ul>
            <li>Verifica que el ISBN sea correcto antes de registrar.</li>
            <li>Las imágenes de portada deben ser claras y de buena calidad.</li>
        </ul>
    </aside>
    <section class="registro-volumen">
        <h2>Registro de Volúmenes</h2>
        <p>Administra los volúmenes de tus colecciones de manera sencilla y rápida.</p>
        <input type="hidden" name="" id="ultimoNumeroVolumen" value="<?= $serie["nuevoVol"] ?? 1; ?>">
        <form id="formulario" action="app/controller/volumenes/c_registrar_volumen.php" METHOD="POST" class="formulario form-volumenes" enctype="multipart/form-data">
            <div id="contenedor-volumenes">
            
                <div class="volumen" data-index="1">
                    <h3>Nuevo Volumen</h3>
                    <small>Los campos marcados con * son obligatorios.</small>
                    <div class="form-group">
                        <label>Título del volumen*:</label>
                        <input type="text" name="titulo_vol_serie[]" required>
                    </div>
                    <div class="form-group">
                        <label>Numeración dentro de la serie*:</label>
                        <input type="text" name="numeracion_serie[]" required>
                    </div>
                    <div class="form-group">
                        <label>ISBN del volumen*:</label>
                        <input type="text" class="isbnRegistro" name="isbn_serie[]" required>
                    </div>
    
                        <div class="form-group">
                        <label>ISBN de la obra completa*:</label>
                        <input type="text" class="isbnRegistro" name="isbn_completo[]" value=<?= $isbnSerie === NULL ? '""' : '"' . $_GET["isbn_serie"] . '" readonly' ?> required>
                        </div>

                        <div class="form-group">
                        <label>Extensión*:</label>
                        <small>Número de páginas o duración.</small>
                        <input type="text" name="extension[]" required>
                        </div>

                        <div class="form-group">
                        <label>Año de publicación:</label>
                        <input type="text" name="anio_publicacion[]" value="">
                        </div>

                    <div class="form-group">
                        <label>Portada:</label>
                        <input type="file" name="portada_volumen[]" accept="image/*" onchange="previewImage(event, this)">
                        <img src="public/img/libros/default.jpg" alt="Portada" class="preview-img">
                    </div>
                </div>
            </div>
            <!--button type="button" id="agregar-volumen" class="btn btn-outline-danger btn-sm"><i class="fas fa-plus"></i> Añadir Volumen</button-->
            <button type="submit" id="submitBtnRegConIsbn" class="btn btn-outline-success btn-sm"><i class="fas fa-check"></i> Registrar Volúmenes</button>
        </form>
    </section>
</section>
<style>
    .registro-volumen-container {
        display: flex;
        gap: 30px;
        max-width: 1200px;
        margin: auto;
        padding: 20px;
    }
    .registro-volumen-info {
        width: 30%;
        background: #e3f2fd;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    .registro-volumen {
        width: 80%;
        background: #ffffff;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    #contenedor-volumenes {
        max-height: 450px;
        overflow-y: auto;
        padding: 10px;
        width: 100%;
    }
    .volumen {
        background: #f1faff;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        width: 100%;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 10px;
    }

    .form-volumenes {
        width: 100%;
    }

    .preview-img {
        width: 100px;
        height: auto;
        margin-top: 10px;
        border-radius: 4px;
    }
</style>
<script>

    let ultimoNumeroVolumen = parseInt(document.getElementById("ultimoNumeroVolumen").value);
    document.getElementById('agregar-volumen').addEventListener('click', function() {
        let contenedor = document.getElementById('contenedor-volumenes');
        let volIndex = ultimoNumeroVolumen + 1;
        let nuevoVolumen = document.querySelector('.volumen').cloneNode(true);
        nuevoVolumen.dataset.index = volIndex;
        //nuevoVolumen.querySelector('h3').innerText = `Volumen ${toRoman(volIndex)}`;

        nuevoVolumen.querySelectorAll('input').forEach(input => {
            input.value = input.name === "isbn_completo[]" ? input.value : "";
        }
        );

        nuevoVolumen.querySelector('.preview-img').src = 'public/img/libros/default.jpg';
        contenedor.appendChild(nuevoVolumen);
        contenedor.scrollTop = contenedor.scrollHeight;
        ultimoNumeroVolumen++;
    });

function toRoman(num) {
    const romanNumerals = [
        [1000, "M"], [900, "CM"], [500, "D"], [400, "CD"],
        [100, "C"], [90, "XC"], [50, "L"], [40, "XL"],
        [10, "X"], [9, "IX"], [5, "V"], [4, "IV"], [1, "I"]
    ];
    let result = "";
    for (let [value, numeral] of romanNumerals) {
        while (num >= value) {
            result += numeral;
            num -= value;
        }
    }
    return result;
}


    
    function previewImage(event, input) {
        let img = input.nextElementSibling;
        let reader = new FileReader();
        reader.onload = function() {
            img.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }


</script>
