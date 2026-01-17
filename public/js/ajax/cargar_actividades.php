<?php
require_once '../../../app/config/database.php';

header('Content-Type: text/html; charset=utf-8');

$searchTerm = $_GET['q'] ?? '';
$searchTerm = trim($searchTerm);

try {
    $conn = conexion();
    
    $query = "SELECT r.id, r.descripcion, r.encargado, r.fecha_inicio, r.fecha_fin, 
                     r.duracion, r.observacion, e.estado 
              FROM registro_actividades r 
              JOIN estado_actividad e ON e.id = r.estado
              WHERE r.descripcion LIKE ? OR r.encargado LIKE ? OR e.estado LIKE ?
              ORDER BY r.fecha_inicio DESC";
    
    $stmt = $conn->prepare($query);
    $searchParam = "%$searchTerm%";
    $stmt->bind_param('sss', $searchParam, $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<tr>
                    <td style="display: none;">'.htmlspecialchars($row['id']).'</td>
                    <td>'.htmlspecialchars($row['descripcion']).'</td>
                    <td>'.htmlspecialchars($row['encargado']).'</td>
                    <td>'.htmlspecialchars($row['fecha_inicio']).'</td>
                    <td>'.htmlspecialchars($row['fecha_fin']).'</td>
                    <td>'.htmlspecialchars($row['duracion'] ?? "3 horas").'</td>
                    <td>'.htmlspecialchars($row['observacion'] ?? "Sin observación").'</td>
                    <td>
                        <button class="btn btn-primary btn-sm modalbtnAccion" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalAccion" 
                                data-id="'.htmlspecialchars($row['id']).'">
                            Acciones
                        </button>
                    </td>
                    <td>'.htmlspecialchars($row['estado']).'</td>
                  </tr>';
        }
    } else {
        echo '<tr><td colspan="9">No se encontraron resultados para "'.htmlspecialchars($searchTerm).'"</td></tr>';
    }
    
} catch(Exception $e) {
    echo '<tr><td colspan="9">Error en la búsqueda: '.htmlspecialchars($e->getMessage()).'</td></tr>';
} finally {
    if(isset($stmt)) $stmt->close();
    if(isset($conn)) $conn->close();
}
?>