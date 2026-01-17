<?php //$registrosVariable = 10;
//$consultaCount = "SELECT COUNT(*) AS total FROM categorias;";
//$consultaPaginacion = "SELECT * FROM categorias LIMIT ?, ?";
//include "app/controller/c_paginacion.php";

require_once "app/config/database.php";
$conexion = conexion();

include "modal/agregar_categoria.php";


$consulta = "SELECT 
    ca.id, 
    ca.nombre, 
    COUNT(l.isbn) AS total_libros
FROM 
    categorias ca
LEFT JOIN 
    libro_categoria lc ON lc.id_categoria = ca.id
LEFT JOIN 
    libros l ON l.isbn = lc.isbn_libro
GROUP BY 
    ca.id, ca.nombre;";

$resultado = $conexion->query($consulta);

$todasLasCategorias = [];

while ($fila = $resultado->fetch_assoc()){
$todasLasCategorias[] = [
    "id"=> $fila["id"],
    "nombre"=> $fila["nombre"],
    "total_libros" => $fila["total_libros"]
];
}

// Todas las categorías


// Convertir a JSON para JS
$categoriasJson = json_encode($todasLasCategorias);
?>
<br><br>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Categorías</title>
    <link rel="stylesheet" href="public/css/newCategorias.css">
    <style>
        /* Tus estilos CSS existentes */
        :root {
            --color-primario: #2c3e50;
            --color-acento: #3498db;
            --color-fondo: #f8f9fa;
            /* ... (mantén tus estilos actuales) */
        }
        
        .paginacion .cargando {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
    
</head>
<body>
    <div class="contenedor">
        <div class="header">
            <h1 class="titulo-pagina">Gestión de Categorías</h1>
            <button class="boton-primario btn-registrar" id="btnNuevaCategoria" data-bs-toggle="modal" data-bs-target="#agregarCategoria">
                <i class="fas fa-plus"></i> Nueva Categoría
            </button>
        </div>
        
        <div class="barra-herramientas">
            <div class="busqueda">
                <i class="fas fa-search"></i>
                <input type="text" id="busqueda" placeholder="Buscar categorías...">
            </div>
        </div>
        
        <!-- Contenedor para categorías -->
        <div class="grid-categorias" id="gridCategorias"></div>
        
        <!-- Paginación -->
        <div class="paginacion" id="paginacion">
            <span class="deshabilitada" id="btnAnterior">
                <i class="fas fa-chevron-left"></i>
            </span>
            
            <div id="numerosPagina"></div>
            
            <span class="deshabilitada" id="btnSiguiente">
                <i class="fas fa-chevron-right"></i>
            </span>
        </div>
    </div>

    <script>
    // Configuración
    const itemsPorPagina = 8;
    let paginaActual = 1;
    let totalPaginas = Math.ceil(<?= count($todasLasCategorias) ?> / itemsPorPagina);
    let categoriasFiltradas = <?= $categoriasJson ?>;
    let busquedaActiva = false;

    // Elementos DOM
    const gridCategorias = document.getElementById('gridCategorias');
    const inputBusqueda = document.getElementById('busqueda');
    const btnAnterior = document.getElementById('btnAnterior');
    const btnSiguiente = document.getElementById('btnSiguiente');
    const numerosPagina = document.getElementById('numerosPagina');

    // Función para renderizar categorías
    function renderizarCategorias(categorias) {
        gridCategorias.innerHTML = '';
        
        if (categorias.length === 0) {
            gridCategorias.innerHTML = `
                <div class="estado-vacio">
                    <i class="fas fa-bookmark"></i>
                    <h3>No se encontraron categorías</h3>
                    <p>Intenta con otro término de búsqueda</p>
                </div>`;
            return;
        }
        
        categorias.forEach(categoria => {
            const color = generarColor(categoria.id);
            const tarjeta = document.createElement('div');
            tarjeta.className = 'tarjeta-categoria';
            tarjeta.innerHTML = `
                <div class="cabecera-tarjeta" style="--color-cabecera: ${color}"></div>
                <div class="contenido-tarjeta">
                    <h3 class="nombre-categoria">${categoria.nombre}</h3>
                    <div class="meta-categoria">
                        <span>ID: ${categoria.id}</span>
                        <span class="contador-libros">
                            <i class="fas fa-book"></i> ${categoria.total_libros} libros
                        </span>
                    </div>
                    <div class="acciones-tarjeta">
                        <button class="boton-accion btn-editar" data-id="${categoria.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="boton-accion btn-libros" data-id="${categoria.id}">
                            <i class="fas fa-book-open"></i>
                        </button>
                    </div>
                </div>`;
            
            gridCategorias.appendChild(tarjeta);
        });
        
        // Agregar eventos a los botones
        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                window.location.href = `editar_categoria.php?id=${btn.dataset.id}`;
            });
        });
        
        document.querySelectorAll('.btn-libros').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                window.location.href = `index.php?vista=libroCategoria&categoria_id=${btn.dataset.id}`;
            });
        });
        
        // Evento de click en la tarjeta
        document.querySelectorAll('.tarjeta-categoria').forEach(tarjeta => {
            tarjeta.addEventListener('click', (e) => {
                if (!e.target.closest('.boton-accion')) {
                    const id = tarjeta.querySelector('.btn-libros').dataset.id;
                    window.location.href = `index.php?vista=libroCategoria&categoria_id=${id}`;
                }
            });
        });
    }

    // Función para generar color
    function generarColor(id) {
        const tonos = ['#4a6da7', '#5a8f6b', '#a04a6d', '#6d4a6d', '#4a6d8f'];
        return tonos[id % tonos.length];
    }

    // Función para actualizar paginación
    function actualizarPaginacion() {
        // Calcular índices
        const inicio = (paginaActual - 1) * itemsPorPagina;
        const fin = inicio + itemsPorPagina;
        const categoriasPagina = categoriasFiltradas.slice(inicio, fin);
        
        // Renderizar categorías
        renderizarCategorias(categoriasPagina);
        
        // Actualizar controles de paginación
        btnAnterior.className = paginaActual > 1 ? '' : 'deshabilitada';
        btnSiguiente.className = paginaActual < totalPaginas ? '' : 'deshabilitada';
        
        // Actualizar números de página
        numerosPagina.innerHTML = '';
        const maxBotones = 5;
        let inicioPaginas = Math.max(1, paginaActual - Math.floor(maxBotones / 2));
        let finPaginas = Math.min(totalPaginas, inicioPaginas + maxBotones - 1);
        
        // Ajustar si estamos al final
        if (finPaginas - inicioPaginas + 1 < maxBotones) {
            inicioPaginas = Math.max(1, finPaginas - maxBotones + 1);
        }
        
        for (let i = inicioPaginas; i <= finPaginas; i++) {
            const btn = document.createElement('a');
            btn.href = '#';
            btn.textContent = i;
            if (i === paginaActual) {
                btn.className = 'activa';
            }
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                cambiarPagina(i);
            });
            numerosPagina.appendChild(btn);
        }
    }

    // Función para cambiar de página
    function cambiarPagina(nuevaPagina) {
        if (nuevaPagina < 1 || nuevaPagina > totalPaginas || nuevaPagina === paginaActual) return;
        
        paginaActual = nuevaPagina;
        actualizarPaginacion();
        
        // Scroll suave al principio
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    // Evento de búsqueda
    inputBusqueda.addEventListener('input', function() {
        const termino = this.value.toLowerCase();
        
        if (termino === '') {
            busquedaActiva = false;
            categoriasFiltradas = <?= $categoriasJson ?>;
        } else {
            busquedaActiva = true;
            categoriasFiltradas = <?= $categoriasJson ?>.filter(cat => 
                cat.nombre.toLowerCase().includes(termino)
            );
        }
        
        // Recalcular paginación
        totalPaginas = Math.ceil(categoriasFiltradas.length / itemsPorPagina);
        paginaActual = 1;
        actualizarPaginacion();
    });

    // Eventos de paginación
    btnAnterior.addEventListener('click', () => cambiarPagina(paginaActual - 1));
    btnSiguiente.addEventListener('click', () => cambiarPagina(paginaActual + 1));

    // Inicializar
    document.addEventListener('DOMContentLoaded', () => {
        actualizarPaginacion();
        
        // Evento para nuevo botón
        /*document.getElementById('btnNuevaCategoria').addEventListener('click', () => {
            window.location.href = 'agregar_categoria.php';
        });*/
    });
    </script>
</body>
</html>