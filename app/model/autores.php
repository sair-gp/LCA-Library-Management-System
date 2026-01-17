<?php


class Autores
{

    private $selectAutor = "SELECT id, nombre, fecha_nacimiento, lugar_nacimiento, biografia, foto FROM autores WHERE id = ?";

    private $selectLibrosAutor = "SELECT l.isbn, l.titulo, l.extension, vo.anio, c.nombre AS categoria, GROUP_CONCAT(DISTINCT a.nombre SEPARATOR ', ') AS autor FROM libros l LEFT JOIN libro_autores la ON la.isbn_libro = l.isbn LEFT JOIN autores a ON a.id = la.id_autor LEFT JOIN volumen vo ON vo.isbn_obra = l.isbn LEFT JOIN libro_categoria lc ON lc.isbn_libro = l.isbn LEFT JOIN categorias as c ON c.id = lc.id_categoria WHERE a.id = ? GROUP BY l.titulo;";

    private $selectRankingAutor = "SELECT ROW_NUMBER() OVER (ORDER BY total_prestamos DESC, nombre ASC) AS posicion, id, nombre, total_prestamos FROM ( SELECT a.nombre, a.id, COUNT(p.cota) AS total_prestamos FROM autores a LEFT JOIN libro_autores la ON la.id_autor = a.id LEFT JOIN libros l ON l.isbn = la.isbn_libro LEFT JOIN ejemplares e ON e.isbn_copia = l.isbn LEFT JOIN prestamos p ON p.cota = e.id GROUP BY a.id, a.nombre ) AS ranking ORDER BY posicion;";

    public function agregarAutores($conn, $nombre, $biografia, $fechaNacimiento, $foto) 
{
    // Validación adicional de parámetros
    if (empty($nombre)) {
        throw new InvalidArgumentException("El nombre del autor no puede estar vacío");
    }

    if (!is_string($nombre) || strlen($nombre) > 255) {
        throw new InvalidArgumentException("Nombre inválido");
    }

    if ($biografia !== null && !is_string($biografia)) {
        throw new InvalidArgumentException("Biografía debe ser una cadena de texto");
    }

    if ($fechaNacimiento !== null) {
        if (!DateTime::createFromFormat('Y-m-d', $fechaNacimiento)) {
            throw new InvalidArgumentException("Formato de fecha inválido");
        }
    }

    // Iniciar transacción para operación atómica
    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO `autores` 
                              (`nombre`, `biografia`, `fecha_nacimiento`, `foto`) 
                              VALUES (?, ?, ?, ?)");
        
        if (!$stmt) {
            throw new RuntimeException("Error al preparar la consulta: " . $conn->error);
        }

        // Bind parameters con verificación de éxito
        $bound = $stmt->bind_param("ssss", 
            $nombre, 
            $biografia, 
            $fechaNacimiento, 
            $foto
        );

        if (!$bound) {
            throw new RuntimeException("Error al vincular parámetros: " . $stmt->error);
        }

        // Ejecutar con verificación
        if (!$stmt->execute()) {
            throw new RuntimeException("Error al ejecutar la consulta: " . $stmt->error);
        }

        // Verificar inserción exitosa
        $affectedRows = $stmt->affected_rows;
        $insertedId = $stmt->insert_id;
        
        $stmt->close();
        
        // Confirmar transacción
        $conn->commit();

        // Devolver ID del nuevo autor en lugar de solo true/false
        return $insertedId;

    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();
        
        // Registrar error (en producción usar un sistema de logging)
        error_log("Error en agregarAutores: " . $e->getMessage());
        
        // Relanzar excepción para manejo superior
        throw $e;
    }
}

public function verificarExistencia($conn, $nombre) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM autores WHERE nombre = ?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}


    public function setUpFichaAutor($id, $conexion){
        $stmt = $conexion->prepare($this->selectAutor);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if (!$fila = $resultado->fetch_assoc()) {
            return false;
        }

        $autor = [
            'nombre' => $fila['nombre'] ?? "",
            'foto' => $fila['foto'] ?? "public/img/autores/default.jpg",
            'biografia' => $fila["biografia"] ?? NULL,
            'fecha_nacimiento' => $fila["fecha_nacimiento"] ?? NULL,
            'lugar_nacimiento' => $fila["lugar_nacimiento"] ?? NULL
        ];
        
        return $autor;

    }





   /**
 * Obtiene todos los libros de un autor desde la base de datos.
 *
 * @param int $idAutor ID del autor.
 * @param mysqli $conexion Conexión a la base de datos.
 * @return array|false Array asociativo con los libros o false si no hay resultados o hay error.
 */
public function obtenerLibrosAutor(int $idAutor, mysqli $conexion): array|false {
    // Verifica si la consulta SQL está definida
    if (empty($this->selectLibrosAutor)) {
        throw new InvalidArgumentException("La consulta SQL no está definida.");
    }

    $stmt = $conexion->prepare($this->selectLibrosAutor);
    if ($stmt === false) {
        return false; // o throw new RuntimeException("Error al preparar la consulta.");
    }

    $stmt->bind_param("i", $idAutor);
    if (!$stmt->execute()) {
        return false; // o throw new RuntimeException("Error al ejecutar la consulta.");
    }

    $resultado = $stmt->get_result();
    if ($resultado === false || $resultado->num_rows === 0) {
        return false;
    }

    return $resultado->fetch_all(MYSQLI_ASSOC); // Más eficiente que el while
}






    /**
 * Obtiene los nombres de los campos que son NULL para un autor específico
 */
    private function obtenerCamposNulos(int $id, mysqli $conexion): array
{
    $camposRequeridos = ['biografia', 'fecha_nacimiento', 'lugar_nacimiento'];
    $camposNulos = [];
    
    try {
        // Consulta para verificar qué campos son NULL
        $sql = "SELECT biografia, fecha_nacimiento, lugar_nacimiento FROM autores WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $conexion->error);
        }

        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $fila = $result->fetch_assoc();

        foreach ($camposRequeridos as $campo) {
            if ($fila[$campo] === null) {
                $camposNulos[] = $campo;
            }
        }

        return $camposNulos;
        
    } catch (Exception $e) {
        error_log("Error en obtenerCamposNulos: " . $e->getMessage());
        return [];
    }
    }

    // Función para obtener datos del autor desde Wikidata
    private function obtenerDatosAutorAPI($nombreAutor) {
        // Configurar el contexto para file_get_contents con timeout
        $context = stream_context_create([
            'http' => [
                'timeout' => 3 // Timeout de 3 segundos
            ]
        ]);
        
        // Valores por defecto
        $datosPorDefecto = [
            'nombre_correcto' => $nombreAutor,
            'biografia' => 'Biografía no disponible.',
            'fecha_nacimiento' => 'Fecha de nacimiento no disponible.',
            'lugar_nacimiento' => 'Lugar de nacimiento no disponible.',
            'fuente' => 'local'
        ];
        
        // Intenta desactivar errores para file_get_contents
        $errorReporting = error_reporting();
        error_reporting($errorReporting & ~E_WARNING);
        
        try {
            // Paso 1: Obtener el ID de Wikidata para el autor
            $url = "https://www.wikidata.org/w/api.php?action=wbsearchentities&format=json&language=es&search=" . urlencode($nombreAutor);
            $response = @file_get_contents($url, false, $context);
            
            if ($response === FALSE) {
                return $datosPorDefecto;
            }
            
            $data = json_decode($response, true);
            
            // Verificar si se encontraron resultados
            if (empty($data['search'])) {
                return $datosPorDefecto;
            }
            
            // Tomar el primer resultado (el más relevante)
            $wikidataId = $data['search'][0]['id'];
            $nombreCorrecto = $data['search'][0]['label']; // Nombre correcto del autor
            
            // Paso 2: Obtener los datos específicos del autor usando el ID de Wikidata
            $url = "https://www.wikidata.org/w/api.php?action=wbgetentities&ids=$wikidataId&format=json&props=claims|descriptions|sitelinks";
            $response = @file_get_contents($url, false, $context);
            
            if ($response === FALSE) {
                return array_merge($datosPorDefecto, [
                    'nombre_correcto' => $nombreCorrecto
                ]);
            }
            
            $data = json_decode($response, true);
            
            // Extraer los datos relevantes
            $fechaNacimiento = $data['entities'][$wikidataId]['claims']['P569'][0]['mainsnak']['datavalue']['value']['time'] ?? 'Fecha de nacimiento no disponible.';
            $lugarNacimientoId = $data['entities'][$wikidataId]['claims']['P19'][0]['mainsnak']['datavalue']['value']['id'] ?? null;
            
            // Obtener el nombre del lugar de nacimiento
            $lugarNacimiento = 'Lugar de nacimiento no disponible.';
            if ($lugarNacimientoId) {
                $url = "https://www.wikidata.org/w/api.php?action=wbgetentities&ids=$lugarNacimientoId&format=json&props=labels";
                $response = @file_get_contents($url, false, $context);
                
                if ($response !== FALSE) {
                    $lugarData = json_decode($response, true);
                    $lugarNacimiento = $lugarData['entities'][$lugarNacimientoId]['labels']['es']['value'] ?? 'Lugar de nacimiento no disponible.';
                }
            }
            
            // Formatear la fecha de nacimiento (eliminar el prefijo "+" y el formato ISO)
            if ($fechaNacimiento !== 'Fecha de nacimiento no disponible.') {
                $fechaNacimiento = substr($fechaNacimiento, 1, 10); // Extraer YYYY-MM-DD
            }
            
            // Paso 3: Obtener el título de la página de Wikipedia en español
            $tituloWikipedia = $data['entities'][$wikidataId]['sitelinks']['eswiki']['title'] ?? null;
            
            // Paso 4: Obtener la biografía desde Wikipedia usando el título exacto
            $biografia = 'Biografía no disponible.';
            if ($tituloWikipedia) {
                // Codificar el título para usarlo en la URL
                $tituloCodificado = urlencode(str_replace(' ', '_', $tituloWikipedia));
                $url = "https://es.wikipedia.org/api/rest_v1/page/summary/$tituloCodificado";
                $response = @file_get_contents($url, false, $context);
                
                if ($response !== FALSE) {
                    $data = json_decode($response, true);
                    $biografia = $data['extract'] ?? 'Biografía no disponible.';
                }
            }
            
            // Restaurar el nivel de reporte de errores
            error_reporting($errorReporting);
            
            // Retornar los datos en un array
            return [
                'nombre_correcto' => $nombreCorrecto,
                'biografia' => $biografia,
                'fecha_nacimiento' => $fechaNacimiento,
                'lugar_nacimiento' => $lugarNacimiento,
                'fuente' => 'wikidata'
            ];
            
        } catch (Exception $e) {
            // Restaurar el nivel de reporte de errores
            error_reporting($errorReporting);
            return $datosPorDefecto;
        }
    }

    public function guardarDatosAutorAPI(string $nombreAutor, int $id, mysqli $conexion): bool
{
    // Validación de parámetros de entrada
    if (empty($nombreAutor) || $id <= 0 || !$conexion instanceof mysqli) {
        return false;
    }

    // 1. Primero verificamos qué campos necesitamos actualizar
    $camposAActualizar = $this->obtenerCamposNulos($id, $conexion);
    
    // Si no hay campos nulos, no necesitamos hacer nada
    if (empty($camposAActualizar)) {
        return true;
    }

    // 2. Solo llamamos a la API si realmente necesitamos datos
    $datosAutor = $this->obtenerDatosAutorAPI($nombreAutor);
    
    if (empty($datosAutor) || !is_array($datosAutor)) {
        return false;
    }

    // 3. Validar datos obtenidos solo para los campos que necesitamos
    foreach ($camposAActualizar as $campo) {
        if (!isset($datosAutor[$campo]) || empty(trim($datosAutor[$campo])) || stripos($datosAutor[$campo], 'no disponible') !== false) {
            return false;
        }
    }

    try {
        // 4. Construir la consulta SQL dinámicamente según los campos a actualizar
        $sql = "UPDATE autores SET ";
        $params = [];
        $types = "";
        
        foreach ($camposAActualizar as $campo) {
            $sql .= "$campo = ?, ";
            $params[] = $datosAutor[$campo];
            $types .= "s"; // todos los campos son strings en este caso
        }
        
        $sql = rtrim($sql, ", ") . " WHERE id = ?";
        $params[] = $id;
        $types .= "i";

        $stmt = $conexion->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $conexion->error);
        }

        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }

        return $stmt->affected_rows > 0;
        
    } catch (Exception $e) {
        error_log("Error en guardarDatosAutorAPI: " . $e->getMessage());
        return false;
    }
}

    public function obtenerRanking($conexion)
{
    $stmt = $conexion->prepare($this->selectRankingAutor);
    $stmt->execute();

    $resultado = $stmt->get_result();

    $rankingAutores = [];
    $posicion = 1; // Inicia el contador de posiciones

    while ($fila = $resultado->fetch_assoc()) {
        
        $idAutor = $fila['id'];
        $rankingAutores[$idAutor] = $fila["total_prestamos"] > 0 ? $posicion : NULL; // Asigna la posición actual
        $posicion++; // Incrementa para el siguiente autor
    }

    return $rankingAutores;
   
}



}


