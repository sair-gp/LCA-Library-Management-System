<?php
include "imprimirRegistros.php";

class Libros extends imprimirRegistros
{
    public $registros_por_pagina = 10;

    public $consultaCount = "SELECT COUNT(*) AS total FROM libros where delete_at = 0;";

    public $consultaPaginacion = "SELECT libros.isbn, libros.titulo, GROUP_CONCAT(DISTINCT CONCAT(autores.nombre) SEPARATOR ', ') AS autores, libros.anio, editorial.nombre AS editorialN, libros.edicion, GROUP_CONCAT(DISTINCT categorias.nombre SEPARATOR ', ') AS categorias, (SELECT COUNT(*) FROM ejemplares WHERE ejemplares.isbn_copia = libros.isbn) AS cantidad_ejemplares FROM libros JOIN libro_autores ON libros.isbn = libro_autores.isbn_libro JOIN autores ON libro_autores.id_autor = autores.id JOIN editorial ON libros.editorial = editorial.id JOIN libro_categoria ON libros.isbn = libro_categoria.isbn_libro JOIN categorias ON libro_categoria.id_categoria = categorias.id WHERE libros.delete_at = 0 GROUP BY libros.isbn ORDER BY libros.isbn LIMIT ?, ?";


    public function RegistrarLibro($isbn, $titulo, $anio_publicacion, $edicion, $autores, $editorial, $categorias, $fecha_registro, $conn, $portada, $forma_contenido, $calificacion_contenido, $tipo_de_medio, $titulo_paralelo, $extension, $detalles_fisicos, $dimensiones, $titulo_serie, $notas, $cota, $lugar_publicacion, $checkLugarPublicacion)
    {
        $conn->begin_transaction();
        //En caso del titulo serie estar definido (Es decir, es una serie) se le da ese titulo al titulo principal en lugar del titulo que deberia ser para el volumen. De no ser una serie, se mantiene el titulo original
        $titulo = empty($titulo_serie) ? $titulo : $titulo_serie;
        $es_obra_completa = empty($titulo_serie) ? 1 : 0;
        //$notas = $es_obra_completa = 1 ? $notas : "";
        
        try {

            //Manejo de autores, editoriales y categorias en caso de ser ingresadas unas que no existen
            // Función para manejar la inserción o recuperación de IDs
            $getOrInsertId = function ($table, $column, $value) use ($conn) {
                if (is_numeric($value)) {
                    return (int)$value;
                }

                // Convertir el valor a minúsculas para comparación
                $lowerValue = mb_strtolower($value, 'UTF-8');

                // Verificar si ya existe en la base de datos (sin distinguir mayúsculas)
                $stmt = $conn->prepare("SELECT id FROM $table WHERE LOWER($column) = LOWER(?) LIMIT 1");
                if (!$stmt) {
                    throw new Exception("Error al preparar la consulta en $table: " . $conn->error);
                }
                $stmt->bind_param("s", $lowerValue);
                $stmt->execute();
                $id = null;
                $stmt->bind_result($id);

                if ($stmt->fetch()) { // Si existe, devuelve el ID
                    $stmt->close();
                    return $id;
                }
                $stmt->close();

                // Si no existe, lo insertamos
                $stmt = $conn->prepare("INSERT INTO $table ($column) VALUES (?)");
                if (!$stmt) {
                    throw new Exception("Error al preparar inserción en $table: " . $conn->error);
                }
                $stmt->bind_param("s", $value);
                $stmt->execute();

                if ($stmt->affected_rows === 0) {
                    throw new Exception("No se pudo insertar en $table: $value");
                }

                $newId = $conn->insert_id;
                $stmt->close();

                return $newId;
            };

            // Verificar si $editorial es un array, si no lo es, convertirlo en un array con un solo elemento
            if (!is_array($editorial)) {
                $editorial = array($editorial);
            }

            // Manejo de editoriales
            $editorial_ids = array_map(fn($edit) => $getOrInsertId("editorial", "nombre", $edit), $editorial);

            //Agregar el origen de la editorial nueva
            if ($checkLugarPublicacion == 0) {
                // Primero comprobamos si el ID de la editorial ya existe
                $sqlCheckEditorial = "SELECT id FROM editorial WHERE id = ?";
                $stmtCheck = $conn->prepare($sqlCheckEditorial);
                if (!$stmtCheck) {
                    throw new Exception("No se ha podido preparar la consulta para comprobar el ID de la editorial");
                }
            
                $stmtCheck->bind_param("i", $editorial_ids[0]);
                $stmtCheck->execute();
                $result = $stmtCheck->get_result();
                $fila = $result->fetch_assoc();
            
                if ($fila["id"] == $editorial_ids[0]) {
                   // Si el ID existe, procedemos con la inserción del lugar de publicación
                $sqlLugar = "UPDATE editorial SET origen = ? WHERE id = ?";
                $stmt = $conn->prepare($sqlLugar);
                if (!$stmt) {
                    throw new Exception("No se ha podido preparar la consulta del lugar");
                }
            
                $stmt->bind_param("si", $lugar_publicacion, $editorial_ids[0]);
                $stmt->execute();
            
                if (!$stmt) {
                    throw new Exception("No se ha podido ejecutar la consulta del lugar");
                }
                }
            }
            

            // Manejo de categorías
            $categoria_ids = array_map(fn($categoria) => $getOrInsertId("categorias", "nombre", $categoria), $categorias);

            // Manejo de autores
            $autor_ids = array_map(fn($autor) => $getOrInsertId("autores", "nombre", $autor), $autores);


            
            // Insertar libro
            $stmt = $conn->prepare("INSERT INTO libros (isbn, forma_contenido, calificacion_contenido, tipo_medio, cota, titulo, titulo_paralelo, anio, extension, detalles_fisicos, dimensiones, editorial, edicion, fecha_registro, notas, portada, es_obra_completa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt)
                throw new Exception("Error al preparar consulta de libro: " . $conn->error);

            $stmt->bind_param("sssisssssssissssi", $isbn, $forma_contenido, $calificacion_contenido, $tipo_de_medio, $cota, $titulo, $titulo_paralelo, $anio_publicacion, $extension, $detalles_fisicos, $dimensiones, $editorial_ids[0], $edicion, $fecha_registro, $notas, $portada, $es_obra_completa);
            $stmt->execute();
            if ($stmt->affected_rows === 0)
                throw new Exception("No se pudo insertar el libro.");
            $stmt->close();


            // Insertar categorías
            $stmt2 = $conn->prepare("INSERT INTO libro_categoria (isbn_libro, id_categoria) VALUES (?, ?)");
            if (!$stmt2)
                throw new Exception("Error al preparar consulta de categorías: " . $conn->error);

            foreach ($categoria_ids as $categoriaId) {
                $stmt2->bind_param("si", $isbn, $categoriaId);
                $stmt2->execute();
                if ($stmt2->affected_rows === 0)
                    throw new Exception("Error al insertar categoría con ID: $categoriaId");
            }
            $stmt2->close();


            // Insertar autores
            $stmt3 = $conn->prepare("INSERT INTO libro_autores (id_autor, isbn_libro) VALUES (?, ?)");
            if (!$stmt3)
                throw new Exception("Error al preparar consulta de autores: " . $conn->error);

            foreach ($autor_ids as $autorId) {
                $stmt3->bind_param("is", $autorId, $isbn);
                $stmt3->execute();
                if ($stmt3->affected_rows === 0)
                    throw new Exception("Error al insertar autor con ID: $autorId");
            }
            $stmt3->close();

            $conn->commit();
            return true;
        } catch (Exception $editorial_ids) {
            $conn->rollback();
            error_log("Error en RegistrarLibro: " . $editorial_ids->getMessage());
            return false;
        }
    }

    public function ejecutarConsulta($tipo, $tabla, $datos = [], $condiciones = '', $conexion) {

        switch (strtoupper($tipo)) {
            case 'SELECT':
                $columnas = isset($datos['columnas']) ? implode(", ", $datos['columnas']) : '*';
                $sql = "SELECT $columnas FROM $tabla" . ($condiciones ? " WHERE $condiciones" : "");
                $resultado = $conexion->query($sql);
                return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    
            case 'INSERT':
                $columnas = implode(", ", array_keys($datos));
                $valores = implode("', '", array_map([$conexion, 'real_escape_string'], array_values($datos)));
                $sql = "INSERT INTO $tabla ($columnas) VALUES ('$valores')";
                return $conexion->query($sql) ? $conexion->insert_id : "Error: " . $conexion->error;
    
            case 'UPDATE':
                $set = implode(", ", array_map(fn($col) => "$col = '" . $conexion->real_escape_string($datos[$col]) . "'", array_keys($datos)));
                $sql = "UPDATE $tabla SET $set" . ($condiciones ? " WHERE $condiciones" : "");
                return $conexion->query($sql) ? $conexion->affected_rows : "Error: " . $conexion->error;
    
            case 'DELETE':
                if (!$condiciones) {
                    return "Error: DELETE requiere condiciones para evitar eliminar todos los registros";
                }
                $sql = "DELETE FROM $tabla WHERE $condiciones";
                return $conexion->query($sql) ? $conexion->affected_rows : "Error: " . $conexion->error;
    
            default:
                return "Error: Tipo de consulta no soportado";
        }
    
       
    }
    


    private function RegistrarLibroVolumenEjemplarOg($isbn, $titulo, $anio_publicacion, $edicion, $autor, $editorial, $categorias, $fecha_registro, $conn, $cota, $volumenes, $portada)
    {
        // Iniciar una transacción
        $conn->begin_transaction();

        try {
            // Insertar libro
            $stmt = $conn->prepare("INSERT INTO libros (isbn, cota, titulo, anio, editorial, edicion, fecha_registro, portada) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt === false) {
                throw new Exception("Error al preparar la consulta para insertar libro: " . $conn->error);
            }

            $stmt->bind_param("ssssisss", $isbn, $cota, $titulo, $anio_publicacion, $editorial, $edicion, $fecha_registro, $portada);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new Exception("No se pudo insertar el libro.");
            }


            // Insertar categorías
            $stmt2 = $conn->prepare("INSERT INTO libro_categoria (isbn_libro, id_categoria) VALUES (?, ?)");
            if ($stmt2 === false) {
                throw new Exception("Error al preparar la consulta para insertar categorías: " . $conn->error);
            }

            foreach ($categorias as $categoriaId) {
                $stmt2->bind_param("si", $isbn, $categoriaId);
                $stmt2->execute();
                if ($stmt2->affected_rows === 0) {
                    throw new Exception("Error al insertar la categoría con ID: $categoriaId");
                }
            }

            // Insertar autores
            $stmt3 = $conn->prepare("INSERT INTO libro_autores (id_autor, isbn_libro) VALUES (?, ?)");
            if ($stmt3 === false) {
                throw new Exception("Error al preparar la consulta para insertar autores: " . $conn->error);
            }

            foreach ($autor as $autorId) {
                $stmt3->bind_param("is", $autorId, $isbn);
                $stmt3->execute();
                if ($stmt3->affected_rows === 0) {
                    throw new Exception("Error al insertar el autor con ID: $autorId");
                }
            }


            //Insertar volúmenes$stmt4

            // Insertar volúmenes
            $stmt4 = $conn->prepare("INSERT INTO volumenes (isbn_vol, isbn_obra, nombre, numero, anio, portada) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt4 === false) {
                throw new Exception("Error al preparar la consulta para insertar volúmenes: " . $conn->error);
            }



            foreach ($volumenes as $volumen) {

                $isbnVolumen = $volumen['isbn'];
                $tituloVolumen = $volumen['titulo'];
                $numeroVolumen = $volumen['numero'];
                $anioVolumen = $volumen['anio'];
                $portadaVol = $volumen['portada'];

                $stmt4->bind_param("ssssss", $isbnVolumen, $isbn, $tituloVolumen, $numeroVolumen, $anioVolumen, $portadaVol);
                $stmt4->execute();
                if ($stmt4->affected_rows === 0) {
                    throw new Exception("Error al insertar el volumen con ISBN: $isbnVolumen");
                }
            }

            // Si todo fue exitoso, se confirma la transacción
            $conn->commit();
            return true;
        } catch (Exception $e) {
            // Si hubo un error, revertir la transacción
            $conn->rollback();
            error_log($e->getMessage()); // Log para depuración
            return false;
        } finally {
            // Cerrar los statements después de usarlos
            if (isset($stmt))
                $stmt->close();
            if (isset($stmt2))
                $stmt2->close();
            if (isset($stmt3))
                $stmt3->close();
            if (isset($stmt4))
                $stmt4->close();
        }
    }

    public function RegistrarLibroVolumenEjemplar($isbn, $titulo, $anio_publicacion, $edicion, $autor, $editorial, $categorias, $fecha_registro, $conn, $cota, $volumenes, $portada)
    {
        // Habilitar reportes detallados en desarrollo (opcional)
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        // Validar arrays antes de iterarlos
        if (!is_array($categorias))
            $categorias = [];
        if (!is_array($autor))
            $autor = [];
        if (!is_array($volumenes))
            $volumenes = [];

        // Iniciar una transacción
        $conn->begin_transaction();

        try {
            // Insertar libro
            $stmt = $conn->prepare("INSERT INTO libros (isbn, cota, titulo, anio, editorial, edicion, fecha_registro, portada) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt === false) {
                throw new Exception("Error al preparar la consulta para insertar libro: " . $conn->error);
            }
            $stmt->bind_param("ssssisss", $isbn, $cota, $titulo, $anio_publicacion, $editorial, $edicion, $fecha_registro, $portada);
            if (!$stmt->execute()) {
                throw new Exception("Error al insertar el libro: " . $stmt->error);
            }

            // Insertar categorías
            $stmt2 = $conn->prepare("INSERT INTO libro_categoria (isbn_libro, id_categoria) VALUES (?, ?)");
            if ($stmt2 === false) {
                throw new Exception("Error al preparar la consulta para insertar categorías: " . $conn->error);
            }
            foreach ($categorias as $categoriaId) {
                $stmt2->bind_param("si", $isbn, $categoriaId);
                if (!$stmt2->execute()) {
                    throw new Exception("Error al insertar la categoría con ID: $categoriaId - " . $stmt2->error);
                }
            }

            // Insertar autores
            $stmt3 = $conn->prepare("INSERT INTO libro_autores (id_autor, isbn_libro) VALUES (?, ?)");
            if ($stmt3 === false) {
                throw new Exception("Error al preparar la consulta para insertar autores: " . $conn->error);
            }
            foreach ($autor as $autorId) {
                $stmt3->bind_param("is", $autorId, $isbn);
                if (!$stmt3->execute()) {
                    throw new Exception("Error al insertar el autor con ID: $autorId - " . $stmt3->error);
                }
            }

            // Insertar volúmenes
            $stmt4 = $conn->prepare("INSERT INTO volumenes (isbn_vol, isbn_obra, nombre, numero, anio, portada) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt4 === false) {
                throw new Exception("Error al preparar la consulta para insertar volúmenes: " . $conn->error);
            }

            $isbnObra = $isbn; // Se usa el ISBN de la obra principal

            foreach ($volumenes as $volumen) {
                $isbnVolumen = $volumen['isbn'];
                $tituloVolumen = $volumen['titulo'];
                $numeroVolumen = $volumen['numero'];
                $anioVolumen = $volumen['anio'];
                $portadaVol = $volumen['portada'] ?? 'default.jpg'; // Asegurar que tenga un valor

                $stmt4->bind_param("ssssss", $isbnVolumen, $isbnObra, $tituloVolumen, $numeroVolumen, $anioVolumen, $portadaVol);
                if (!$stmt4->execute()) {
                    throw new Exception("Error al insertar el volumen con ISBN: $isbnVolumen - " . $stmt4->error);
                }
            }

            // Confirmar la transacción si todo fue exitoso
            $conn->commit();
            return true;
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $conn->rollback();
            error_log($e->getMessage()); // Registrar errores para depuración
            return false;
        } finally {
            // Cerrar los statements para liberar memoria
            if (isset($stmt))
                $stmt->close();
            if (isset($stmt2))
                $stmt2->close();
            if (isset($stmt3))
                $stmt3->close();
            if (isset($stmt4))
                $stmt4->close();
        }
    }



    public function eliminar_libro($connect, $isbn)
    {
        $stmt = $connect->prepare("UPDATE `libros` SET `delete_at`= 1 WHERE isbn = ?");
        $stmt->bind_param("s", $isbn);

        $result = $stmt->execute();

        if ($result) {
            return true;
        } else {
            return null; // O retorna un mensaje de error adecuado
        }
    }

    public function retornar_libro($connect, $isbn)
    {
        $stmt = $connect->prepare("UPDATE `libros` SET `delete_at`= 0 WHERE isbn = ?");
        $stmt->bind_param("s", $isbn);

        $result = $stmt->execute();
    }
}
