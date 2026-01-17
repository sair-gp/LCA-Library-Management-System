<?php
include '../../model/autores.php';
include '../../config/database.php';

// Configuración básica de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '../../logs/autores_error.log');

$conn = conexion();
$autor = new Autores();

try {
    // Validar campos obligatorios
    if (empty($_POST['autorNombre'])) {
        throw new Exception("El nombre del autor es requerido");
    }

    // Sanitizar datos
    $nombre = htmlspecialchars($_POST['autorNombre'], ENT_QUOTES, 'UTF-8');
    $biografia = !empty($_POST['biografia']) ? htmlspecialchars($_POST['biografia'], ENT_QUOTES, 'UTF-8') : null;
    $fechaNacimiento = !empty($_POST['fechaNacimiento']) ? $_POST['fechaNacimiento'] : null;

    // Manejo de foto (igual que tu versión de portadas)
    $fotoAutor = 'public/img/autores/default.jpg';
    if (!empty($_FILES['fotoAutor']['name'])) {
        
        $directorio_autores = '../../../public/img/autores/';
        
        // Crear directorio si no existe
        if (!is_dir($directorio_autores)) {
            if (!mkdir($directorio_autores, 0755, true)) {
                throw new Exception("No se pudo crear el directorio para fotos");
            }
        }

        // Validar tipo de imagen
        $permitidos = ['image/jpeg', 'image/png'];
        $tipoArchivo = $_FILES['fotoAutor']['type'];
        
        if (!in_array($tipoArchivo, $permitidos)) {
            throw new Exception("Solo se permiten imágenes JPEG o PNG");
        }

        // Generar nombre único
        $extension = pathinfo($_FILES['fotoAutor']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = time() . '_' . preg_replace('/[^a-zA-Z0-9-_\.]/', '', $_FILES['fotoAutor']['name']);
        $ruta_archivo = $directorio_autores . $nombre_archivo;

        if (move_uploaded_file($_FILES['fotoAutor']['tmp_name'], $ruta_archivo)) {
            $fotoAutor = 'public/img/autores/' . $nombre_archivo;
        } else {
            throw new Exception("Error al mover el archivo subido");
        }
    }

    // Insertar en base de datos
    $resultado = $autor->agregarAutores($conn, $nombre, $biografia, $fechaNacimiento, $fotoAutor);
    
    if ($resultado === false) {
        throw new Exception("Error al guardar en la base de datos");
    }

    // Éxito
    header("Location: ../../../index.php?vista=autores&alerta=exito");
    exit;

} catch (Exception $e) {
    // Limpiar archivo subido si hubo error
    if (isset($ruta_archivo) && file_exists($ruta_archivo)) {
        unlink($ruta_archivo);
    }
    
    // Registrar error
    error_log("Error en agregar_autor: " . $e->getMessage());
    
    // Redireccionar con error
    header("Location: ../../../index.php?vista=autores&alerta=error&mensaje=" . urlencode($e->getMessage()));
    exit;
}
?>