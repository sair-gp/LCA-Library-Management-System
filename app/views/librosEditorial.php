<?php

include "app/config/database.php";
$conexion = conexion();

// Obtener el ID de la editorial desde GET
$editorial_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Consulta para obtener los datos de la editorial
$query_editorial = "SELECT nombre FROM editorial WHERE id = $editorial_id";
$result_editorial = mysqli_query($conexion, $query_editorial);
$editorial = mysqli_fetch_assoc($result_editorial);
$nombre_editorial = $editorial ? $editorial['nombre'] : 'Editorial Desconocida';

// Consulta para obtener los libros (la que proporcionaste)
$query_libros = "SELECT l.isbn, l.titulo, GROUP_CONCAT(DISTINCT au.nombre SEPARATOR ', ') as autores, 
                GROUP_CONCAT(DISTINCT c.nombre SEPARATOR ', ') as categorias, l.anio, l.portada 
                FROM libros l 
                LEFT JOIN libro_categoria lc ON lc.isbn_libro = l.isbn 
                LEFT JOIN categorias c ON c.id = lc.id_categoria 
                LEFT JOIN libro_autores la ON la.isbn_libro = l.isbn 
                LEFT JOIN autores au ON au.id = la.id_autor 
                LEFT JOIN editorial ed ON ed.id = l.editorial 
                WHERE ed.id = $editorial_id 
                GROUP BY l.isbn, l.titulo";

$result_libros = mysqli_query($conexion, $query_libros);
$total_libros = mysqli_num_rows($result_libros);


?>
<br><br>
<div class="container-fluid px-4">
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0"><i class="bi bi-book me-2"></i>Libros de la Editorial: <span class="text-primary"><?= htmlspecialchars($nombre_editorial) ?></span></h3>
                    <p class="text-muted mb-0">Total de libros: <?= $total_libros ?></p>
                </div>
                
                <div class="d-flex">
                    <div class="btn-group" role="group">
                        <button type="button" id="btnVistaTabla" class="btn btn-outline-secondary active">
                            <i class="bi bi-list-ul"></i> Tabla
                        </button>
                        <button type="button" id="btnVistaTarjetas" class="btn btn-outline-secondary">
                            <i class="bi bi-grid"></i> Tarjetas
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Vista de tabla (por defecto) -->
            <div id="vista-tabla">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%;">ISBN</th>
                                <th style="width: 25%;">Título</th>
                                <th style="width: 20%;">Autor(es)</th>
                                <th style="width: 20%;">Categorías</th>
                                <th style="width: 15%;">Año</th>
                                <th style="width: 10%; text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($total_libros > 0): ?>
                                <?php while ($libro = mysqli_fetch_assoc($result_libros)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($libro['isbn']) ?></td>
                                        <td>
                                            <div class="fw-semibold"><?= htmlspecialchars($libro['titulo']) ?></div>
                                        </td>
                                        <td><?= htmlspecialchars($libro['autores'] ?? 'Sin autores') ?></td>
                                        <td><?= htmlspecialchars($libro['categorias'] ?? 'Sin categorías') ?></td>
                                        <td><?= htmlspecialchars($libro['anio']) ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-primary" onclick="window.location.href='index.php?vista=fichaLibro&isbn=<?= $libro['isbn'] ?>'" data-bs-toggle="tooltip" title="Ver detalles">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-exclamation-circle text-muted" style="font-size: 2rem;"></i>
                                            <p class="text-muted mt-2 mb-0">No se encontraron libros para esta editorial</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Aquí iría la paginación si la implementas -->
                <!-- <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Mostrando X de <?= $total_libros ?> registros
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            ...
                        </ul>
                    </nav>
                </div> -->
            </div>
            
            <!-- Vista de tarjetas (oculta por defecto) -->
            <div id="vista-tarjetas" class="d-none">
                <div class="vista-tarjetas">
                    <div class="tarjetas-contenedor">
                        <?php 
                        // Reiniciamos el puntero del resultado para volver a iterar
                        mysqli_data_seek($result_libros, 0);
                        
                        if ($total_libros > 0): 
                            while ($libro = mysqli_fetch_assoc($result_libros)): ?>
                                <div class="tarjeta-libro">
                                    <div class="tarjeta-portada">
                                        <?php if (!empty($libro['portada'])): ?>
                                            <img src="<?= htmlspecialchars($libro['portada']) ?>" alt="<?= htmlspecialchars($libro['titulo']) ?>">
                                        <?php else: ?>
                                            <div class="portada-placeholder">
                                                <i class="bi bi-book"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="tarjeta-contenido">
                                        <h5 class="tarjeta-titulo"><?= htmlspecialchars($libro['titulo']) ?></h5>
                                        <p class="tarjeta-autor"><?= htmlspecialchars($libro['autores'] ?? 'Sin autores') ?></p>
                                        <div class="tarjeta-detalles">
                                            <span><i class="bi bi-tag me-1"></i> <?= htmlspecialchars($libro['categorias'] ?? 'Sin categorías') ?></span>
                                            <span><i class="bi bi-calendar me-1"></i> <?= htmlspecialchars($libro['anio']) ?></span>
                                            <span><i class="bi bi-upc me-1"></i> <?= htmlspecialchars($libro['isbn']) ?></span>
                                        </div>
                                        <div class="tarjeta-acciones">
                                            <button class="btn btn-sm btn-outline-primary me-1" onclick="window.location.href='index.php?vista=fichaLibro&isbn=<?= $libro['isbn'] ?>'" data-bs-toggle="tooltip" title="Ver detalles">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                           
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="no-resultados">
                                <i class="bi bi-exclamation-circle" style="font-size: 2rem;"></i>
                                <p>No se encontraron libros para esta editorial</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Aquí iría la paginación si la implementas -->
                <!-- <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Mostrando X de <?= $total_libros ?> registros
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            ...
                        </ul>
                    </nav>
                </div> -->
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos para la vista de tabla y tarjetas */
    .vista-tarjetas { margin: 15px 0; }
    .tarjetas-contenedor { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); 
        gap: 15px; 
        padding: 10px;
    }
    .tarjeta-libro { 
        background: white; 
        border-radius: 8px; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .tarjeta-portada { 
        height: 150px; 
        background-color: #f8f9fa; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        border-bottom: 1px solid #eee;
        padding: 10px;
    }
    .tarjeta-portada img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }
    .portada-placeholder { 
        font-size: 2.5rem; 
        color: #6c757d; 
    }
    .tarjeta-contenido { 
        padding: 12px; 
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .tarjeta-titulo { 
        font-size: 0.95rem; 
        margin: 0 0 6px 0; 
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 40px;
    }
    .tarjeta-autor { 
        color: #6c757d; 
        font-size: 0.8rem; 
        margin: 0 0 8px 0;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .tarjeta-detalles { 
        font-size: 0.75rem; 
        color: #6c757d; 
        margin-bottom: 12px;
    }
    .tarjeta-detalles span { 
        display: block; 
        margin-bottom: 4px;
    }
    .tarjeta-acciones { 
        margin-top: auto;
        display: flex; 
        justify-content: flex-end; 
    }
    .no-resultados { 
        grid-column: 1 / -1; 
        text-align: center; 
        padding: 20px; 
        color: #6c757d; 
    }
    .table-responsive {
        min-height: 300px;
    }
</style>

<script>
    // Script para alternar entre vistas de tabla y tarjetas
    document.addEventListener('DOMContentLoaded', function() {
        const btnVistaTabla = document.getElementById('btnVistaTabla');
        const btnVistaTarjetas = document.getElementById('btnVistaTarjetas');
        const vistaTabla = document.getElementById('vista-tabla');
        const vistaTarjetas = document.getElementById('vista-tarjetas');
        
        btnVistaTabla.addEventListener('click', function() {
            this.classList.add('active');
            btnVistaTarjetas.classList.remove('active');
            vistaTabla.classList.remove('d-none');
            vistaTarjetas.classList.add('d-none');
        });
        
        btnVistaTarjetas.addEventListener('click', function() {
            this.classList.add('active');
            btnVistaTabla.classList.remove('active');
            vistaTarjetas.classList.remove('d-none');
            vistaTabla.classList.add('d-none');
        });
        
        // Inicializar tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>