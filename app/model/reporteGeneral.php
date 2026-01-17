<?php

class ReporteGeneral {

    
    private $prestamos = "SELECT p.id, l.isbn, e.cota, v.cedula, v.nombre, p.fecha_inicio, p.fecha_fin, es.id AS verificarBoton, es.estado, CASE WHEN l.es_obra_completa = 1 THEN l.titulo ELSE CASE WHEN REGEXP_REPLACE(vo.nombre, '[0-9]', '') = l.titulo THEN CONCAT(l.titulo, ' ', 'volumen ', vo.numero) ELSE CONCAT(l.titulo, ' \"', COALESCE(vo.nombre, ''), '\". ') END END AS titulo FROM prestamos AS p JOIN ejemplares AS e ON p.cota = e.id JOIN libros AS l ON e.isbn_copia = l.isbn JOIN estado_prestamo AS es ON p.estado = es.id JOIN visitantes AS v ON p.lector = v.cedula JOIN volumen AS vo ON e.isbn_vol = vo.id";  
    private $devoluciones = "SELECT p.id, l.isbn, e.cota, v.cedula, v.nombre, p.fecha_inicio, p.fecha_fin, es.id AS verificarBoton, es.estado, p.estado AS eprestamo, CASE WHEN l.es_obra_completa = 1 THEN l.titulo ELSE CASE WHEN REGEXP_REPLACE(vo.nombre, '[0-9]', '') = l.titulo THEN CONCAT(l.titulo, ' ', 'volumen ', vo.numero) ELSE CONCAT(l.titulo, ' \"', COALESCE(vo.nombre, ''), '\". ') END END AS titulo FROM prestamos AS p JOIN ejemplares AS e ON p.cota = e.id JOIN libros AS l ON e.isbn_copia = l.isbn JOIN estado_prestamo AS es ON p.estado = es.id JOIN visitantes AS v ON p.lector = v.cedula JOIN volumen AS vo ON e.isbn_vol = vo.id ";  
    private $actividades = "SELECT ra.*, ea.estado FROM registro_actividades ra JOIN estado_actividad ea ON ea.id = ra.estado";  
    private $librosMasSolicitados = "SELECT GROUP_CONCAT(DISTINCT c.nombre SEPARATOR ', ') AS categoria, l.isbn, CASE WHEN l.es_obra_completa = 1 THEN l.titulo ELSE CASE WHEN REGEXP_REPLACE(vo.nombre, '[0-9]', '') = l.titulo THEN CONCAT(l.titulo, ' ', 'volumen ', vo.numero) ELSE CONCAT(l.titulo, ' \"', COALESCE(vo.nombre, ''), '\". ') END END AS titulo, COUNT(DISTINCT p.id) AS total_prestamos FROM prestamos AS p JOIN ejemplares AS e ON p.cota = e.id JOIN libros AS l ON e.isbn_copia = l.isbn LEFT JOIN volumen AS vo ON e.isbn_vol = vo.id JOIN libro_categoria AS lc ON lc.isbn_libro = l.isbn JOIN categorias AS c ON c.id = lc.id_categoria";  
    private $visitasPorHorario = "SELECT asi.*, vi.nombre FROM asistencias asi JOIN visitantes vi ON vi.cedula = asi.cedula_visitante";  
    private $sanciones = "";  


    private $conexion;

    private $tipoDeReporte = [];

    public function __construct($conexion){
        $this->conexion = $conexion;
        $this->tipoDeReporte = [
            "prestamos" => $this->prestamos ?? "",
            "devoluciones" => $this->devoluciones ?? "",
            "actividades" => $this->actividades ?? "",
            "librosMasSolicitados" => $this->librosMasSolicitados ?? "",
            "visitasPorHorario" => $this->visitasPorHorario ?? "",
            "sanciones" => $this->sanciones ?? ""
        ];
    }


    /**
 * Obtiene los datos para un reporte específico.
 *
 * @param string $tipoReporte Tipo de reporte (ej. "prestamos", "devoluciones").
 * @param string $filtros Filtros SQL para la consulta.
 * @param array $valores Valores para los placeholders en los filtros.
 * @return array Datos del reporte.
 * @throws Exception Si ocurre un error en la consulta.
 */
    public function obtenerDatosParaReporte($tipoReporte, $filtros, $valores){
        //Verificar si el tipo de reporte existe en el array
        if (array_key_exists($tipoReporte, $this->tipoDeReporte)){
            $sql = $this->tipoDeReporte[$tipoReporte];
            $sql .= " $filtros";

            $stmt = $this->conexion->prepare($sql);
            if (!$stmt){
                throw new Exception("Error al preprar la consulta de $tipoReporte");
            }

            //Contar cuantas veces se repite el ? en filtros

            $conteo = substr_count($filtros, "?");

            //Verificar que la cantidad de ? sea igual a la cantidad de valores
            if ($conteo !== count($valores)) {
                throw new Exception("El número de placeholders ? ($conteo) en filtros: $filtros no coincide con el número de valores (" . count($valores) .") proporcionados.");
            }

            $tipos = str_repeat("s", $conteo);
            
            $stmt->bind_param($tipos, ...array_values($valores)); //uso del operador de propagacion ... para que convierta el array en una lista de argumentos separados, que es lo que bind_param espera. 

            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar la consulta: $stmt->error");
            }

            $resultado = $stmt->get_result();

            if(!$resultado){
                throw new Exception("Ha habido un error a la hora de obtener los datos de $tipoReporte");
            }

          
            $datos = [];
            while ($row = $resultado->fetch_assoc()){
                $datos[] = $row;
            }

            $stmt->close();

            return $datos;

        } else {
            throw new Exception("El tipo de reporte ". $tipoReporte ." no existe.");
        }


    }

}