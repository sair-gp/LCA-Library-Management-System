<?php

class imprimirRegistros
{
    public function obtenerPaginacion($conexion, $consultaCount, $consultaPaginacion, $registros_por_pagina) {
        // Obtener la página actual
        $pagina_actual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? intval($_GET['pagina']) : 1;
    
        // Calcular el desplazamiento (offset)
        $inicio = ($pagina_actual - 1) * $registros_por_pagina;
    
        // Consulta para obtener el total de registros
        $stmt = $conexion->prepare($consultaCount);
        $stmt->execute();
        $resultado_total = $stmt->get_result();
        $row_total = $resultado_total->fetch_assoc();
        $total_registros = $row_total['total'];
    
        // Calcular el total de páginas
        $total_paginas = ceil($total_registros / $registros_por_pagina);
    
        // Consulta para obtener los registros de la página actual
        $stmt = $conexion->prepare($consultaPaginacion);
        $stmt->bind_param("ii", $inicio, $registros_por_pagina);
        $stmt->execute();
        $resultado = $stmt->get_result();
    
        // Retornar los resultados junto con el total de páginas y registros
        return [
            'total_registros' => $total_registros,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_actual,
            'registros' => $resultado
        ];
    }
    

    
    public function generarPaginacion($pagina_actual, $total_paginas) {
    // Obtener el nombre de la vista desde el parámetro GET
    $vista = isset($_GET['vista']) ? $_GET['vista'] : '';

    // Iniciar la variable de la paginación
    $paginacion = '<div class="col-sm-7 d-flex justify-content-end">
                    <ul class="pagination">';

    // Botón de Anterior
    $paginacion .= '<li class="page-item ' . ($pagina_actual <= 1 ? 'disabled' : '') . '">
                        <a class="page-link" href="index.php?vista=' . $vista . '&pagina=' . ($pagina_actual - 1) . '">Anterior</a>
                    </li>';

    // Mostrar la primera página
    if ($pagina_actual > 3) {
        $paginacion .= '<li class="page-item"><a class="page-link" href="index.php?vista=' . $vista . '&pagina=1">1</a></li>';
    }

    // Mostrar puntos suspensivos antes de las páginas cercanas si es necesario
    if ($pagina_actual > 4) {
        $paginacion .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
    }

    // Mostrar páginas cercanas a la actual
    for ($i = max(1, $pagina_actual - 2); $i <= min($total_paginas, $pagina_actual + 2); $i++) {
        $paginacion .= '<li class="page-item ' . ($pagina_actual == $i ? 'active' : '') . '">
                            <a class="page-link" href="index.php?vista=' . $vista . '&pagina=' . $i . '">' . $i . '</a>
                        </li>';
    }

    // Mostrar puntos suspensivos después de las páginas cercanas si es necesario
    if ($pagina_actual < $total_paginas - 3) {
        $paginacion .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
    }

    // Mostrar la última página
    if ($pagina_actual < $total_paginas - 2) {
        $paginacion .= '<li class="page-item"><a class="page-link" href="index.php?vista=' . $vista . '&pagina=' . $total_paginas . '">' . $total_paginas . '</a></li>';
    }

    // Botón de Siguiente
    $paginacion .= '<li class="page-item ' . ($pagina_actual >= $total_paginas ? 'disabled' : '') . '">
                        <a class="page-link" href="index.php?vista=' . $vista . '&pagina=' . ($pagina_actual + 1) . '">Siguiente</a>
                    </li>';

    // Cerrar la lista y el contenedor
    $paginacion .= '</ul></div>';

    // Retornar el código HTML generado
    return $paginacion;
}






}