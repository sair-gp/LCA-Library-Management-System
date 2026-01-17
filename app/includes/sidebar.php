<?php

//require_once "app/config/database.php";


// Crear instancia del usuario
require_once 'app/model/usuarios.php';



$usuario = new User();

$nombreCompletoUsuario = $usuario->getNombreCompletoFormateado();
$rolUsuario = $usuario->getRol();
// Generar menú
$menuItems = $usuario->generarMenu();



?>

<div class="sidebar close">
    <!-- Encabezado de la barra lateral -->
    <div class="top">
        <div class="logo" style="margin: 15px;">
            <img src="<?= $_SESSION["datos_biblioteca"]["logo"] ?? 'public/img/logo/logo.png' ?>" style="width: 32px; height: 32px; margin-right: 10px;">
            <span id="abreviacionBP" title="<?= $_SESSION["datos_biblioteca"]["abreviacion"] ?? 'Biblioteca' ?>"><?= $_SESSION["datos_biblioteca"]["abreviacion"] ?? 'Biblioteca' ?></span>
        </div>
        <button id="botonTglSidebar"><i class="bi bi-list" id="btn"></i></button>
    </div>

    <!-- Información del usuario -->
    <div class="user">
        <?php $fotoPerfil = $_SESSION["fotoPerfil"] != NULL ? $_SESSION["fotoPerfil"] : "public/img/userDefault.jpg" ?>
        <img src="<?php echo $fotoPerfil ?>" alt="me" id="fotoPerfilSidebar" class="user-img">
        <div>
            <p class="bold"><?= $nombreCompletoUsuario ?></p>
            <p><?= $rolUsuario ?></p>
        </div>
    </div>

    <!-- Menú de navegación -->
    <ul>
        <?php foreach ($menuItems as $item => $details): ?>
            <?php
            // Si tiene permiso para el item, lo mostramos
            if (isset($details['permiso']) && !$usuario->tienePermiso($details['permiso'])) continue;

            // Si tiene submenus, verificar si tiene permisos para al menos uno
            $mostrarDropdown = false;
            if (isset($details['submenus'])) {
                foreach ($details['submenus'] as $submenu) {
                    if ($usuario->tienePermiso($submenu['permiso'])) {
                        $mostrarDropdown = true;
                        break;
                    }
                }
            }

            // Si tiene permiso para el dropdown o el item no tiene submenus, se renderiza
            if ($mostrarDropdown || !isset($details['submenus'])):
            ?>
                <li <?= isset($details['submenus']) ? 'class="dropdown"' : '' ?>>
                    <a href="<?= $details['link'] ?? '#' ?>">
                        <i class="<?= $details['icon'] ?>"></i>
                        <span class="nav-item"><?= $item ?></span>
                    </a>
                    <?php if (isset($details['submenus'])): ?>
                        <ul class="submenu inactive">
                            <?php foreach ($details['submenus'] as $submenu): ?>
                                <?php if (!$usuario->tienePermiso($submenu['permiso'])) continue; ?>
                                <li>
                                    <a href="<?= $submenu['link'] ?>">
                                        <i class="<?= $submenu['icon'] ?>"></i>
                                        <span><?= $submenu['label'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
        <li>
            <a href="" data-bs-toggle="modal" data-bs-target="#confirmarCerrarSesion">
                <i class="bi bi-box-arrow-in-left"></i>
                <span class="nav-item">Salir</span>
            </a>
        </li>
    </ul>
</div>
<!-- Modal para confirmar cierre de sesion -->
<div class="modal fade" id="confirmarCerrarSesion" tabindex="-1" aria-labelledby="confirmarCerrarSesionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmarCerrarSesionLabel">Confirmar Cierre de Sesión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas cerrar la sesión?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="app/controller/c_logout.php" class="btn btn-danger">Cerrar Sesión</a>
            </div>
        </div>
    </div>
</div>



<!-- Contenido principal -->
<div class="main-content">

    <?php

    $vistasRequierenPermiso = [
        'home' => null,
        //"inicio" => null, // Vistas abiertas a todos
        'libros' => 'libros',
        'autores' => 'autores',
        'editoriales' => 'editoriales',
        'categorias' => 'categorias',
        'prestamos' => 'prestamos',
        'reportes' => 'reportes',
        'visitantes' => 'visitantes',
        'actividades' => 'actividades',
        'usuarios' => 'usuarios',
        'historial' => 'historial',
        'papelera' => null,
        'registrar_usuario' => null,
        'cargos' => null,
        "fichaLibro" => null,
        "fichaAutor" => null,
        "detallesVolumen" => null,
        "registrar_libro" => null,
        "registrar_obra" => null,
        "registrar_estanteria" => null,
        "registrar_volumen" => null,
        "fichaLector" => null,
        "configuracion" => null,
        "multas" => null,
        'registrarVisitante' => null,
        'estanterias' => null,
        'fichaEstanteria' => null,
        'perfilUsuario' => null,
        'libroCategoria' => null,
        'librosEditorial' => null
    ];


    // Obtén la vista solicitada
    $vistaSolicitada = htmlspecialchars($_GET['vista'] ?? 'home');

    // Verifica si la vista existe en la lista de permisos
    if (array_key_exists($vistaSolicitada, $vistasRequierenPermiso)) {
        $permisoRequerido = $vistasRequierenPermiso[$vistaSolicitada];
        if ($permisoRequerido === null || $usuario->tienePermiso($permisoRequerido)) {
            // Incluir la vista si el usuario tiene el permiso
            include_once "app/views/modal/userConfig.php";

            include "app/includes/section.php";
            echo '<input type="hidden" id="alertaDulce" value="' . (isset($_GET["alerta"]) ? $_GET["alerta"] : "") . '">';

            include "app/views/modal/mostrarRegistroCualquierTabla.php";

            include "app/views/{$vistaSolicitada}.php";

            //$conexion = conexion();
            

            // Actualizar el estado de los préstamos vencidos (solo los vencidos hoy)
            $consulta = "UPDATE prestamos SET estado = 3 WHERE fecha_fin < CURRENT_DATE AND estado NOT IN (2, 3, 5)";
            $resultado = $conexion->query($consulta);

            // Si la actualización fue exitosa, obtener los préstamos que estén por vencer mañana, hoy, o ya vencidos
            if ($resultado) {
                // Consulta para obtener los préstamos que faltan un día, están vencidos o expiran hoy
                $consulta = "
                    SELECT DISTINCT p.id, p.estado, v.nombre, p.fecha_fin, l.titulo, l.isbn, e.cota FROM prestamos p JOIN visitantes v ON p.lector = v.cedula JOIN ejemplares e ON e.id = p.cota JOIN libros l ON l.isbn = e.isbn_copia WHERE p.fecha_fin = CURRENT_DATE OR p.fecha_fin = CURRENT_DATE + INTERVAL 1 DAY OR p.fecha_fin = CURRENT_DATE - INTERVAL 1 DAY;
    ";

                $resultado = $conexion->query($consulta);

                // Verificar si se obtuvieron resultados
                if ($resultado->num_rows > 0) {
                    include_once "app/model/historial.php";
                    // Aquí generamos las notificaciones en JavaScript
                    echo '<script src="public/js/notificaciones.js"></script>';

                    // Crea el objeto DateTime de la fecha actual
                    $fechaActual = new DateTime();
                    //$fechaActual->modify('-1 day');
                    //echo $fechaActual->format('Y-m-d') . "<br>";

                    //ma;ana
                    $fechaVencimientoMañana = new DateTime();
                    $fechaVencimientoMañana->modify('+1 day');
                    //echo $fechaVencimientoMañana->format("Y-m-d") . "<br><br>";

                    while ($row = $resultado->fetch_assoc()) {

                        $fechaFin = new DateTime($row['fecha_fin']);
                        //echo $row["id"] . "<br>";
                        //echo $fechaFin->format('Y-m-d'), "<br><br>";


                        // Comparar las fechas en formato Y-m-d
                        if ($fechaFin->format('Y-m-d') == $fechaVencimientoMañana->format('Y-m-d') && $row["estado"] != 3) {
                            $titulo = "Préstamo por vencer mañana";
                            $texto = "El préstamo con ID: " . $row['id'] . " de " . $row['nombre'] . " vencerá mañana.";
                            $link = "index.php?vista=prestamos&id=" . $row['id'];
                        }
                        // El último día para entregar el préstamo (fecha_fin es hoy)
                        elseif ($fechaFin->format('Y-m-d') == $fechaActual->format('Y-m-d') && $row["estado"] != 3) {
                            $titulo = "Último día para entregar el préstamo";
                            $texto = "El préstamo con ID: " . $row['id'] . " de " . $row['nombre'] . " debe ser entregado hoy.";
                            $link = "index.php?vista=prestamos&id=" . $row['id'];
                        }
                        // Préstamo vencido (fecha_fin ya pasó)
                        elseif ($fechaFin->format('Y-m-d') < $fechaActual->format('Y-m-d') && $row["estado"] == 3) {

                            $titulo = "Préstamo vencido";
                            $texto = "El préstamo con ID: " . $row['id'] . " de " . $row['nombre'] . " ya ha vencido.";
                            $link = "index.php?vista=prestamos&id=" . $row['id'];

                            // Generar el string de detalles
                            $detalles = "Titulo: {$row['titulo']}, ISBN: {$row['isbn']}, Cota: {$row['cota']}, Lector: {$row['nombre']}";

                            //echo "<br>" . $detalles . "<br>";

                            // Verificar si ya existe en el historial
                            $consultaHistorial = "SELECT COUNT(*) FROM historial WHERE accion_id = 13 AND detalles = ? AND DATE(fecha) = CURDATE();";
                            $stmtHistorial = $conexion->prepare($consultaHistorial);
                            $stmtHistorial->bind_param("s", $detalles);
                            $stmtHistorial->execute();
                            $stmtHistorial->bind_result($conteo);
                            $stmtHistorial->fetch();
                            $stmtHistorial->close(); // Cierra el statement 
                            //echo "<br>Conteo: " . $conteo . "<br><br>";
                            // Si no existe un registro con esos detalles, lo agregamos
                            if ($conteo == 0) {
                                //echo "<br>dentro de historial";
                                $historial = new Historial($conexion);
                                $resultadoRegistro = $historial->registrar_accion(
                                    $_SESSION["cedula"], // Responsable
                                    $detalles,           // Detalles
                                    $fechaActual->format('Y-m-d'), // Fecha en formato compatible con MySQL
                                    13                   // Acción (13: préstamo vencido)
                                );

                                if (!$resultadoRegistro) {
                                    error_log("Error al registrar en el historial: {$conexion->error}");
                                }
                            }
                        }


                        // Llamada a la función de notificación en JavaScript
                        if (isset($titulo, $texto, $link)) {
                            echo "<script>addNotification('$titulo', '$texto', '$link');</script>";
                        }
                    }
                } else {
                    echo "<script>addNotification('No hay préstamos a vencer', 'No hay préstamos próximos a vencer o ya vencidos.');</script>";
                }
            } else {
                echo "Error al actualizar los préstamos: " . $conexion->error;
            }
        } else {
            // Mostrar mensaje de error si no tiene permiso
            include "app/views/errors/403.php";
        }
    }

    ?>


    <?php // include "app/views/" . htmlspecialchars($_GET['vista']) . ".php"; 
    ?>
</div>






<script defer>
    //Prevenir que el usuario abra el inspector
    //Desactivar click derecho
    /*document.addEventListener('contextmenu', function (e) {
    e.preventDefault();
    });
    //Desactivar atajos
    document.addEventListener('keydown', function (e) {
    if (e.keyCode === 123 || (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) || (e.ctrlKey && e.keyCode === 85)) {
    e.preventDefault();
    }
    });*/

    // Esperar 100 milisegundos y luego redirigir eliminando el parámetro 'alerta' de la URL
    /*setTimeout(() => {
        window.history.replaceState({}, document.title, window.location.pathname);
    }, 100);*/ // 100 milisegundos = 0.1 segundo

    document.addEventListener("DOMContentLoaded", () => {
        // Botón para expandir o colapsar el sidebar
        const sidebarToggle = document.getElementById("btn");
        const sidebar = document.querySelector(".sidebar");

        if (sidebarToggle) {
            sidebarToggle.addEventListener("click", () => {
                const divDerecha = document.querySelector(".divDerecha")
                sidebar.classList.remove("close");

                if (divDerecha) {
                    divDerecha.style = "margin-left: 61.4%";
                    console.log("existe divDerecha");
                }

                if (sidebar.classList.contains("active")) {
                    divDerecha.style = "margin-left: 55.9%";

                }

            });
        }

        // Dropdown para submenús
        const dropdowns = document.querySelectorAll(".dropdown > a");

        dropdowns.forEach((dropdownLink) => {
            dropdownLink.addEventListener("click", (e) => {
                e.preventDefault();

                const currentMenu = dropdownLink.nextElementSibling; // El submenú
                const allMenus = document.querySelectorAll(".submenu");

                // Cerrar todos los menús excepto el actual
                allMenus.forEach((menu) => {
                    if (menu !== currentMenu) {
                        menu.classList.remove("active");
                        menu.style.maxHeight = "0"; // Cerrar el menú
                    }
                });

                // Alternar el menú actual
                if (currentMenu.classList.contains("active")) {
                    currentMenu.classList.remove("active");
                    currentMenu.style.maxHeight = "0"; // Cerrar el menú
                } else {
                    currentMenu.classList.add("active");
                    currentMenu.style.maxHeight = `${currentMenu.scrollHeight}px`; // Abrir el menú
                }
            });
        });
    });
</script>


<!-- SCRIPT DEL SECTION   -->

<script defer>
    //section

    // Dropdown Toggle
    const dropdownButton = document.getElementById('userOptionsToggle');
    const dropdownMenu = document.querySelector('.user-options-menu');

    // Toggle the dropdown menu on button click
    dropdownButton.addEventListener('click', (e) => {
        e.stopPropagation(); // Prevent event bubbling
        dropdownMenu.classList.toggle('show'); // Show/hide dropdown menu
        const isExpanded = dropdownButton.getAttribute('aria-expanded') === 'true';
        dropdownButton.setAttribute('aria-expanded', !isExpanded);
    });

    // Close the dropdown menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!dropdownMenu.contains(e.target) && !dropdownButton.contains(e.target)) {
            dropdownMenu.classList.remove('show');
            dropdownButton.setAttribute('aria-expanded', 'false');
        }
    });
</script>