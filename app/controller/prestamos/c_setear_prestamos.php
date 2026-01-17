<?php
$registrosVariable = 10;
$consultaCount = "SELECT COUNT(*) AS total FROM prestamos;";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $consultaPaginacion = "SELECT 
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
    LEFT JOIN multas AS m ON p.id = m.prestamo_id -- LEFT JOIN para verificar si existe en multas
    ORDER BY p.id = $id DESC LIMIT ?, ?";
} else {
    $consultaPaginacion = "SELECT 
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
    LEFT JOIN multas AS m ON p.id = m.prestamo_id -- LEFT JOIN para verificar si existe en multas
ORDER BY 
    p.id DESC LIMIT ?, ?";
}
