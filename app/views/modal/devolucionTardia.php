<?php

$consultaDiasDeRetraso = "
SELECT
    p.id AS 'ID Préstamo',
    v.cedula as 'Cedula del visitante',
    v.nombre AS;'Nombre del Visitante',
    ep.estado,
    CASE
        WHEN p.fecha_devolucion IS NOT NULL THEN -- Si fecha_devolucion no es NULL, compara con ella
            CASE
                WHEN DATEDIFF(p.fecha_devolucion, p.fecha_fin) < 7 THEN CONCAT(DATEDIFF(p.fecha_devolucion, p.fecha_fin), ' días')
                WHEN DATEDIFF(p.fecha_devolucion, p.fecha_fin) < 30 THEN CONCAT(FLOOR(DATEDIFF(p.fecha_devolucion, p.fecha_fin) / 7), ' semanas')
                ELSE CONCAT(FLOOR(DATEDIFF(p.fecha_devolucion, p.fecha_fin) / 30), ' meses')
            END
        ELSE -- Si fecha_devolucion es NULL, compara con la fecha actual
            CASE
                WHEN DATEDIFF(CURDATE(), p.fecha_fin) < 7 THEN CONCAT(DATEDIFF(CURDATE(), p.fecha_fin), ' días')
                WHEN DATEDIFF(CURDATE(), p.fecha_fin) < 30 THEN CONCAT(FLOOR(DATEDIFF(CURDATE(), p.fecha_fin) / 7), ' semanas')
                ELSE CONCAT(FLOOR(DATEDIFF(CURDATE(), p.fecha_fin) / 30), ' meses')
            END
    END AS diasDeRetraso
    
FROM
    prestamos p
JOIN
    visitantes v ON v.cedula = p.lector
LEFT JOIN
    multas m ON m.prestamo_id = p.id -- Asumo que tienes un campo id_prestamo en multas
LEFT JOIN estado_prestamo as ep ON ep.id = p.estado
WHERE
    p.estado IN (3, 5)
AND
    m.id IS NULL;
";


