<?php
require_once "app/config/database.php";
$conexion = conexion();
// Verificamos si se ha pasado una cédula por GET
if (!isset($_GET['cedula'])) {
   // header("Location: index.php?vista=usuarios");
    exit();
}

$cedula = $_GET['cedula'];

// Consulta para obtener datos del usuario
$consultaUsuario = "SELECT u.foto_perfil, u.cedula, u.nombre, u.apellido, u.fecha_nacimiento, 
                    u.direccion, u.telefono, u.correo, 
                    CASE WHEN u.sexo = 1 THEN 'Masculino' WHEN u.sexo = 2 THEN 'Femenino' ELSE 'Otro' END AS sexo_descripcion, 
                    r.nombre AS rol_nombre, 
                    YEAR(CURRENT_DATE) - YEAR(u.fecha_nacimiento) - (RIGHT(CURRENT_DATE,5) < RIGHT(u.fecha_nacimiento,5)) AS edad 
                    FROM usuarios AS u 
                    JOIN rol AS r ON u.rol = r.id_rol 
                    WHERE u.cedula = ? AND u.estado = 1";

$stmt = $conexion->prepare($consultaUsuario);
$stmt->bind_param("s", $cedula);
$stmt->execute();
$resultadoUsuario = $stmt->get_result();
$usuario = $resultadoUsuario->fetch_assoc();

if (!$usuario) {
    echo "<script>alert('Usuario no encontrado'); window.location.href = 'index.php?vista=usuarios';</script>";
    exit();
}

// Consulta para obtener el historial del usuario
$consultaHistorial = "SELECT h.id, h.fecha, a.descripcion AS accion, h.detalles 
                     FROM historial AS h 
                     JOIN acciones AS a ON h.accion_id = a.id 
                     WHERE h.cedula_responsable = ? 
                     ORDER BY h.fecha DESC 
                     LIMIT 50";

$stmtHistorial = $conexion->prepare($consultaHistorial);
$stmtHistorial->bind_param("s", $cedula);
$stmtHistorial->execute();
$resultadoHistorial = $stmtHistorial->get_result();
?>
<br><br>
<div class="profile-container">
    <!-- Header del perfil -->
    <div class="profile-header-simple">
        <div class="profile-info">
            <div class="profile-picture">
                <?php $imagen = $usuario["foto_perfil"] != NULL ? "data:image/jpeg;base64," . base64_encode($usuario['foto_perfil']) : "public/img/userDefault.jpg"; ?>
                <img src="<?php echo $imagen ?>" alt="Foto de perfil" class="profile-img">
            </div>
            
            <div class="profile-details">
                <h1><?php echo $usuario['nombre'] . ' ' . $usuario['apellido']; ?></h1>
                <p class="profile-role"><i class="bi bi-person-badge"></i> <?php echo $usuario['rol_nombre']; ?></p>
                <p class="profile-email"><i class="bi bi-envelope"></i> <?php echo $usuario['correo']; ?></p>
                
                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $resultadoHistorial->num_rows; ?></span>
                        <span class="stat-label">Actividades</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $usuario['edad']; ?></span>
                        <span class="stat-label">Años</span>
                    </div>
                    <!--div class="stat-item">
                        <span class="stat-number"><?php echo $usuario['cedula']; ?></span>
                        <span class="stat-label">Cédula</span>
                    </div-->
                </div>
            </div>
            
            <div class="profile-actions">
         
                <a href="index.php?vista=usuarios" class="btn btn-back">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
    
    <!-- Contenido principal del perfil -->
    <div class="profile-content">
        <div class="profile-grid-container">
            <!-- Columna izquierda - Información personal -->
            <div class="profile-grid-column">
                <div class="card profile-card">
                    <div class="card-header">
                        <h3><i class="bi bi-info-circle"></i> Información Personal</h3>
                    </div>
                    <div class="card-body">
                        <ul class="info-list">
                            <li>
                                <span class="info-label"><i class="bi bi-credit-card"></i> Cédula:</span>
                                <span class="info-value"><?php echo $usuario['cedula']; ?></span>
                            </li>
                            <li>
                                <span class="info-label"><i class="bi bi-gender-ambiguous"></i> Sexo:</span>
                                <span class="info-value"><?php echo $usuario['sexo_descripcion']; ?></span>
                            </li>
                            <li>
                                <span class="info-label"><i class="bi bi-calendar"></i> Edad:</span>
                                <span class="info-value"><?php echo $usuario['edad']; ?> años</span>
                            </li>
                            <li>
                                <span class="info-label"><i class="bi bi-telephone"></i> Teléfono:</span>
                                <span class="info-value"><?php echo $usuario['telefono']; ?></span>
                            </li>
                            <li>
                                <span class="info-label"><i class="bi bi-geo-alt"></i> Dirección:</span>
                                <span class="info-value"><?php echo $usuario['direccion']; ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Estadísticas rápidas -->
                <div class="card stats-card">
                    <div class="card-header">
                        <h3><i class="bi bi-bar-chart"></i> Estadísticas</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        // Consulta para estadísticas específicas
                        $consultaStats = "SELECT 
                                        SUM(CASE WHEN a.descripcion LIKE '%Registro de préstamo%' THEN 1 ELSE 0 END) AS total_prestamos,
                                        SUM(CASE WHEN a.descripcion LIKE '%devolución%' THEN 1 ELSE 0 END) AS total_devoluciones,
                                        SUM(CASE WHEN a.descripcion LIKE '%registro%' THEN 1 ELSE 0 END) AS total_registros
                                        FROM historial AS h 
                                        JOIN acciones AS a ON h.accion_id = a.id 
                                        WHERE h.cedula_responsable = ?";
                        
                        $stmtStats = $conexion->prepare($consultaStats);
                        $stmtStats->bind_param("s", $cedula);
                        $stmtStats->execute();
                        $resultadoStats = $stmtStats->get_result();
                        $stats = $resultadoStats->fetch_assoc();
                        ?>
                        
                        <div class="stat-chart">
                            <div class="stat-item-circle">
                                <div class="circle-progress" data-value="<?php echo $stats['total_prestamos'] ?? 0; ?>">
                                    <svg class="circle-chart" viewBox="0 0 36 36">
                                        <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        <path class="circle-fill" stroke-dasharray="<?php echo ($stats['total_prestamos'] ?? 0) * 10; ?>, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    </svg>
                                    <div class="circle-info">
                                        <span><?php echo $stats['total_prestamos'] ?? 0; ?></span>
                                        <small>Préstamos</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="stat-item-circle">
                                <div class="circle-progress" data-value="<?php echo $stats['total_devoluciones'] ?? 0; ?>">
                                    <svg class="circle-chart" viewBox="0 0 36 36">
                                        <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        <path class="circle-fill" stroke-dasharray="<?php echo ($stats['total_devoluciones'] ?? 0) * 10; ?>, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    </svg>
                                    <div class="circle-info">
                                        <span><?php echo $stats['total_devoluciones'] ?? 0; ?></span>
                                        <small>Devoluciones</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="stat-item-circle">
                                <div class="circle-progress" data-value="<?php echo $stats['total_registros'] ?? 0; ?>">
                                    <svg class="circle-chart" viewBox="0 0 36 36">
                                        <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        <path class="circle-fill" stroke-dasharray="<?php echo ($stats['total_registros'] ?? 0) * 10; ?>, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    </svg>
                                    <div class="circle-info">
                                        <span><?php echo $stats['total_registros'] ?? 0; ?></span>
                                        <small>Registros</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Columna derecha - Historial de actividades -->
            <div class="profile-grid-column">
        <div class="card activity-card">
            <div class="card-header">
                <h3><i class="bi bi-clock-history"></i> Historial de movimientos</h3>
                <div class="search-container">
                    <input type="text" id="searchActivities" class="search-input" placeholder="Buscar en movimientos...">
                    <i class="bi bi-search search-icon"></i>
                </div>
                <div class="header-actions">
                            <button class="btn btn-filter" id="filter-week">
                                <i class="bi bi-calendar-week"></i> Esta semana
                            </button>
                            <button class="btn btn-filter" id="filter-month">
                                <i class="bi bi-calendar-month"></i> Este mes
                            </button>
                            <button class="btn btn-filter active" id="filter-all">
                                <i class="bi bi-calendar-check"></i> Todos
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($resultadoHistorial->num_rows > 0): ?>
                            <div class="timeline">
                                <?php while ($actividad = $resultadoHistorial->fetch_assoc()): 
                                    $fecha = new DateTime($actividad['fecha']);
                                    $hora = $fecha->format('H:i');
                                    $dia = $fecha->format('d M');
                                ?>
                                    <div class="timeline-item" data-date="<?php echo $fecha->format('Y-m-d'); ?>">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <div class="timeline-header">
                                                <span class="timeline-time"><?php //echo $hora; ?></span>
                                                <span class="timeline-day"><?php echo $dia; ?></span>
                                            </div>
                                            <div class="timeline-body">
                                                <h4><?php echo $actividad['accion']; ?></h4>
                                                <p><?php echo $actividad['detalles']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="bi bi-journal-x"></i>
                                <p>No se encontraron actividades registradas</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS para el perfil -->
<link rel="stylesheet" href="public/css/perfilUsuario.css">

<!-- JavaScript para filtros y animaciones -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtros de timeline
    const filterWeek = document.getElementById('filter-week');
    const filterMonth = document.getElementById('filter-month');
    const filterAll = document.getElementById('filter-all');
    const timelineItems = document.querySelectorAll('.timeline-item');
    
    // Filtro para esta semana
    filterWeek.addEventListener('click', function() {
        filterWeek.classList.add('active');
        filterMonth.classList.remove('active');
        filterAll.classList.remove('active');
        
        const today = new Date();
        const oneWeekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
        
        timelineItems.forEach(item => {
            const itemDate = new Date(item.dataset.date);
            item.style.display = itemDate >= oneWeekAgo ? 'block' : 'none';
        });
    });
    
    // Filtro para este mes
    filterMonth.addEventListener('click', function() {
        filterWeek.classList.remove('active');
        filterMonth.classList.add('active');
        filterAll.classList.remove('active');
        
        const today = new Date();
        const oneMonthAgo = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
        
        timelineItems.forEach(item => {
            const itemDate = new Date(item.dataset.date);
            item.style.display = itemDate >= oneMonthAgo ? 'block' : 'none';
        });
    });
    
    // Filtro para todos
    filterAll.addEventListener('click', function() {
        filterWeek.classList.remove('active');
        filterMonth.classList.remove('active');
        filterAll.classList.add('active');
        
        timelineItems.forEach(item => {
            item.style.display = 'block';
        });
    });
    
    // Animación de los círculos de estadísticas
    const circleProgresses = document.querySelectorAll('.circle-progress');
    circleProgresses.forEach(circle => {
        const value = parseInt(circle.dataset.value);
        const circleFill = circle.querySelector('.circle-fill');
        const dashArray = Math.min(value * 10, 100);
        circleFill.style.strokeDasharray = `${dashArray}, 100`;
    });
});


// Añade esto al final de tu script existente
const timeline = document.querySelector('.timeline');

// Efecto al hacer scroll
timeline.addEventListener('scroll', function() {
    this.classList.add('scrolling');
    clearTimeout(this.scrollTimer);
    this.scrollTimer = setTimeout(() => {
        this.classList.remove('scrolling');
    }, 500);
    
    // Verificar si llegó al final
    const isAtEnd = this.scrollHeight - this.scrollTop === this.clientHeight;
    if (isAtEnd) {
        this.classList.add('reached-end');
        setTimeout(() => this.classList.remove('reached-end'), 2000);
    }
});

// Efecto hover para items cerca del scroll
timeline.addEventListener('mousemove', function(e) {
    const scrollThumb = this.querySelector('::-webkit-scrollbar-thumb');
    if (scrollThumb) {
        const rect = this.getBoundingClientRect();
        const isNearScroll = e.clientX > rect.right - 15;
        this.style.cursor = isNearScroll ? 'pointer' : 'default';
    }
});




//buscador
// JavaScript corregido para el buscador
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchActivities');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim().toLowerCase();
            const activities = document.querySelectorAll('.timeline-item');
            
            activities.forEach(activity => {
                const textContent = activity.textContent.toLowerCase();
                const matches = textContent.includes(searchTerm);
                
                // Mostrar/ocultar basado en coincidencias
                activity.style.display = matches ? 'block' : 'none';
                
                // Resaltar texto (opcional)
                if (searchTerm.length > 2) {
                    highlightSearchResults(activity, searchTerm);
                } else {
                    removeHighlights(activity);
                }
            });
        });
    }
    
    function highlightSearchResults(element, term) {
        const regex = new RegExp(term, 'gi');
        const content = element.innerHTML;
        const highlighted = content.replace(
            regex, 
            match => `<span class="highlight">${match}</span>`
        );
        element.innerHTML = highlighted;
    }
    
    function removeHighlights(element) {
        const highlights = element.querySelectorAll('.highlight');
        highlights.forEach(highlight => {
            const parent = highlight.parentNode;
            parent.replaceChild(document.createTextNode(highlight.textContent), highlight);
        });
    }
});





</script>