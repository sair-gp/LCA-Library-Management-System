<?php
include "app/config/database.php";
$conexion = conexion();
                $query = "SELECT * FROM regla_de_circulacion";
                $resultado = mysqli_query($conexion, $query);

                if (mysqli_num_rows($resultado) > 0) {
                    $fila = mysqli_fetch_assoc($resultado);

                    
     $periodoPrestamo = $fila["periodo_prestamo"];
    $unidades = $fila["unidades"];
    $periodoRenovaciones = $fila["periodo_renovaciones"];
    $renovacionesPermitidas = $fila["renovaciones_permitidas"];
    $peticionesPermitidas = $fila["peticiones_permitidas"];
    $peticionesDiarias = $fila["peticiones_diarias"];
    $peticionesPorRegistro = $fila["peticiones_por_registro"];
    $dolarBCV = $fila["dolarBCV"];
    $montoRetraso = $fila["monto_por_dia_retraso"];
    $montoDano = $fila["monto_por_danio"];
    $montoPerdida = $fila["monto_por_perdida_material"]; 

                    if (!isset($_SESSION["periodo_prestamo"], $_SESSION["unidades"], $_SESSION["periodo_renovaciones"], $_SESSION["renovaciones_permitidas"], $_SESSION["peticiones_permitidas"], $_SESSION["peticiones_diarias"], $_SESSION["peticiones_por_registro"], $_SESSION["dolarBCV"], $_SESSION["monto_por_perdida_material"], $_SESSION["monto_por_danio"], $_SESSION["monto_por_dia_retraso"])){
                        $_SESSION["periodo_prestamo"] = $periodoPrestamo;
                        $_SESSION["unidades"] = $unidades; 
                        $_SESSION["periodo_renovaciones"] = $periodoRenovaciones;
                        $_SESSION["renovaciones_permitidas"] = $renovacionesPermitidas; 
                        $_SESSION["peticiones_permitidas"] = $peticionesPermitidas; 
                        $_SESSION["peticiones_diarias"] = $peticionesDiarias; 
                        $_SESSION["peticiones_por_registro"] = $peticionesPorRegistro;
                        $_SESSION["dolarBCV"] = $dolarBCV;
                        $_SESSION["monto_por_perdida_material"] = $montoPerdida;
                        $_SESSION["monto_por_danio"] = $montoDano;
                        $_SESSION["monto_por_dia_retraso"] = $montoRetraso;
                    
                    }
                }

                $query = "SELECT * FROM datos_biblioteca";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) { // Check if the query was successful
        if (mysqli_num_rows($resultado) > 0) {
            $fila = $resultado->fetch_assoc();

            $_SESSION["datos_biblioteca"] = [
                "nombre" => $fila["nombre"] ?? "",
                "abreviacion" => $fila["abreviacion"] ?? "",
                "logo" => $fila["logo"] ?? "",
                "direccion" => $fila["direccion"] ?? "",
                "telefono" => $fila["telefono"] ?? "",
                "correo" => $fila["correo"] ?? ""
            ];
        }
    }
?>
<br><br>
<div class="config-container">
    <h2>Configuración General</h2>
    <div class="config-tabs">
        <button class="tab-btn active" onclick="openTab(event, 'general')">Datos Generales</button>
        <button class="tab-btn" onclick="openTab(event, 'circulacion')">Regla de Circulación</button>
        <button class="tab-btn" onclick="openTab(event, 'respaldo')">Respaldo</button>
    </div>
    
    <div id="general" class="config-tab-content active">
        <form id="configGeneralForm" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombreBiblioteca">Nombre de la Biblioteca:</label>
                <input type="text" id="nombreBiblioteca" name="nombreBiblioteca" class="form-control" value='<?= $_SESSION["datos_biblioteca"]["nombre"] ?? "" ?>'>
                <label for="abreviacionBiblioteca">Abreviación del nombre de la Biblioteca:</label>
                <input type="text" id="abreviacionBiblioteca" name="abreviacionBiblioteca" class="form-control" value='<?= $_SESSION["datos_biblioteca"]["abreviacion"] ?? "" ?>'>
            </div>

            
            
            <div class="form-group">
                <label for="logoBiblioteca">Logo de la Biblioteca:</label>
                <input type="file" id="logoBiblioteca" name="logoBiblioteca" class="form-control" accept="image/*" onchange="previewImage(event, 'preview')">
                <img id="preview" src="<?= $_SESSION["datos_biblioteca"]["logo"] ?? "public/img/libros/default.jpg" ?>" alt="Portada" class="preview-img">
            </div>

            <div class="form-group">
                <label for="direccionBiblioteca">Dirección:</label>
                <input type="text" id="direccionBiblioteca" name="direccionBiblioteca" class="form-control" value='<?= $_SESSION["datos_biblioteca"]["direccion"] ?? "" ?>'>
            </div>
            
            <div class="form-group">
                <label for="telefonoBiblioteca">Teléfono:</label>
                <input type="text" id="telefonoBiblioteca" name="telefonoBiblioteca" class="form-control" value='<?= $_SESSION["datos_biblioteca"]["telefono"] ?? "" ?>'>
            </div>

            <div class="form-group">
                <label for="emailBiblioteca">Correo Electrónico:</label>
                <input type="email" id="emailBiblioteca" name="emailBiblioteca" class="form-control" value='<?= $_SESSION["datos_biblioteca"]["correo"] ?? "" ?>'>
            </div>
            
            <button type="submit" class="btn-primary">Guardar</button>
        </form>
    </div>
    


    <div id="circulacion" class="config-tab-content">
    <div class="circulacion-container">
        <div class="circulacion-column">
            <h3>Regla de Circulación</h3>
            <?php

echo <<<HTML
<form id="reglaCirculacionForm">
    <div class="form-group">
        <label for="periodoPrestamo">Periodo de préstamo:</label>
        <input type="number" name="periodoPrestamo" id="periodoPrestamo" class="form-control" value="$periodoPrestamo">
    </div>

    <div class="form-group">
        <label for="unidadesPrestamo">Unidad:</label>
HTML;

echo '<select id="unidadesPrestamo" name="unidadesPrestamo" class="form-select">';
echo '<option value="Días"' . ($unidades == "Días" ? ' selected' : '') . '>Días</option>';
echo '<option value="Semanas"' . ($unidades == "Semanas" ? ' selected' : '') . '>Semanas</option>';
echo '<option value="Meses"' . ($unidades == "Meses" ? ' selected' : '') . '>Meses</option>';
echo '</select>';

echo <<<HTML
    </div>

    <div class="form-group">
        <label for="periodoRenovaciones">Periodo de renovación:</label>
        <input type="number" name="periodoRenovaciones" id="periodoRenovaciones" class="form-control" value="$periodoRenovaciones">
    </div>

    <div class="form-group">
        <label for="renovacionesPermitidas">Renovaciones permitidas por usuario:</label>
        <input type="number" name="renovacionesPermitidas" id="renovacionesPermitidas" class="form-control" value="$renovacionesPermitidas">
    </div>

    <div class="form-group">
        <label for="peticionesPermitidas">Peticiones permitidas:</label>
        <input type="number" name="peticionesPermitidas" id="peticionesPermitidas" class="form-control" value="$peticionesPermitidas">
    </div>

    <div class="form-group">
        <label for="peticionesDiarias">Peticiones diarias por usuario:</label>
        <input type="number" name="peticionesDiarias" id="peticionesDiarias" class="form-control" value="$peticionesDiarias">
    </div>

    <div class="form-group">
        <label for="peticionesPorRegistro">Peticiones permitidas por registro bibliográfico:</label>
        <input type="number" name="peticionesPorRegistro" id="peticionesPorRegistro" class="form-control" value="$peticionesPorRegistro">
    </div>

HTML;

        ?>

     
        </div>

        <div class="multas-column">
            <h3>Reglas de Multas</h3>
    
            <?php


// Formatear los montos con 2 decimales
$montoRetrasoFormateado = number_format($montoRetraso, 2, '.', '');
$montoDanoFormateado = number_format($montoDano, 2, '.', '');
$montoPerdidaFormateado = number_format($montoPerdida, 2, '.', '');

// Imprimir el bloque HTML usando echo <<<HTML
echo <<<HTML

<div class="form-group">
<label for="mdolarBCV">Precio del dólar(BCV):</label>
    <div class="input-group">
        <span class="input-group-text">
            <i class="bi bi-cash-stack"></i> <!-- Ícono de reloj a la izquierda -->
        </span>
        <input type="text" name="dolarBCV" id="dolarBCV" class="form-control" 
               value="$dolarBCV"
               pattern="^\d+(\.\d{1,2})?$"
               title="Solo se permiten números y hasta 2 decimales"
               oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')">
        <span class="input-group-text">$</span> <!-- Símbolo monetario a la derecha -->
    </div>
</div>

<div class="form-group">
    <label for="montoRetraso">Monto por día de retraso:</label>
    <div class="input-group">
        <span class="input-group-text">
            <i class="bi bi-clock"></i> <!-- Ícono de reloj a la izquierda -->
        </span>
        <input type="text" name="retraso" id="montoRetraso" class="form-control" 
               value="$montoRetrasoFormateado"
               pattern="^\d+(\.\d{1,2})?$"
               title="Solo se permiten números y hasta 2 decimales"
               oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')">
        <span class="input-group-text">$</span> <!-- Símbolo monetario a la derecha -->
    </div>
</div>

<div class="form-group">
    <label for="montoDano">Monto por daño:</label>
    <div class="input-group">
        <span class="input-group-text">
            <i class="bi bi-tools"></i> <!-- Ícono de herramientas a la izquierda -->
        </span>
        <input type="text" name="dano" id="montoDano" class="form-control" 
               value="$montoDanoFormateado"
               pattern="^\d+(\.\d{1,2})?$"
               title="Solo se permiten números y hasta 2 decimales"
               oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')">
        <span class="input-group-text">$</span> <!-- Símbolo monetario a la derecha -->
    </div>
</div>

<div class="form-group">
    <label for="montoPerdida">Monto por pérdida de material:</label>
    <div class="input-group">
        <span class="input-group-text">
            <i class="bi bi-exclamation-triangle"></i> <!-- Ícono de advertencia a la izquierda -->
        </span>
        <input type="text" name="perdida" id="montoPerdida" class="form-control" 
               value="$montoPerdidaFormateado"
               pattern="^\d+(\.\d{1,2})?$"
               title="Solo se permiten números y hasta 2 decimales"
               oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')">
        <span class="input-group-text">$</span> <!-- Símbolo monetario a la derecha -->
    </div>
</div>

<div class="modal-footer">
    <div id="mensajeError">Por favor, complete todos los campos antes de guardar.</div>
    <button type="submit" class="btn-primary" id="guardarReglaCirculacion" disabled>Guardar</button>

</div>
</form>
HTML;
?>
               
           
        </div>
    </div>
</div>



    
    <div id="respaldo" class="config-tab-content">
    <div class="respaldo-container">
        <h3>Respaldo de Base de Datos</h3>
        <p class="respaldo-description">
            Protege tu información generando un respaldo de la base de datos. 
            ¡Tu tranquilidad es nuestra prioridad!
        </p>
        <div class="respaldo-card">
            <div class="respaldo-icon">
                <i class="fas fa-database"></i> <!-- Icono de base de datos -->
            </div>
            <div class="respaldo-info">
                <img src="public/img/backup.gif" style="width: 100px; height: auto; border-radius: 10px;" alt="backup-icon">
                <p>Último respaldo generado:</p>
                <p class="respaldo-fecha"><?php // obtenerUltimoRespaldo(); ?></p> <!-- Función PHP para obtener la fecha del último respaldo -->
            </div>
            <form id="respaldoForm" method="POST" action="app/controller/respaldos/c_respaldos.php">
                <button type="submit" class="btn-respaldo">
                    <i class="fas fa-download"></i> Generar Respaldo
                </button>
            </form>
        </div>
        <div class="respaldo-alert">
            <i class="fas fa-exclamation-circle"></i>
            <p>Recuerda guardar el archivo de respaldo en un lugar seguro.</p>
        </div>
    </div>
</div>
</div>

<link rel="stylesheet" href="public/css/configuracion.css">
<script>


document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("reglaCirculacionForm");
    const guardarBtn = document.getElementById("guardarReglaCirculacion");
    const mensajeError = document.getElementById("mensajeError");

    // Seleccionar todos los campos del formulario
    const campos = form.querySelectorAll("input, select");

    // Función para validar si todos los campos tienen valores
    function validarCampos() {
        let todosLlenos = true;
        campos.forEach((campo) => {
            if (!campo.value.trim()) {
                todosLlenos = false;
            }
        });
        return todosLlenos;
    }

    // Función para habilitar o deshabilitar el botón
    function actualizarBoton() {
        if (validarCampos()) {
            guardarBtn.disabled = false; // Habilitar el botón
            guardarBtn.style.display = "block";
            mensajeError.style.display = "none"; // Ocultar el mensaje de error
        } else {
            guardarBtn.disabled = true; // Deshabilitar el botón
            guardarBtn.style.display = "none"; // Deshabilitar el botón
        }
    }

    // Escuchar cambios en los campos del formulario
    campos.forEach((campo) => {
        campo.addEventListener("input", actualizarBoton);
        campo.addEventListener("change", actualizarBoton);
    });

    // Validar inicialmente al cargar la página
    actualizarBoton();

    // Mostrar mensaje si se intenta guardar con el botón deshabilitado
    guardarBtn.addEventListener("click", function (event) {
        if (guardarBtn.disabled) {
            event.preventDefault(); // Evitar que el formulario se envíe
            mensajeError.style.display = "block"; // Mostrar el mensaje de error
        }
    });
});



</script>
<script>
    function openTab(event, tabId) {
        document.querySelectorAll(".config-tab-content").forEach(tab => tab.classList.remove("active"));
        document.querySelectorAll(".tab-btn").forEach(btn => btn.classList.remove("active"));
        document.getElementById(tabId).classList.add("active");
        event.currentTarget.classList.add("active");
    }

    // script para guardar dinamicamente la configuracion
    document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("reglaCirculacionForm").addEventListener("submit", function (event) {
        event.preventDefault(); // Evita el envío tradicional del formulario

        let formData = new FormData(this);

        fetch("public/js/ajax/reglaCirculacion.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Datos actualizados correctamente");
            } else {
                alert("Error al actualizar los datos");
            }
        })
        .catch(error => console.error("Error en la petición AJAX:", error));
    });


    document.getElementById("configGeneralForm").addEventListener("submit", function (event) {
        event.preventDefault(); // Evita el envío tradicional del formulario

        let formData = new FormData(this);

        fetch("public/js/ajax/guardarDatosBiblioteca.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                //#idPrestamoDivalert("Datos de la biblioteca actualizados correctamente");
                //document.getElementById("abreviacionBP").innerHTML = document.getElementById("abreviacionBiblioteca").value;
                //location.reload(true);
                window.location.href = 'index.php?vista=configuracion&alerta=exito';
            } else {
                alert("Error al actualizar los datos de la biblioteca");
            }
        })
        .catch(error => console.error("Error en la petición AJAX:", error));
    });



});



function previewImage(event, targetId) {
        const reader = new FileReader();
        reader.onload = function () {
            const output = document.getElementById(targetId);
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }

</script>