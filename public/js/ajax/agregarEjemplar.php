<?php
include '../../../app/config/database.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = conexion();
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isbn_obra = $_POST['isbn'] ?? '';
    $isbn_ejemplar = $_POST['id'] ?? '';
    $cota = $_POST['cota'] ?? '';
    $cantidad = (int)($_POST['cantidad'] ?? 1);
    $estanteria = $_POST['estanteria'] ?? null;
    $fila = $_POST['fila'] ?? null;

    // Validación reforzada
    if (empty($isbn_obra) || empty($isbn_ejemplar) || empty($cota) || $cantidad < 1 || empty($estanteria) || empty($fila)) {
        echo json_encode([
            "status" => "error", 
            "message" => "Todos los campos son obligatorios."
        ]);
        exit;
    }

    try {
        $conn->begin_transaction();

        // 1. Verificar disponibilidad en la fila
        $sql = "SELECT f.Capacidad, f.LibrosActuales 
                FROM fila f 
                WHERE f.FilaID = ? FOR UPDATE"; // Bloqueo para evitar condiciones de carrera
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $fila);
        $stmt->execute();
        $result = $stmt->get_result();
        $filaData = $result->fetch_assoc();
        
        if (!$filaData) {
            throw new Exception("La fila seleccionada no existe.");
        }
        
        $disponible = $filaData['Capacidad'] - $filaData['LibrosActuales'];
        if ($disponible < $cantidad) {
            throw new Exception("No hay suficiente espacio en la fila. Disponible: $disponible, Intento: $cantidad");
        }

        // 2. Insertar ejemplares
        $sql = "INSERT INTO ejemplares (isbn_copia, isbn_vol, cota, filaID, estado, delete_at, fuente_suministro) 
                VALUES (?, ?, ?, ?, 1, 1, 1)";
        $stmt = $conn->prepare($sql);
        
        // Obtener el último número de ejemplar
        $sqlLast = "SELECT IFNULL(MAX(CAST(SUBSTRING_INDEX(cota, '.e', -1) AS UNSIGNED)), 0) AS last_num 
                    FROM ejemplares 
                    WHERE isbn_copia = ?";
        $stmtLast = $conn->prepare($sqlLast);
        $stmtLast->bind_param("s", $isbn_obra);
        $stmtLast->execute();
        $resultLast = $stmtLast->get_result();
        $lastNum = $resultLast->fetch_assoc()['last_num'];
        
        // Insertar cada ejemplar
        for ($i = 1; $i <= $cantidad; $i++) {
            $newNum = $lastNum + $i;
            $cotaFormateada = "{$cota}.e{$newNum}";
            $stmt->bind_param("sssi", $isbn_obra, $isbn_ejemplar, $cotaFormateada, $fila);
            $stmt->execute();
        }

        // 3. Obtener el nuevo conteo exacto
        $sqlCount = "SELECT COUNT(*) AS total FROM ejemplares WHERE isbn_vol = ?";
        $stmtCount = $conn->prepare($sqlCount);
        $stmtCount->bind_param("s", $isbn_ejemplar);
        $stmtCount->execute();
        $resultCount = $stmtCount->get_result();
        $totalCopias = $resultCount->fetch_assoc()['total'];

        // 4. Actualizar contador de la fila
        $sqlUpdateFila = "UPDATE fila SET LibrosActuales = LibrosActuales + ? WHERE FilaID = ?";
        $stmtUpdate = $conn->prepare($sqlUpdateFila);
        $stmtUpdate->bind_param("ii", $cantidad, $fila);
        $stmtUpdate->execute();

        $conn->commit();
        
        echo json_encode([
            "status" => "success", 
            "message" => "$cantidad ejemplar(es) agregado(s) correctamente",
            "copias" => $totalCopias,
            "isbnVol" => $isbn_ejemplar,
            "added" => $cantidad // Nuevo campo para indicar cuántos se agregaron
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            "status" => "error", 
            "message" => $e->getMessage()
        ]);
    } finally {
        if (isset($stmt)) $stmt->close();
        if (isset($stmtLast)) $stmtLast->close();
        if (isset($stmtCount)) $stmtCount->close();
        if (isset($stmtUpdate)) $stmtUpdate->close();
        $conn->close();
    }
}