<?php
require_once "app/config/database.php";
$conexion = conexion();
require_once "app/controller/autores/c_setup_fichaAutor.php";
?>
<br><br>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($autor['nombre']); ?> - Perfil de Autor</title>
    <link rel="stylesheet" href="public/css/fichaAutor.css">
</head>
<body>
    <div class="content-wrapper">
        <!-- Sección del Autor - Mejorada visualmente pero misma estructura -->
        <section class="author-profile">
            <div class="autor-container">
                <div class="autor-avatar">
                    <img src="<?php echo htmlspecialchars($autor['foto']); ?>" 
                         alt="Foto de <?php echo htmlspecialchars($autor['nombre']); ?>" 
                         class="autor-foto"
                         onerror="this.src='public/images/default-author.jpg'">
                </div>
                <div class="autor-info">
                    <h1><?php echo htmlspecialchars($autor['nombre']); ?></h1>
                    
                    <?php if (!empty($autor['biografia'])): ?>
                    <div class="autor-bio">
                        <p><?php echo nl2br(htmlspecialchars($autor['biografia'])); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="autor-meta">
                        <?php if (!empty($autor['fecha_nacimiento'])): ?>
                        <div class="meta-item">
                            <i class="fas fa-calendar-day"></i>
                            <span><?php echo htmlspecialchars($autor['fecha_nacimiento']); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($autor['lugar_nacimiento'])): ?>
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars($autor['lugar_nacimiento']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección de Libros - Compatible con tu JS actual -->
        <section class="books-section">
            <div class="libros-table-container">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Buscar obra por título..." 
                           aria-label="Buscar obras del autor">
                </div>

                <table class="libros-table" id="librosTable">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Año de publicación</th>
                            <th>Género</th>
                            <!--th>Extensión</th-->
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="librosBody">
                        <?php if (!empty($libros)): ?>
                            <!-- Tu JS se encargará de renderizar esto -->
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="no-books">El autor no tiene obras registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="pagination" id="pagination">
                    <!-- Tu JS genera la paginación aquí -->
                </div>
            </div>
        </section>
    </div>

    <script>
        // Tu código JavaScript permanece EXACTAMENTE igual
        const libros = <?php echo json_encode($libros); ?>;
        const itemsPerPage = 10;
        let currentPage = 1;
    </script>
    <script src="public/js/fichaAutor.js"></script>
</body>
</html>