<?php
include "app/config/database.php";
$conexion = conexion();

// Iniciar sesión si no está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar rol para elementos de UI
$rol = $_SESSION["rol"] ?? '';
$displayNone = $rol === 'Bibliotecario' ? 'style="display: none"' : "";

// Obtener el modo de vista de la sesión o establecer el predeterminado
$modoVista = $_SESSION['modo_vista_libros'] ?? 'tabla';

// Determinar la acción solicitada
$action = $_GET['action'] ?? 'listar';

switch ($action) {
    case 'buscar':
        include 'app/controller/libros/c_buscar_libro.php';
        exit;
    case 'listar':
    default:
        handleListado($conexion, $modoVista);
        break;
}

function handleListado($conexion, $modoVista) {
    global $displayNone;
    
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $porPagina = 10;
    $offset = ($pagina - 1) * $porPagina;
    
    // Consulta para contar el total de registros
    $sqlCount = "SELECT COUNT(*) AS total FROM libros";
    $resultCount = $conexion->query($sqlCount);
    $total = $resultCount->fetch_assoc()['total'];
    $totalPaginas = ceil($total / $porPagina);
    
    // Consulta principal con paginación
    $sql = "SELECT 
                libros.portada, 
                libros.isbn, 
                libros.titulo, 
                GROUP_CONCAT(DISTINCT CONCAT(autores.nombre) SEPARATOR ', ') AS autores, 
                libros.anio, 
                editorial.nombre AS editorialN, 
                libros.volumen, 
                libros.edicion, 
                GROUP_CONCAT(DISTINCT categorias.nombre SEPARATOR ', ') AS categorias, 
                (SELECT COUNT(*) FROM ejemplares WHERE ejemplares.isbn_copia = libros.isbn AND ejemplares.delete_at = 1) AS cantidad_ejemplares 
            FROM libros 
            JOIN libro_autores ON libros.isbn = libro_autores.isbn_libro 
            JOIN autores ON libro_autores.id_autor = autores.id 
            JOIN editorial ON libros.editorial = editorial.id 
            JOIN libro_categoria ON libros.isbn = libro_categoria.isbn_libro 
            JOIN categorias ON libro_categoria.id_categoria = categorias.id 
            GROUP BY libros.isbn 
            ORDER BY libros.titulo
            LIMIT ?, ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $offset, $porPagina);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $libros = [];
    while ($row = $result->fetch_assoc()) {
        $libros[] = $row;
    }
    
    // Mostrar HTML
    mostrarVista($libros, $pagina, $total, $totalPaginas, $modoVista);
}

function mostrarVista($libros, $pagina, $total, $totalPaginas, $modoVista) {
    global $displayNone;
    ?>
    <br><br>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestión de Libros</title>
        <link rel="stylesheet" href="public/css/libros.css">
    </head>
    <body>
        <div class="container-fluid py-3">
            <div class="vista-libros">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <img style="height: 40px; border-radius: 5px;" src="public/img/libros.gif" alt="libros">
                        <h4 class="ms-2 mb-0">Gestión de Libros</h4>
                    </div>
                    
                    <div class="d-flex">
                        <div class="input-group me-2" style="width: 300px;">
                            <input type="text" id="inputBuscarLibros" class="form-control form-control-sm" placeholder="Buscar libros...">
                            <button class="btn btn-outline-secondary" type="button" id="btnBuscar">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                        <button id="toggleVista" class="btn btn-outline-primary btn-sm me-2">
                            <i class="bi bi-<?= $modoVista === 'tabla' ? 'grid-fill' : 'table' ?>"></i> <?= $modoVista === 'tabla' ? 'Tarjetas' : 'Tabla' ?>
                        </button>
                        <button class="btn btn-success btn-sm" onclick="window.location.href='index.php?vista=registrar_libro'" <?= $displayNone ?>>
                            <i class="bi bi-plus-lg"></i> Nuevo
                        </button>
                    </div>
                </div>

                <div id="contenedor-vistas">
                    <?php if ($modoVista === 'tabla'): ?>
                        <!-- Vista de tabla -->
                        <div id="vista-tabla">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Título</th>
                                            <th>Autor</th>
                                            <th>Editorial</th>
                                            <th>Edición</th>
                                            <th>Copias</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaLibros">
                                        <?php foreach ($libros as $libro): ?>
                                            <tr>
                                                <td title="<?= htmlspecialchars($libro['titulo']) ?>">
                                                    <div class="text-truncate" style="max-width: 250px;">
                                                        <?= htmlspecialchars($libro['titulo']) ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-truncate" style="max-width: 200px;">
                                                        <?= htmlspecialchars($libro['autores']) ?>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($libro['editorialN']) ?></td>
                                                <td><?= htmlspecialchars($libro['edicion']) ?></td>
                                                <td><?= htmlspecialchars($libro['cantidad_ejemplares']) ?></td>
                                                <td>
                                                    <a href="index.php?vista=fichaLibro&isbn=<?= urlencode($libro['isbn']) ?>" class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-info-circle"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($libros)): ?>
                                            <tr><td colspan="6" class="text-center">No se encontraron registros.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Vista de tarjetas -->
                        <div id="vista-tarjetas">
                            <div class="tarjetas-contenedor">
                                <?php foreach ($libros as $libro): ?>
                                    <div class="tarjeta-libro">
                                        <div class="tarjeta-portada">
                                            <?php if (!empty($libro['portada'])): ?>
                                                <img src="<?= htmlspecialchars($libro['portada']) ?>" alt="Portada de <?= htmlspecialchars($libro['titulo']) ?>">
                                            <?php else: ?>
                                                <div class="portada-placeholder">
                                                    <i class="bi bi-book"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="tarjeta-contenido">
                                            <h5 class="tarjeta-titulo"><?= htmlspecialchars($libro['titulo']) ?></h5>
                                            <p class="tarjeta-autor"><?= htmlspecialchars($libro['autores']) ?></p>
                                            <div class="tarjeta-detalles">
                                                <span><i class="bi bi-building"></i> <?= htmlspecialchars($libro['editorialN']) ?></span>
                                                <span><i class="bi bi-journal-bookmark"></i> Ed. <?= htmlspecialchars($libro['edicion']) ?></span>
                                                <span><i class="bi bi-123"></i> <?= htmlspecialchars($libro['cantidad_ejemplares']) ?> copias</span>
                                            </div>
                                            <div class="tarjeta-acciones">
                                                <a href="index.php?vista=fichaLibro&isbn=<?= urlencode($libro['isbn']) ?>" class="btn btn-sm btn-outline-info">
                                                    <i class="bi bi-info-circle"></i> Detalles
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (empty($libros)): ?>
                                    <div class="no-resultados">No se encontraron registros.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Paginación -->
                <?php if ($totalPaginas > 1): ?>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">Mostrando <?= count($libros) ?> de <?= $total ?> registros</small>
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php if ($pagina > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?vista=libros&pagina=<?= $pagina - 1 ?>&modo=<?= $modoVista ?>">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                <li class="page-item <?= ($i == $pagina) ? 'active' : '' ?>">
                                    <a class="page-link" href="?vista=libros&pagina=<?= $i ?>&modo=<?= $modoVista ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagina < $totalPaginas): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?vista=libros&pagina=<?= $pagina + 1 ?>&modo=<?= $modoVista ?>">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
            </div>
        </div>

        
        <script>
        $(document).ready(function() {
            // Manejar el cambio de vista
            $('#toggleVista').click(function() {
                const nuevoModo = $('#vista-tabla').is(':visible') ? 'tarjetas' : 'tabla';
                
                // Guardar preferencia en sesión
                $.post('app/controller/libros/c_guardar_modo_vista.php', { modo: nuevoModo }, function() {
                    window.location.href = 'index.php?vista=libros&modo=' + nuevoModo;
                });
            });
            
            // Manejar búsqueda
            $('#btnBuscar').click(function() {
                buscarLibros();
            });
            
            $('#inputBuscarLibros').on('keypress', function(e) {
                if (e.which === 13) {
                    buscarLibros();
                }
            });
            
            function buscarLibros() {
                const termino = $('#inputBuscarLibros').val().trim();
                
                if (termino.length >= 3) {
                    $.ajax({
                        url: 'app/controller/libros/c_buscar_libro.php',
                        method: 'POST',
                        data: { termino: termino },
                        dataType: 'json',
                        beforeSend: function() {
                            // Mostrar spinner de carga
                            $('#contenedor-vistas').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>');
                        },
                        success: function(response) {
                            if (response.success) {
                                actualizarVistas(response.libros);
                            } else {
                                alert(response.error || 'Error en la búsqueda');
                            }
                        },
                        error: function() {
                            alert('Error al realizar la búsqueda');
                        }
                    });
                } else if (termino.length === 0) {
                    // Recargar la vista normal si el campo está vacío
                    window.location.href = 'index.php?vista=libros';
                }
            }
            
            function actualizarVistas(libros) {
                const modoActual = '<?= $modoVista ?>';
                
                if (modoActual === 'tabla') {
                    // Actualizar tabla
                    let tablaHtml = '<div class="table-responsive"><table class="table table-sm table-hover"><thead class="table-light"><tr><th>Título</th><th>Autor</th><th>Editorial</th><th>Edición</th><th>Copias</th><th>Acciones</th></tr></thead><tbody>';
                    
                    if (libros.length > 0) {
                        libros.forEach(libro => {
                            tablaHtml += `
                                <tr>
                                    <td title="${libro.titulo}">
                                        <div class="text-truncate" style="max-width: 250px;">${libro.titulo}</div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;">${libro.autores}</div>
                                    </td>
                                    <td>${libro.editorialN}</td>
                                    <td>${libro.edicion}</td>
                                    <td>${libro.cantidad_ejemplares}</td>
                                    <td>
                                        <a href="index.php?vista=fichaLibro&isbn=${encodeURIComponent(libro.isbn)}" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-info-circle"></i>
                                        </a>
                                    </td>
                                </tr>`;
                        });
                    } else {
                        tablaHtml += '<tr><td colspan="6" class="text-center">No se encontraron libros que coincidan con tu búsqueda.</td></tr>';
                    }
                    
                    tablaHtml += '</tbody></table></div>';
                    $('#contenedor-vistas').html(tablaHtml);
                } else {
                    // Actualizar tarjetas
                    let tarjetasHtml = '<div class="tarjetas-contenedor">';
                    
                    if (libros.length > 0) {
                        libros.forEach(libro => {
                            tarjetasHtml += `
                                <div class="tarjeta-libro">
                                    <div class="tarjeta-portada">
                                        ${libro.portada ? 
                                            `<img src="${libro.portada}" alt="Portada de ${libro.titulo}">` : 
                                            `<div class="portada-placeholder"><i class="bi bi-book"></i></div>`}
                                    </div>
                                    <div class="tarjeta-contenido">
                                        <h5 class="tarjeta-titulo">${libro.titulo}</h5>
                                        <p class="tarjeta-autor">${libro.autores}</p>
                                        <div class="tarjeta-detalles">
                                            <span><i class="bi bi-building"></i> ${libro.editorialN}</span>
                                            <span><i class="bi bi-journal-bookmark"></i> Ed. ${libro.edicion}</span>
                                            <span><i class="bi bi-123"></i> ${libro.cantidad_ejemplares} copias</span>
                                        </div>
                                        <div class="tarjeta-acciones">
                                            <a href="index.php?vista=fichaLibro&isbn=${encodeURIComponent(libro.isbn)}" class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-info-circle"></i> Detalles
                                            </a>
                                        </div>
                                    </div>
                                </div>`;
                        });
                    } else {
                        tarjetasHtml += '<div class="no-resultados">No se encontraron libros que coincidan con tu búsqueda.</div>';
                    }
                    
                    tarjetasHtml += '</div>';
                    $('#contenedor-vistas').html(tarjetasHtml);
                }
                
                // Ocultar paginación durante la búsqueda
                $('.pagination').hide();
            }
        });
        </script>
    </body>
    </html>
    <?php
}
?>