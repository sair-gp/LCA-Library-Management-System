<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión a la base de datos
include '../../../app/config/database.php';

$conexion = conexion();



if (isset($_GET['term'], $_GET['idTabla'])) {
    $term = '%' . $conexion->real_escape_string($_GET['term']) . '%';
    $idTabla = $_GET['idTabla'];

    // Switch para manejar las tablas permitidas
    $consulta = '';
    switch ($idTabla) {
        case 'tablaVisitantes':
            $consulta = "SELECT cedula, nombre, telefono, direccion, CASE WHEN sexo = 1 THEN 'Masculino' WHEN sexo = 2 THEN 'Femenino' ELSE 'Otro' END AS sexo_descripcion FROM visitantes WHERE cedula LIKE ? OR nombre LIKE ? OR telefono LIKE ? OR direccion LIKE ? OR CASE 
                WHEN sexo = 1 THEN 'Masculino' 
                WHEN sexo = 2 THEN 'Femenino' 
                ELSE 'Otro' 
            END LIKE ?";
            break;
        case 'tablaAutores':
            $consulta = "SELECT id, nombre, foto FROM autores 
                         WHERE nombre LIKE ?";
            break;
        case 'tablaLibros':
            $consulta = "SELECT libros.isbn, libros.titulo, GROUP_CONCAT(DISTINCT autores.nombre SEPARATOR ', ') AS autores, libros.anio, editorial.nombre AS editorialN, libros.volumen, libros.edicion, GROUP_CONCAT(DISTINCT categorias.nombre SEPARATOR ', ') AS categorias, (SELECT COUNT(*) FROM ejemplares WHERE ejemplares.isbn_copia = libros.isbn) AS cantidad_ejemplares FROM libros JOIN libro_autores ON libros.isbn = libro_autores.isbn_libro JOIN autores ON libro_autores.id_autor = autores.id JOIN editorial ON libros.editorial = editorial.id JOIN libro_categoria ON libros.isbn = libro_categoria.isbn_libro JOIN categorias ON libro_categoria.id_categoria = categorias.id WHERE libros.delete_at = 0 AND (libros.isbn LIKE ? OR libros.titulo LIKE ? OR autores.nombre LIKE ? OR libros.anio LIKE ? OR editorial.nombre LIKE ? OR libros.edicion LIKE ? OR categorias.nombre LIKE ?) GROUP BY libros.isbn ORDER BY libros.isbn;";
            break;
        case 'tablaAsistencias':
            $consulta = "SELECT a.id, v.nombre , a.cedula_visitante, a.origen, a.descripcion, a.fecha FROM asistencias a JOIN visitantes v ON v.cedula = a.cedula_visitante WHERE v.nombre LIKE ? OR a.cedula_visitante LIKE ? OR a.descripcion LIKE ? OR a.origen LIKE ? OR a.fecha LIKE ?";
            break;
        case 'tablaCategorias':
            $consulta = "SELECT id, nombre FROM categorias WHERE nombre LIKE ?";
            break;
        case 'tablaEditoriales':
            $consulta = "SELECT id, nombre, origen FROM editorial WHERE nombre LIKE ? OR origen like ? LIMIT 10";
            break;
        case 'tablaHistorial':
            $consulta = "SELECT h.id, h.fecha, a.descripcion AS accion, h.detalles, CONCAT(u.nombre, ' ', u.apellido) AS nombre FROM historial AS h JOIN acciones AS a ON h.accion_id = a.id JOIN usuarios AS u ON u.cedula = h.cedula_responsable WHERE h.fecha LIKE ? OR a.descripcion LIKE ? OR h.detalles LIKE ? OR u.nombre LIKE ? OR h.id LIKE ? OR u.apellido LIKE ?;";
            break;
        case 'tablaPapelera':
            $consulta = "SELECT libros.isbn, libros.titulo, GROUP_CONCAT(DISTINCT CONCAT(autores.nombre, ' ', autores.apellido) SEPARATOR ', ') AS autores, libros.anio, editorial.nombre AS editorialN, libros.edicion, GROUP_CONCAT(DISTINCT categorias.nombre SEPARATOR ', ') AS categorias, (SELECT COUNT(*) FROM ejemplares WHERE ejemplares.isbn_copia = libros.isbn) AS cantidad_ejemplares FROM libros JOIN libro_autores ON libros.isbn = libro_autores.isbn_libro JOIN autores ON libro_autores.id_autor = autores.id JOIN editorial ON libros.editorial = editorial.id JOIN libro_categoria ON libros.isbn = libro_categoria.isbn_libro JOIN categorias ON libro_categoria.id_categoria = categorias.id WHERE libros.delete_at = 1 AND (libros.isbn LIKE ? OR libros.titulo LIKE ? OR autores.nombre LIKE ? OR autores.apellido LIKE ? OR libros.anio LIKE ? OR editorial.nombre LIKE ? OR libros.edicion LIKE ? OR categorias.nombre LIKE ?) GROUP BY libros.isbn ORDER BY libros.isbn;";
            break;
        case 'tablaPrestamos':
            $consulta = "SELECT 
    p.id, 
    l.isbn, 
    e.cota, 
    v.cedula, 
    v.nombre, 
    p.fecha_inicio, 
    p.fecha_fin, 
    es.id AS verificarBoton, 
    es.estado,
    CASE
        WHEN l.es_obra_completa = 1 THEN
            l.titulo
        ELSE
            CASE
                WHEN REGEXP_REPLACE(vo.nombre, '[0-9]', '') = l.titulo THEN
                    CONCAT(l.titulo, ' ', 'volumen ', vo.numero)
                ELSE
                    CONCAT(l.titulo, ' \"', vo.nombre, '\". ')
            END
    END AS titulo,
    CASE
        WHEN p.fecha_devolucion IS NOT NULL THEN -- Si fecha_devolucion no es NULL, compara con ella
            DATEDIFF(p.fecha_devolucion, p.fecha_fin)
        ELSE -- Si fecha_devolucion es NULL, compara con la fecha actual
            DATEDIFF(CURDATE(), p.fecha_fin)
    END AS diasDeRetraso,
    CASE
        WHEN m.prestamo_id IS NOT NULL THEN 1 -- Si existe en la tabla de multas
        ELSE 0 -- Si no existe
    END AS tieneMulta
FROM 
    prestamos AS p 
    JOIN ejemplares AS e ON p.cota = e.id 
    JOIN libros AS l ON e.isbn_copia = l.isbn 
    JOIN estado_prestamo AS es ON p.estado = es.id 
    JOIN visitantes AS v ON p.lector = v.cedula 
    JOIN volumen AS vo ON e.isbn_vol = vo.id 
    LEFT JOIN multas AS m ON p.id = m.prestamo_id
WHERE 
    p.id LIKE ? OR 
    l.titulo LIKE ? OR 
    e.cota LIKE ? OR 
    v.nombre LIKE ? OR 
    p.fecha_inicio LIKE ? OR 
    p.fecha_fin LIKE ? OR 
    es.estado LIKE ?
ORDER BY 
    p.id DESC;";
            break;
        case 'tablaUsuarios':
            $consulta = "SELECT u.cedula, u.nombre, u.apellido, u.direccion, u.telefono, u.correo, CASE WHEN u.sexo = 1 THEN 'Masculino' WHEN u.sexo = 2 THEN 'Femenino' ELSE 'Otro' END AS sexo_descripcion, r.nombre AS rol_nombre, YEAR(CURRENT_DATE) - YEAR(u.fecha_nacimiento) - (RIGHT(CURRENT_DATE,5) < RIGHT(u.fecha_nacimiento,5)) AS edad FROM usuarios AS u JOIN rol AS r ON u.rol = r.id_rol WHERE u.estado = 1 AND 
            (
            u.cedula LIKE ? 
            OR u.nombre LIKE ? 
            OR u.apellido LIKE ? 
            OR u.fecha_nacimiento LIKE ? 
            OR u.direccion LIKE ? 
            OR u.telefono LIKE ? 
            OR u.correo LIKE ? 
            OR r.nombre LIKE ?
            OR (CASE 
                WHEN u.sexo = 1 THEN 'Masculino' 
                WHEN u.sexo = 2 THEN 'Femenino' 
                ELSE 'Otro' 
            END) LIKE ? 
            OR (YEAR(CURRENT_DATE) - YEAR(u.fecha_nacimiento) - (RIGHT(CURRENT_DATE,5) < RIGHT(u.fecha_nacimiento,5))) LIKE ?
)
";
            break;
        default:
            echo json_encode(['error' => 'Tabla no válida']);
            exit;
    }

    // Preparar la consulta para prevenir inyecciones SQL
    $stmt = $conexion->prepare($consulta);
    if ($stmt) {


        if (strpos($consulta, 'WHERE') !== false) { // Solo si hay filtros en la consulta
            $numParametros = substr_count($consulta, '?'); // Contar cuántos ? hay en la consulta
            $tipos = str_repeat('s', $numParametros); // Construir la cadena de tipos (todas las columnas son de tipo string en este caso)

            // Crear un arreglo con tantos $term como sea necesario
            $parametros = array_fill(0, $numParametros, $term);

            // Vincular los parámetros dinámicamente
            $stmt->bind_param($tipos, ...$parametros);
        }




        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode($data);

        exit;
    } else {
        echo json_encode(['error' => 'Error preparando la consulta']);
        exit;
    }
}
