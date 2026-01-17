<?php
require_once "app/config/database.php";
$conexion = conexion();

$codigo = $_GET["codigo"] ?? 0;

// Consulta para información de la estantería
$consultaEstanteria = "SELECT
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
WHERE es.codigo = '$codigo'
GROUP BY
    es.id, es.codigo, es.descripcion, es.cantidad_filas, es.capacidad_total
ORDER BY
    es.id;";

$resultadoEstanterias = $conexion->query($consultaEstanteria);

// Consulta para libros en la estantería
$consultaLibros = "SELECT 
    libros.isbn,
    libros.titulo,
    libros.cota,
    libros.portada,
    libros.edicion,
    libros.anio,
    GROUP_CONCAT(DISTINCT autores.nombre SEPARATOR ', ') AS autores,
    editorial.nombre AS editorial,
    GROUP_CONCAT(DISTINCT categorias.nombre SEPARATOR ', ') AS categorias,
    
    /* Totales generales */
    (
        SELECT COUNT(DISTINCT e.id)
        FROM ejemplares e
        JOIN fila f ON e.filaID = f.FilaID
        JOIN estanterias es ON f.EstanteriaID = es.id
        WHERE e.isbn_copia = libros.isbn 
        AND es.codigo = '$codigo'
        AND e.estado IN (1, 2)
        AND e.delete_at = 1
    ) AS total_ejemplares,
    
    (
        SELECT COUNT(DISTINCT p.cota)
        FROM prestamos p
        JOIN ejemplares e ON p.cota = e.id
        JOIN fila f ON e.filaID = f.FilaID
        JOIN estanterias es ON f.EstanteriaID = es.id
        WHERE e.isbn_copia = libros.isbn
        AND es.codigo = '$codigo'
        AND p.estado IN (1, 3, 4)
        AND e.delete_at = 1
    ) AS en_circulacion,
    
    (
        SELECT COUNT(DISTINCT e.id)
        FROM ejemplares e
        JOIN fila f ON e.filaID = f.FilaID
        JOIN estanterias es ON f.EstanteriaID = es.id
        WHERE e.isbn_copia = libros.isbn
        AND es.codigo = '$codigo'
        AND e.estado = 3
        AND e.delete_at = 1
    ) AS ejemplares_danados,
    
    /* Información de ubicaciones */
    GROUP_CONCAT(
        DISTINCT CONCAT(
            f.NumeroFila, '::',
            (
                SELECT COUNT(*)
                FROM ejemplares e2
                WHERE e2.isbn_copia = libros.isbn
                AND e2.filaID = f.FilaID
                AND e2.estado IN (1, 2)
                AND e2.delete_at = 1
            ), '::',
            (
                SELECT COUNT(*)
                FROM prestamos p2
                JOIN ejemplares e3 ON p2.cota = e3.id
                WHERE e3.isbn_copia = libros.isbn
                AND e3.filaID = f.FilaID
                AND p2.estado IN (1, 3, 4)
                AND e3.delete_at = 1
            )
        ) SEPARATOR '||'
    ) AS ubicaciones_agrupadas
    
FROM 
    libros
JOIN 
    libro_autores ON libros.isbn = libro_autores.isbn_libro
JOIN 
    autores ON libro_autores.id_autor = autores.id
JOIN 
    editorial ON libros.editorial = editorial.id
JOIN 
    libro_categoria ON libros.isbn = libro_categoria.isbn_libro
JOIN 
    categorias ON libro_categoria.id_categoria = categorias.id
JOIN 
    ejemplares ON libros.isbn = ejemplares.isbn_copia AND ejemplares.delete_at = 1
JOIN 
    fila f ON ejemplares.filaID = f.FilaID
JOIN 
    estanterias es ON f.EstanteriaID = es.id AND es.codigo = '$codigo'
GROUP BY 
    libros.isbn
ORDER BY 
    libros.titulo;";

$resultadoLibros = $conexion->query($consultaLibros);

$estanteria = [];

if ($resultadoEstanterias && $estanteriaInfo = $resultadoEstanterias->fetch_assoc()) {
    $estanteria['id'] = $estanteriaInfo['codigo'];
    $estanteria['tematica'] = $estanteriaInfo['descripcion'];
    $estanteria['capacidad'] = $estanteriaInfo['capacidad'];
    $estanteria['ocupacion'] = $estanteriaInfo['ocupacion'];
    $estanteria['prestamos_activos'] = $estanteriaInfo['prestamos_activos'];
    
    $cantidadLibros = 0;
    $estanteria['libros'] = [];

    if ($resultadoLibros) {
        while ($libroInfo = $resultadoLibros->fetch_assoc()) {
            // Procesamiento de ubicaciones
            $ubicaciones = [];
            if (!empty($libroInfo['ubicaciones_agrupadas'])) {
                $grupos = explode('||', $libroInfo['ubicaciones_agrupadas']);
                foreach ($grupos as $grupo) {
                    list($fila, $ejemplares, $prestados) = explode('::', $grupo);
                    $ubicaciones[] = [
                        'fila' => (int)$fila,
                        'ejemplares' => (int)$ejemplares,
                        'prestados' => (int)$prestados,
                        'disponibles' => (int)$ejemplares - (int)$prestados
                    ];
                }
            }
            
            // Ordenar por número de fila
            usort($ubicaciones, function($a, $b) {
                return $a['fila'] <=> $b['fila'];
            });
            
            $disponibles = $libroInfo['total_ejemplares'] - $libroInfo['en_circulacion'];
            
            // Dentro del while que procesa $resultadoLibros
$estanteria['libros'][] = [
    'id' => $libroInfo['cota'],
    'titulo' => $libroInfo['titulo'],
    'autor' => $libroInfo['autores'],
    'isbn' => $libroInfo['isbn'],
    'portada' => $libroInfo['portada'],
    'edicion' => $libroInfo['edicion'],
    'anio' => $libroInfo['anio'],
    'editorial' => $libroInfo['editorial'],
    'categorias' => $libroInfo['categorias'],
    'ejemplares' => $libroInfo['total_ejemplares'],
    'en_circulacion' => $libroInfo['en_circulacion'],
    'danados' => $libroInfo['ejemplares_danados'],
    'disponibles' => $disponibles,
    'ubicaciones' => $ubicaciones,
    'posicion' => generarTextoUbicacion($ubicaciones),
    'estado_fisico' => $libroInfo['ejemplares_danados'] > 0 ? 'Dañado' : ($disponibles > 0 ? 'Disponible' : 'Prestado')
];
            
            $cantidadLibros += $libroInfo['total_ejemplares'];
        }
        $estanteria["cantidadTotal"] = $cantidadLibros;
        $resultadoLibros->free();
    }

    $resultadoEstanterias->free();
}

// Funciones helper
function calcularPorcentajeOcupacion($estanteria) {
    if ($estanteria['capacidad'] == 0) return 0;
    return round(($estanteria['cantidadTotal'] / $estanteria['capacidad']) * 100);
}

function calcularLibrosPrestados($estanteria) {
    $prestados = 0;
    foreach ($estanteria['libros'] as $libro) {
        $prestados += $libro['en_circulacion'];
    }
    return $prestados;
}

function calcularLibrosDanados($estanteria) {
    $danados = 0;
    foreach ($estanteria['libros'] as $libro) {
        $danados += $libro['danados'];
    }
    return $danados;
}

function obtenerIconoEstado($estado) {
    switch (strtolower($estado)) {
        case 'excelente': return 'bi-check-circle-fill';
        case 'bueno': return 'bi-check-circle';
        case 'regular': return 'bi-exclamation-circle';
        case 'dañado': return 'bi-exclamation-triangle-fill';
        case 'disponible': return 'bi-check-circle';
        case 'prestado': return 'bi-arrow-up-circle';
        default: return 'bi-question-circle';
    }
}

function generarTextoUbicacion(array $ubicaciones): string {
    $count = count($ubicaciones);
    
    if ($count === 0) return 'Sin ubicación';
    if ($count === 1) return 'Fila ' . $ubicaciones[0]['fila'];
    
    $filas = array_map(function($ubic) {
        return $ubic['fila'];
    }, $ubicaciones);
    
    return 'Filas ' . implode(', ', $filas) . " ($count ubicaciones)";
}