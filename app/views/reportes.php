<?php
include_once "app/config/database.php";

$conexion = conexion();


$hoy = Date('Y-m-d');


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Biblioteca</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #1a1a2e;
            color: #fff;
        }
        /* Encabezado */
        header {
            background: #0f3460;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-size: 1.1em;
        }
        nav a:hover {
            color: #00f260;
        }

        /* Banner */
        .banner {
            
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            font-size: 2em;
            margin-bottom: 30px;
        }
        .banner h2 {
            background: rgba(0, 0, 0, 0.6);
            padding: 20px;
            border-radius: 10px;
        }

        /* Contenedor principal */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Tarjetas de reportes */
        .report-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .report-item {
            background: #0f3460;
            padding: 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .report-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .report-card {
            background: linear-gradient(135deg, #00f260, #0575e6);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            font-size: 1.5em;
            font-weight: bold;
        }
        .report-description {
            margin-top: 15px;
            font-size: 1em;
            line-height: 1.5;
        }

        /* Pie de página */
        footer {
            background: #0f3460;
            padding: 20px;
            text-align: center;
            margin-top: 50px;
        }
        footer p {
            margin: 0;
            font-size: 0.9em;
        }

        /* Estilos para los modales (se mantienen igual) */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #16213e;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            max-width: 90%;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .modal-header h2 {
            margin: 0;
        }
        .modal-header .close {
            cursor: pointer;
            font-size: 1.5em;
        }
        .modal-body {
            margin-bottom: 20px;
        }
        .modal-body label {
            display: block;
            margin-bottom: 5px;
        }
        .modal-body input, .modal-body select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background: #0f3460;
            color: #fff;
        }
        .modal-footer {
            text-align: right;
        }
        .modal-footer button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background: #00f260;
            color: #fff;
            cursor: pointer;
        }
        .modal-footer button:hover {
            background: #0575e6;
        }

        /* Estilos para las pestañas */

        .nav-tabs .nav-item .nav-link.active {
            color: #000;
            background-color: #00f260;
        }

        .nav-item button:hover {
            color: #00f260;
        }


/* Estilos para los modales */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: #16213e; /* Fondo oscuro */
    padding: 20px;
    border-radius: 10px;
    width: 500px; /* Un poco más ancho para las pestañas */
    max-width: 90%;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.modal-header h2 {
    margin: 0;
    color: #00f260; /* Color del gradiente para el título */
}

.modal-header .close {
    cursor: pointer;
    font-size: 1.5em;
    color: #fff; /* Color blanco para el botón de cerrar */
}

.modal-header .close:hover {
    color: #00f260; /* Cambia el color al pasar el mouse */
}

.modal-body label {
    display: block;
    margin-bottom: 5px;
    color: #fff; /* Texto blanco para las etiquetas */
}

.modal-body input,
.modal-body select {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #ccc;
    background: #0f3460; /* Fondo oscuro para los inputs */
    color: #fff; /* Texto blanco */
}

.modal-body input:focus,
.modal-body select:focus {
    border-color: #00f260; /* Borde resaltado al enfocar */
    outline: none;
}

.modal-footer {
    text-align: right;
}

.modal-footer button {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    background: #00f260; /* Color del gradiente */
    color: #16213e; /* Texto oscuro */
    cursor: pointer;
    transition: background-color 0.3s;
}

.modal-footer button:hover {
    background: #0575e6; /* Cambia el color al pasar el mouse */
}

/* Estilos para Select2 en modo oscuro */
.select2-container--default .select2-selection--single {
    background-color: #0f3460; /* Fondo oscuro */
    border: 1px solid #ccc; /* Borde gris */
    border-radius: 5px; /* Bordes redondeados */
    color: #fff; /* Texto blanco */
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #fff; /* Texto blanco */
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100%;
    top: 0;
    right: 5px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: #fff transparent transparent transparent; /* Flecha blanca */
}

.select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
    border-color: transparent transparent #fff transparent; /* Flecha blanca invertida */
}

/* Dropdown de Select2 */
.select2-container--default .select2-dropdown {
    background-color: #0f3460; /* Fondo oscuro */
    border: 1px solid #ccc; /* Borde gris */
    border-radius: 5px; /* Bordes redondeados */
}

.select2-container--default .select2-results__option {
    color: #fff; /* Texto blanco */
    padding: 8px 12px; /* Espaciado interno */
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #00f260; /* Fondo resaltado con el color del gradiente */
    color: #16213e; /* Texto oscuro para contraste */
}

.select2-container--default .select2-results__option[aria-selected=true] {
    background-color: #16213e; /* Fondo para opción seleccionada */
    color: #fff; /* Texto blanco */
}

/* Placeholder */
.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #ccc; /* Color gris para el placeholder */
}

/* Input de búsqueda */
.select2-container--default .select2-search--dropdown .select2-search__field {
    background-color: #0f3460; /* Fondo oscuro */
    border: 1px solid #ccc; /* Borde gris */
    color: #fff; /* Texto blanco */
    border-radius: 5px; /* Bordes redondeados */
}

/* Mensajes de error y carga */
.select2-container--default .select2-results__option.loading-results,
.select2-container--default .select2-results__option.no-results {
    color: #fff; /* Texto blanco */
    background-color: #0f3460; /* Fondo oscuro */
}



    </style>
</head>
<body>
    <!-- Encabezado -->
     <br>
    <header>
        <h1>Generar reportes</h1>
    </header>

    <!-- Banner -->
    <!--div class="banner">
        <h2>Genera reportes detallados de tu biblioteca</h2>
    </div-->

    <!-- Contenedor principal -->
    <div class="container">
        <div class="report-section">
            <!-- Reporte de Préstamos -->
            <div class="report-item" data-bs-toggle="modal" data-bs-target="#prestamosModal">
                <div class="report-card"> Préstamos</div>
                <!--div class="report-description">
                    Consulta los registros de libros prestados, fechas y usuarios responsables. Obtén información detallada sobre préstamos activos y vencidos.
                </div-->
            </div>

            <!-- Reporte de Devoluciones -->
            <div class="report-item" data-bs-toggle="modal" data-bs-target="#devolucionesModal">
                <div class="report-card"> Devoluciones</div>
                <!--div class="report-description">
                    Revisa el historial de libros devueltos y posibles retrasos en las entregas. 
                </div-->
            </div>

            <!-- Reporte de Actividades -->
            <div class="report-item" data-bs-toggle="modal" data-bs-target="#actividadesModal">
                <div class="report-card"> Actividades</div>
                <!--div class="report-description">
                    Consulta las actividades programadas, su estado y fechas. Filtra por actividades activas, reprogramadas o suspendidas.
                </div-->
            </div>

            <!-- Reporte de Libros más solicitados -->
            <div class="report-item" data-bs-toggle="modal" data-bs-target="#librosSolicitadosModal">
                <div class="report-card"> Libros más solicitados</div>
                <!--div class="report-description">
                    Obtén información sobre los libros más populares en la biblioteca. Filtra por categoría y rango de fechas.
                </div-->
            </div>

            <!-- Reporte de Sanciones -->
            <!--div class="report-item" data-bs-toggle="modal" data-bs-target="#sancionesModal">
                <div class="report-card"> Sanciones</div>
                <-div class="report-description">
                    Revisa las sanciones aplicadas a los usuarios por retrasos o pérdidas. Filtra por tipo de sanción y fechas.
                </div>
            </div-->

            <!-- Reporte de Visitas por horario -->
            <div class="report-item" data-bs-toggle="modal" data-bs-target="#visitasHorarioModal">
                <div class="report-card"> Visitas por horario</div>
                <!--div class="report-description">
                    Analiza las visitas a la biblioteca por franjas horarias. Filtra por mañana, tarde o noche.
                </div-->
            </div>

            <!-- Reporte del mes -->
            <div class="report-item" data-bs-toggle="modal" data-bs-target="#reporteMensualModal">
                <div class="report-card"> Informe mensual</div>
                <!--div class="report-description">
                    Revisa el historial de libros devueltos y posibles retrasos en las entregas. 
                </div-->
            </div>
        </div>
    </div>


    <!-- Modales (se mantienen igual) -->
   <?php include_once "modal/modalesReportes.php"; ?>

    <!--script>
        // Función para abrir un modal
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "flex";
        }

        // Función para cerrar un modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        // Función para generar el reporte (simulación)
        function generarReporte(tipo) {
            alert(`Generando reporte de ${tipo}...`);
            // Aquí puedes agregar la lógica para generar el reporte
        }

        // Función para abrir una pestaña específica
function openTab(event, tabName) {
    // Ocultar todos los contenidos de las pestañas
    const tabContents = document.getElementsByClassName("tab-content");
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].classList.remove("active");
    }

    // Desactivar todos los botones de las pestañas
    const tabButtons = document.getElementsByClassName("tab-button");
    for (let i = 0; i < tabButtons.length; i++) {
        tabButtons[i].classList.remove("active");
    }

    // Mostrar el contenido de la pestaña seleccionada
    document.getElementById(tabName).classList.add("active");

    // Activar el botón de la pestaña seleccionada
    event.currentTarget.classList.add("active");
}

// Función para cerrar el modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
}

// Función para abrir el modal
function openModal(modalId) {
    document.getElementById(modalId).style.display = "flex";
}

// Función para generar el reporte (puedes personalizarla)
function generarReporte(tipo) {
    const activeTab = document.querySelector(".tab-content.active").id;

    let fechaInicio, fechaFin, usuario, lector;

    if (activeTab === "general") {
        fechaInicio = document.getElementById("fechaInicioGeneral").value;
        fechaFin = document.getElementById("fechaFinGeneral").value;
    } else if (activeTab === "porUsuario") {
        fechaInicio = document.getElementById("fechaInicioUsuario").value;
        fechaFin = document.getElementById("fechaFinUsuario").value;
        usuario = document.getElementById("usuarioPrestamos").value;
    } else if (activeTab === "porLector") {
        fechaInicio = document.getElementById("fechaInicioLector").value;
        fechaFin = document.getElementById("fechaFinLector").value;
        lector = document.getElementById("lectorPrestamos").value;
    }

    console.log("Generando reporte...");
    console.log("Fecha de inicio:", fechaInicio);
    console.log("Fecha de fin:", fechaFin);
    console.log("Usuario:", usuario);
    console.log("Lector:", lector);

    // Aquí puedes enviar los datos al servidor para generar el reporte
}
    </script-->


</body>
</html>