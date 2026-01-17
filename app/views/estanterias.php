<?php
require_once "app/config/database.php";
$conexion = conexion();


$consulta = "SELECT
    es.id,
    es.codigo,
    es.descripcion,
    es.cantidad_filas,
    es.capacidad_total AS capacidad,
    COUNT(DISTINCT CASE WHEN ej.estado = 1 AND ej.delete_at = 1 THEN ej.id END) AS ocupacion,
    COUNT(DISTINCT CASE WHEN p.estado IN (1, 3, 4) THEN p.id END) AS prestamos_activos
FROM
    estanterias es
LEFT JOIN
    fila f ON f.EstanteriaID = es.id
LEFT JOIN
    ejemplares ej ON ej.filaID = f.FilaID AND ej.delete_at = 1
LEFT JOIN
    prestamos p ON p.cota = ej.id AND p.estado IN (1, 3, 4)
GROUP BY
    es.id, es.codigo, es.descripcion, es.cantidad_filas, es.capacidad_total
ORDER BY
    es.id;";

$resultado = $conexion->query($consulta);

$estanterias = [];
while ($fila = $resultado->fetch_assoc()) {
    $estanterias[] = [
        "id"=> $fila["codigo"],
        'tematica' => $fila["descripcion"],
        'ubicacion' => '',
        'responsable' => '',
        'ultima_revision' => '',
        'capacidad' => $fila["capacidad"] ?? 0,
        'ocupacion' => $fila["ocupacion"] ?? 0,
        'estado' => 'Óptimo',
        'libros' => $fila["ocupacion"] ?? 0,
        'prestados' => $fila["prestamos_activos"] ?? 0
    ];
}

?>


<br>

   <link rel="stylesheet" href="public/css/listaEstantes.css">


    <div class="container">
        <header>
            <div class="logo">
                <i class="bi bi-book-half logo-icon"></i>
                <span class="logo-text"></span>
                
                <div id="ejemplarEstanteriaSearch">
    <div class="ejemplar-search-box">
        <i class="bi bi-search ejemplar-search-icon"></i>
        <input type="text" class="ejemplar-search-field" placeholder="Buscar ejemplar en estantería...">
    </div>
    <div class="ejemplar-results-container"></div>
</div>

            </div>
            <!-- Botón para abrir el modal de nueva estantería -->
             <?php if ($_SESSION["rol"] !== "Bibliotecario"): ?>
<div class="toolbar-actions" style="margin-right: 20px;">
    <button class="btn btn-primary"  style="padding: 10px 15px; background-color: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer;" onclick="window.location.href='index.php?vista=registrar_estanteria'">
        <i class="bi bi-plus-lg"></i> Nueva Estantería
    </button>
</div>
<?php endif; ?>
            <!--div class="search-container">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Buscar estantería...">
            </div-->


        </header>
        
        <h1 class="main-title">Gestión de Estanterías</h1>
        <p class="subtitle">Selecciona una estantería para ver su contenido o gestionar los libros</p>
        
        <div class="view-toggle">
            <button class="view-btn active" data-view="grid">
                <i class="bi bi-grid-fill"></i> Cuadrícula
            </button>
            <button class="view-btn" data-view="list">
                <i class="bi bi-list-ul"></i> Lista
            </button>
        </div>
        
        <div class="filter-section">
            <!--div class="filter-group">
                <label class="filter-label">Ordenar por</label>
                <select class="filter-select" id="sort-by">
                    <option>Ubicación</option>
                    <option>Temática</option>
                    <option>Ocupación</option>
                    <option>Recientes</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label class="filter-label">Filtrar por</label>
                <select class="filter-select" id="filter-by">
                    <option>Todas las estanterías</option>
                    <option>Literatura</option>
                    <option>Ciencias</option>
                    <option>Arte</option>
                    <option>Historia</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label class="filter-label">Estado</label>
                <select class="filter-select" id="status-filter">
                    <option>Todos los estados</option>
                    <option>Óptimo</option>
                    <option>En mantenimiento</option>
                    <option>Lleno</option>
                </select>
            </div-->
        </div>
        
        <div class="estantes-grid" id="estantes-container">
        <?php foreach ($estanterias as $estanteria): ?>
            <?php 
            $porcentaje_ocupacion = round(($estanteria['ocupacion'] / $estanteria['capacidad']) * 100);
            $clase_estado = strtolower(str_replace(' ', '-', $estanteria['estado']));
            ?>
            
            <div class="estante-card" data-id="<?= $estanteria['id'] ?>" data-tematica="<?= $estanteria['tematica'] ?>" data-ocupacion="<?= $porcentaje_ocupacion ?>">
                <a href="index.php?vista=fichaEstanteria&codigo=<?= $estanteria["id"] ?>" style="text-decoration: none;">
                <div class="estante-id">
                    <i class="bi bi-bookmarks"></i> <?= $estanteria['id'] ?>
                </div>
                <h2 class="estante-tematica"><?= $estanteria['tematica'] ?></h2>
                <div class="estante-meta">
                     <div class="meta-item">
                     <i class="bi bi-bookshelf"></i> Capacidad: <?= $estanteria['capacidad'] ?>
                    </div>
                    <!--div class="meta-item">
                        <i class="bi bi-geo-alt"></i> <?= $estanteria['ubicacion'] ?>
                    </div-->
                    <!--div class="meta-item">
                        <i class="bi bi-person"></i> <?= $estanteria['responsable'] ?>
                    </div-->
                    <!--div class="meta-item">
                        <i class="bi bi-calendar"></i> Revisión: <?= date('d/m/Y', strtotime($estanteria['ultima_revision'])) ?>
                    </div-->
                </div>
                <div class="ocupacion-bar">
                    <div class="ocupacion-fill" style="width: <?= $porcentaje_ocupacion ?>%"></div>
                </div>
                <div class="estante-stats">
                    <div class="stat">
                        <div class="stat-value"><?= $porcentaje_ocupacion ?>%</div>
                        <div class="stat-label">Ocupación</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value"><?= $estanteria['libros'] ?></div>
                        <div class="stat-label">Libros</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value"><?= $estanteria['prestados'] ?></div>
                        <div class="stat-label">Prestados</div>
                    </div>
                </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cambiar vista
            const viewButtons = document.querySelectorAll('.view-btn');
            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    viewButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    const viewType = this.dataset.view;
                    const container = document.getElementById('estantes-container');
                    
                    if (viewType === 'list') {
                        container.style.gridTemplateColumns = '1fr';
                        document.querySelectorAll('.estante-card').forEach(card => {
                            card.style.padding = '1.2rem';
                        });
                    } else {
                        container.style.gridTemplateColumns = 'repeat(auto-fill, minmax(280px, 1fr))';
                        document.querySelectorAll('.estante-card').forEach(card => {
                            card.style.padding = '1.5rem';
                        });
                    }
                });
            });
            
            // Búsqueda
            const searchInput = document.querySelector('.search-input');
            const cards = document.querySelectorAll('.estante-card');
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                cards.forEach(card => {
                    const title = card.querySelector('.estante-tematica').textContent.toLowerCase();
                    const id = card.dataset.id.toLowerCase();
                    const tematica = card.dataset.tematica.toLowerCase();
                    
                    if (title.includes(searchTerm) || id.includes(searchTerm) || tematica.includes(searchTerm)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
            
            // Efecto hover suave
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transition = 'all 0.2s ease';
                });
            });
        });
    </script>



<!-- Estilos adicionales -->
<style>
    /* Estilo para los niveles */
    .nivel-item {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 10px;
        align-items: center;
        padding: 10px;
        background-color: white;
        border-radius: 4px;
        border: 1px solid #eee;
    }
    
    /* Estilo para el select de Dewey */
    select {
        appearance: none;

        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 16px;
    }
    

</style>



<script>
(function() {
    const searchComponent = document.getElementById('ejemplarEstanteriaSearch');
    const searchInput = searchComponent.querySelector('.ejemplar-search-field');
    const resultsContainer = searchComponent.querySelector('.ejemplar-results-container');
    
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        
        if (query.length < 2) {
            resultsContainer.style.display = 'none';
            return;
        }
        
        fetch('app/controller/estanterias/c_buscar_ubicacion_ejemplar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ query: query })
        })
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                resultsContainer.innerHTML = '';
                data.forEach(ejemplar => {
                    const item = document.createElement('div');
                    item.className = 'ejemplar-result-item';
                    
                    item.innerHTML = `
                        <img src="${ejemplar.portada}" class="ejemplar-portada" 
                             alt="Portada de ${ejemplar.titulo_libro}">
                        <div class="ejemplar-result-content">
                            <div class="ejemplar-result-title">${ejemplar.titulo_libro}</div>
                            <div class="ejemplar-result-meta">
                                <span>Cota: ${ejemplar.cota}</span>
                                <span>• ${ejemplar.estado_ejemplar}</span>
                                <span>• Estantería ${ejemplar.estanteria} Fila ${ejemplar.numero_fila}</span>
                            </div>
                        </div>
                    `;
                    
                    item.addEventListener('click', () => {
                        window.location.href = `index.php?vista=fichaEstanteria&codigo=${ejemplar.estanteria}`;
                    });
                    
                    resultsContainer.appendChild(item);
                });
                resultsContainer.style.display = 'block';
            } else {
                resultsContainer.innerHTML = '<div class="ejemplar-no-results">No se encontraron ejemplares</div>';
                resultsContainer.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resultsContainer.innerHTML = '<div class="ejemplar-no-results">Error en la búsqueda</div>';
            resultsContainer.style.display = 'block';
        });
    });

    // Resto del código permanece igual
})();
</script>