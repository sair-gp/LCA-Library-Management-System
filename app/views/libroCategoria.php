<?php 

require_once "app/config/database.php";
$conexion = conexion();

$consultaPaginacion = "SELECT * FROM editorial LIMIT ?, ?";



$categoria_id = isset($_GET['categoria_id']) ? (int)$_GET['categoria_id'] : 0;

$consulta = "SELECT * FROM categorias";

$resultado = $conexion->query($consulta);


$categorias = [];
if ($resultado->num_rows > 0) {
   
    while ($fila = $resultado->fetch_assoc()) {
        $categorias[$fila["id"]] = [
             'nombre' => $fila['nombre'], 'color' => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT)
        ];

    }
}




/*$categorias = [
    1 => ['nombre' => 'Literatura Clásica', 'color' => '#4a6da7'],
    2 => ['nombre' => 'Ciencia Ficción', 'color' => '#5a8f6b'],
    3 => ['nombre' => 'Historia', 'color' => '#a04a6d'],
    4 => ['nombre' => 'Ciencias Naturales', 'color' => '#6d4a6d'],
    5 => ['nombre' => 'Arte y Fotografía', 'color' => '#4a6d8f']
];*/




if (!isset($categorias[$categoria_id])) {
   // header("Location: categorias.php");
    exit();
}



$consulta = "SELECT l.isbn, l.titulo, au.nombre as autor, l.anio, l.portada FROM libros l LEFT JOIN libro_categoria lc ON lc.isbn_libro = l.isbn LEFT JOIN categorias c ON c.id = lc.id_categoria LEFT JOIN libro_autores la ON la.isbn_libro = l.isbn LEFT JOIN autores au ON au.id = la.id_autor WHERE c.id = $categoria_id GROUP BY l.isbn, l.titulo";

$resultado = $conexion->query($consulta);


$libros = [];
$i = 1;
while ($fila = $resultado->fetch_assoc()) {
    $libros[] = [
        'id' => $i,
        'titulo' => $fila["titulo"],
        'autor' => $fila["autor"],
        'anio' => $fila["anio"],
        'categoria_id' => $categoria_id,
        'isbn' => $fila["isbn"],
        'portada' => $fila["portada"]
    ];
    $i++;
}

$libros_categoria = array_filter($libros, function($libro) use ($categoria_id) {
    return $libro['categoria_id'] == $categoria_id;
});

$libros_json = json_encode(array_values($libros_categoria));
?>
<br>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros de <?= htmlspecialchars($categorias[$categoria_id]['nombre']) ?></title>
    <style>
      :root {
    --color-primario: <?= $categorias[$categoria_id]['color'] ?>;
    --color-fondo: #f8f9fa;
    --color-texto: #333;
    --color-texto-claro: #777;
    --color-borde: #e0e0e0;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: var(--color-fondo);
    margin: 0;
    padding: 0;
    color: var(--color-texto);
    line-height: 1.6;
}

.contenedor-libros {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.header-categoria {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 2px solid var(--color-borde);
}

.titulo-categoria {
    font-size: 2.2rem;
    color: var(--color-primario);
    margin: 0;
    font-weight: 600;
}

.contador-libros {
    font-size: 1.1rem;
    color: var(--color-texto-claro);
    margin-top: 10px;
}

.herramientas-libros {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 15px;
}

.busqueda-libros {
    flex-grow: 1;
    position: relative;
    max-width: 500px;
}

.busqueda-libros input {
    width: 100%;
    padding: 12px 20px 12px 45px;
    border: 2px solid var(--color-borde);
    border-radius: 30px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.busqueda-libros input:focus {
    outline: none;
    border-color: var(--color-primario);
}

.busqueda-libros i {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--color-primario);
}

.filtro-orden {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filtro-orden select {
    padding: 10px 15px;
    border-radius: 20px;
    border: 2px solid var(--color-borde);
    background-color: white;
    cursor: pointer;
    font-size: 0.95rem;
}

/* NUEVO ESTILO PARA EL GRID DE LIBROS */
.grid-libros {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
}

/* ESTILOS ACTUALIZADOS PARA LAS TARJETAS */
.tarjeta-libro {
    background-color: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid var(--color-borde);
    display: flex;
    flex-direction: column;
    height: 100%;
}

.tarjeta-libro:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    border-color: var(--color-primario);
}

.tarjeta-libro a {
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* ESTILO MEJORADO PARA LAS PORTADAS */
.portada-libro {
    height: 0;
    padding-top: 150%; /* Proporción 3:2 */
    position: relative;
    background-color: #f0f0f0;
    background-size: contain;
    background-position: center;
    background-repeat: no-repeat;
    border-bottom: 1px solid var(--color-borde);
}

.contenido-libro {
    padding: 15px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.titulo-libro {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 5px 0;
    color: var(--color-texto);
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.autor-libro {
    font-size: 0.85rem;
    color: var(--color-primario);
    margin: 0 0 8px 0;
    font-weight: 500;
}

.meta-libro {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    color: var(--color-texto-claro);
    margin-top: auto;
}

.sin-resultados {
    grid-column: 1 / -1;
    text-align: center;
    padding: 50px 20px;
    color: var(--color-texto-claro);
}

.sin-resultados i {
    font-size: 3rem;
    opacity: 0.3;
    margin-bottom: 20px;
    display: block;
}

.configuracion-paginacion {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
    flex-wrap: wrap;
    gap: 15px;
}

.selector-items {
    display: flex;
    align-items: center;
    gap: 10px;
}

.selector-items select {
    padding: 8px 12px;
    border-radius: 5px;
    border: 1px solid var(--color-borde);
}

.contador-items {
    font-size: 0.9rem;
    color: var(--color-texto-claro);
}

.paginacion-avanzada {
    display: flex;
    gap: 5px;
}

.btn-pagina {
    padding: 8px 12px;
    border: 1px solid var(--color-borde);
    background: white;
    cursor: pointer;
    border-radius: 5px;
    min-width: 40px;
    text-align: center;
}

.btn-pagina:hover {
    background-color: var(--color-primario);
    color: white;
    border-color: var(--color-primario);
}

.btn-pagina.activa {
    background-color: var(--color-primario);
    color: white;
    border-color: var(--color-primario);
}

.btn-pagina.deshabilitada {
    opacity: 0.5;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .grid-libros {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
    }
    
    .herramientas-libros {
        flex-direction: column;
    }
    
    .busqueda-libros {
        max-width: 100%;
    }
    
    .configuracion-paginacion {
        flex-direction: column;
        align-items: stretch;
    }
    
    .paginacion-avanzada {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .titulo-libro {
        font-size: 0.9rem;
    }
    
    .autor-libro {
        font-size: 0.8rem;
    }
    
    .meta-libro {
        font-size: 0.75rem;
    }
}
    </style>
    
</head>
<body>
    <div class="contenedor-libros">
        <div class="header-categoria">
            <h1 class="titulo-categoria"><?= htmlspecialchars($categorias[$categoria_id]['nombre']) ?></h1>
            <div class="contador-libros"><span id="contadorTotal"><?= count($libros_categoria) ?></span> libros en esta categoría</div>
        </div>
        
        <div class="herramientas-libros">
            <div class="busqueda-libros">
                <i class="fas fa-search"></i>
                <input type="text" id="busqueda" placeholder="Buscar por título o autor...">
            </div>
            
            <div class="filtro-orden">
                <span>Ordenar por:</span>
                <select id="orden">
                    <option value="titulo">Título (A-Z)</option>
                    <option value="titulo-desc">Título (Z-A)</option>
                    <option value="autor">Autor (A-Z)</option>
                    <option value="anio">Año (más reciente)</option>
                    <option value="anio-asc">Año (más antiguo)</option>
                </select>
            </div>
        </div>
        
        <div class="grid-libros" id="gridLibros"></div>
        
        <div class="configuracion-paginacion">
            <div class="selector-items">
                <span>Mostrar:</span>
                <select id="itemsPorPagina">
                    <option value="10">10</option>
                    <option value="20" selected>20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>libros por página</span>
            </div>
            
            <div class="contador-items">
                Mostrando <span id="contadorMostrando">1-20</span> de <span id="contadorTotal2"><?= count($libros_categoria) ?></span>
            </div>
            
            <div class="paginacion-avanzada" id="paginacion">
                <button class="btn-pagina deshabilitada" id="btnPrimera">
                    <i class="fas fa-angle-double-left"></i>
                </button>
                <button class="btn-pagina deshabilitada" id="btnAnterior">
                    <i class="fas fa-angle-left"></i>
                </button>
                <div id="numerosPagina" style="display: flex; gap: 5px;"></div>
                <button class="btn-pagina" id="btnSiguiente">
                    <i class="fas fa-angle-right"></i>
                </button>
                <button class="btn-pagina" id="btnUltima">
                    <i class="fas fa-angle-double-right"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Datos y configuración inicial
        const todosLosLibros = <?= $libros_json ?>;
        let librosFiltrados = [...todosLosLibros];
        let paginaActual = 1;
        let itemsPorPagina = 20;
        let criterioOrden = 'titulo';
        
        // Elementos del DOM
        const gridLibros = document.getElementById('gridLibros');
        const inputBusqueda = document.getElementById('busqueda');
        const selectOrden = document.getElementById('orden');
        const selectItemsPorPagina = document.getElementById('itemsPorPagina');
        const contadorMostrando = document.getElementById('contadorMostrando');
        const contadorTotal = document.getElementById('contadorTotal');
        const contadorTotal2 = document.getElementById('contadorTotal2');
        const btnPrimera = document.getElementById('btnPrimera');
        const btnAnterior = document.getElementById('btnAnterior');
        const btnSiguiente = document.getElementById('btnSiguiente');
        const btnUltima = document.getElementById('btnUltima');
        const numerosPagina = document.getElementById('numerosPagina');
        
        // Función para renderizar libros
        function renderizarLibros(libros) {
            gridLibros.innerHTML = '';
            
            if (libros.length === 0) {
                gridLibros.innerHTML = `
                    <div class="sin-resultados">
                        <i class="fas fa-book-open"></i>
                        <h3>No se encontraron libros</h3>
                        <p>Prueba con otros términos de búsqueda</p>
                    </div>`;
                return;
            }
            
            libros.forEach(libro => {
                const tarjeta = document.createElement('div');
                tarjeta.className = 'tarjeta-libro';
                tarjeta.innerHTML = `
                    <a href="index.php?vista=fichaLibro&isbn=${libro.isbn}">
                        <div class="portada-libro" style="background-image: url('${libro.portada}')"></div>
                        <div class="contenido-libro">
                            <h3 class="titulo-libro">${libro.titulo}</h3>
                            <p class="autor-libro">${libro.autor}</p>
                            <div class="meta-libro">
                                <span>${libro.anio}</span>
                                <span>ISBN: ${libro.isbn}</span>
                            </div>
                        </div>
                    </a>`;
                gridLibros.appendChild(tarjeta);
            });
        }
        
        // Función para filtrar libros
        function filtrarLibros() {
            const termino = inputBusqueda.value.toLowerCase();
            
            librosFiltrados = todosLosLibros.filter(libro => {
                const titulo = libro.titulo.toLowerCase();
                const autor = libro.autor.toLowerCase();
                return titulo.includes(termino) || autor.includes(termino);
            });
            
            contadorTotal.textContent = librosFiltrados.length;
            contadorTotal2.textContent = librosFiltrados.length;
            paginaActual = 1;
            ordenarLibros();
        }
        
        // Función para ordenar libros
        function ordenarLibros() {
            librosFiltrados.sort((a, b) => {
                if (criterioOrden === 'titulo') {
                    return a.titulo.localeCompare(b.titulo);
                } else if (criterioOrden === 'titulo-desc') {
                    return b.titulo.localeCompare(a.titulo);
                } else if (criterioOrden === 'autor') {
                    return a.autor.localeCompare(b.autor);
                } else if (criterioOrden === 'anio') {
                    return b.anio - a.anio;
                } else {
                    return a.anio - b.anio;
                }
            });
            
            actualizarPaginacion();
        }
        
        // Función para actualizar la paginación
        function actualizarPaginacion() {
            const totalPaginas = Math.ceil(librosFiltrados.length / itemsPorPagina);
            const inicio = (paginaActual - 1) * itemsPorPagina;
            const fin = Math.min(inicio + itemsPorPagina, librosFiltrados.length);
            const librosPagina = librosFiltrados.slice(inicio, fin);
            
            contadorMostrando.textContent = `${inicio + 1}-${fin}`;
            
            btnPrimera.className = paginaActual > 1 ? 'btn-pagina' : 'btn-pagina deshabilitada';
            btnAnterior.className = paginaActual > 1 ? 'btn-pagina' : 'btn-pagina deshabilitada';
            btnSiguiente.className = paginaActual < totalPaginas ? 'btn-pagina' : 'btn-pagina deshabilitada';
            btnUltima.className = paginaActual < totalPaginas ? 'btn-pagina' : 'btn-pagina deshabilitada';
            
            numerosPagina.innerHTML = '';
            
            const maxBotones = 5;
            let inicioPaginas = Math.max(1, paginaActual - Math.floor(maxBotones / 2));
            let finPaginas = Math.min(totalPaginas, inicioPaginas + maxBotones - 1);
            
            if (finPaginas - inicioPaginas + 1 < maxBotones) {
                inicioPaginas = Math.max(1, finPaginas - maxBotones + 1);
            }
            
            if (inicioPaginas > 1) {
                const btn = document.createElement('button');
                btn.className = 'btn-pagina';
                btn.textContent = '1';
                btn.addEventListener('click', () => cambiarPagina(1));
                numerosPagina.appendChild(btn);
                
                if (inicioPaginas > 2) {
                    const puntos = document.createElement('span');
                    puntos.textContent = '...';
                    puntos.style.padding = '8px 12px';
                    numerosPagina.appendChild(puntos);
                }
            }
            
            for (let i = inicioPaginas; i <= finPaginas; i++) {
                const btn = document.createElement('button');
                btn.className = i === paginaActual ? 'btn-pagina activa' : 'btn-pagina';
                btn.textContent = i;
                btn.addEventListener('click', () => cambiarPagina(i));
                numerosPagina.appendChild(btn);
            }
            
            if (finPaginas < totalPaginas) {
                if (finPaginas < totalPaginas - 1) {
                    const puntos = document.createElement('span');
                    puntos.textContent = '...';
                    puntos.style.padding = '8px 12px';
                    numerosPagina.appendChild(puntos);
                }
                
                const btn = document.createElement('button');
                btn.className = 'btn-pagina';
                btn.textContent = totalPaginas;
                btn.addEventListener('click', () => cambiarPagina(totalPaginas));
                numerosPagina.appendChild(btn);
            }
            
            renderizarLibros(librosPagina);
        }
        
        // Función para cambiar de página
        function cambiarPagina(nuevaPagina) {
            paginaActual = nuevaPagina;
            actualizarPaginacion();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        // Event listeners
        inputBusqueda.addEventListener('input', filtrarLibros);
        selectOrden.addEventListener('change', function() {
            criterioOrden = this.value;
            ordenarLibros();
        });
        selectItemsPorPagina.addEventListener('change', function() {
            itemsPorPagina = parseInt(this.value);
            paginaActual = 1;
            actualizarPaginacion();
        });
        btnPrimera.addEventListener('click', () => cambiarPagina(1));
        btnAnterior.addEventListener('click', () => cambiarPagina(paginaActual - 1));
        btnSiguiente.addEventListener('click', () => cambiarPagina(paginaActual + 1));
        btnUltima.addEventListener('click', () => {
            const totalPaginas = Math.ceil(librosFiltrados.length / itemsPorPagina);
            cambiarPagina(totalPaginas);
        });
        
        // Inicializar
        document.addEventListener('DOMContentLoaded', () => {
            filtrarLibros();
        });
    </script>
</body>
</html>