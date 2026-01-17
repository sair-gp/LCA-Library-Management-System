<?php 
require_once "app/controller/estanterias/c_setup_estanterias.php";
?>


<br><br>
<link rel="stylesheet" href="public/css/estantes.css">

<div class="estanteria-container">
    <!-- Encabezado con información de la estantería -->
    <div class="estanteria-header">
        <div class="estanteria-identificacion">
            <div class="estanteria-codigo"><?= $estanteria['id'] ?></div>
            <h1 class="estanteria-titulo">Estantería <?= $estanteria['tematica'] ?></h1>
            <div class="estanteria-meta">
                <!--span class="estanteria-ubicacion"><i class="bi bi-geo-alt"></i> <?= $estanteria['ubicacion'] ?></span-->
                <span class="estanteria-capacidad"><i class="bi bi-book"></i> <?= $estanteria["cantidadTotal"] ?> / <?= $estanteria['capacidad'] ?> libros</span>
                <!--span class="estanteria-estado"><i class="bi bi-check-circle"></i> <?= $estanteria['estado'] ?></span-->
            </div>
        </div>
        
        <div class="estanteria-stats">
            <div class="stat-card">
                <div class="stat-value"><?= calcularPorcentajeOcupacion($estanteria) ?>%</div>
                <div class="stat-label">Ocupación</div>
                <div class="stat-progress">
                    <div class="progress-bar" style="width: <?= calcularPorcentajeOcupacion($estanteria) ?>%"></div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value"><?= calcularLibrosPrestados($estanteria) ?></div>
                <div class="stat-label">En préstamo</div>
                <div class="stat-icon"><i class="bi bi-arrow-up-right"></i></div>
            </div>
            
            <!--div class="stat-card">
                <div class="stat-value"><?= calcularLibrosDanados($estanteria) ?></div>
                <div class="stat-label">Dañados</div>
                <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
            </div-->
        </div>
    </div>

    <!-- Barra de herramientas -->
    <div class="estanteria-toolbar">
        <div class="search-box">
            <input type="text" id="buscador-libros" placeholder="Buscar por título, autor, posición...">
            <i class="bi bi-search"></i>
        </div>
        
        <div class="toolbar-actions">
            <button class="btn-action" data-action="scan">
                <i class="bi bi-arrow-left-right"></i> Mover Copia
            </button>
            <!--button class="btn-action" data-action="add">
                <i class="bi bi-plus-circle"></i> Añadir Copia
            </button-->
            <!--button class="btn-action" data-action="report">
                <i class="bi bi-clipboard-check"></i> Reporte
            </button-->
            <button class="btn-action" id="toggle-view">
                <i class="bi bi-list-ul"></i> Vista Compacta
            </button>
        </div>
    </div>

    <!-- Vista principal de libros -->
    <div class="estanteria-main">
        <!-- Filtros -->
        <div class="estanteria-filters">
    <div class="filter-group">
        <label for="sort-by">Ordenar por:</label>
        <select class="filter-select" id="sort-by">
            <option value="posicion">Posición</option>
            <option value="titulo">Título (A-Z)</option>
            <option value="autor">Autor (A-Z)</option>
            <option value="prestamos">Más prestados</option>
        </select>
    </div>
    
    <div class="filter-group">
        <label for="group-by">Agrupar por:</label>
        <select class="filter-select" id="group-by">
            <option value="none">Sin agrupación</option>
            <option value="autor">Autor</option>
            <option value="estado">Estado</option>
        </select>
    </div>
    
    <div class="filter-group">
        <span class="filter-label">Estado:</span>
        <div class="filter-tags">
            <button type="button" class="filter-tag active" data-filter="all">Todos</button>
            <button type="button" class="filter-tag" data-filter="disponible">Disponibles</button>
            <button type="button" class="filter-tag" data-filter="prestado">Prestados</button>
            <!--button type="button" class="filter-tag" data-filter="danado">Dañados</button-->
        </div>
    </div>
</div>
        
        <!-- Controles de paginación -->
        <div class="pagination-controls">
            <!--span class="pagination-info">Mostrando <span id="libros-mostrados">12</span> de <?= count($estanteria['libros']) ?> libros</span-->
            <div class="pagination-buttons">
                <button id="prev-page" disabled><i class="bi bi-chevron-left"></i></button>
                <span id="page-indicator">1</span>
                <button id="next-page"><i class="bi bi-chevron-right"></i></button>
            </div>
            <select id="items-per-page">
                <option value="6">6 por página</option>
                <option value="12">12 por página</option>
                <option value="18">18 por página</option>
                <option value="120">Todos</option>
            </select>
        </div>
        
        <!-- Listado de libros -->
        <div class="libros-grid">
            <?php foreach ($estanteria['libros'] as $libro): ?>
                
                <div class="libro-card" 
                data-id="<?= $libro['id'] ?>" 
                data-autor="<?= htmlspecialchars($libro['autor']) ?>"
                data-estado="<?= strtolower($libro['estado_fisico']) ?>"
                data-disponibles="<?= $libro['disponibles'] ?>"
                data-ejemplares="<?= $libro['ejemplares'] ?>"
                data-libro="<?= htmlspecialchars(json_encode($libro), ENT_QUOTES, 'UTF-8') ?>">
                    <div class="libro-portada">
                        <img src="<?= $libro['portada'] ?>" alt="<?= $libro['titulo'] ?>">
                        <div class="libro-badge"><?= $libro['disponibles'] ?>/<?= $libro['ejemplares'] ?></div>
                    </div>
                    
                    <div class="libro-info">
                        <h3 class="libro-titulo"><?= $libro['titulo'] ?></h3>
                        <p class="libro-autor"><?= $libro['autor'] ?></p>
                        
                        <div class="libro-meta">
                            <span class="libro-posicion"><i class="bi bi-signpost"></i> <?= $libro['posicion'] ?></span>
                            <span class="libro-estado <?= strtolower($libro['estado_fisico']) ?>">
                                <i class="bi <?= obtenerIconoEstado($libro['estado_fisico']) ?>"></i> 
                                <?= $libro['estado_fisico'] ?>
                            </span>
                        </div>
                        
                        <div class="libro-actions">
                            <button class="btn-libro-action" data-action="details" data-bs-toggle="modal" data-bs-target="#libroModal">
                                <i class="bi bi-info-circle"></i> Detalles
                            </button>
                            <!--button class="btn-libro-action" data-action="edit">
                                <i class="bi bi-pencil"></i> Editar
                            </button-->
                        </div>
                    </div>
                </div>
      
            <?php endforeach; ?>
        </div>
        
        <!-- Controles de paginación (abajo) -->
        <div class="pagination-controls">
            <!--span class="pagination-info">Mostrando <span id="libros-mostrados-bottom">12</span> de <?= count($estanteria['libros']) ?> libros</span-->
            <div class="pagination-buttons">
                <button id="prev-page-bottom" disabled><i class="bi bi-chevron-left"></i></button>
                <span id="page-indicator-bottom">1</span>
                <button id="next-page-bottom"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </div>
    
  
</div>



<!-- JavaScript completo -->
<script src="public/js/fichaEstanteria.js"></script>

<?php require_once "modal/estanteriaModales.php"; ?>


<style>
    .rowe {
    display: flex;
    justify-content: center;
    margin-right: 10px;
    width: 100%;
    margin-top: 10px;
    }
</style>
