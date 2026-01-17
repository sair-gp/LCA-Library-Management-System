<?php

date_default_timezone_set('America/Caracas');

// Configuración para mostrar errores (solo para desarrollo)
error_reporting(E_ALL); // Reportar todos los errores
ini_set('display_errors', 1); // Mostrar errores en pantalla

try {
  include "app/config/database.php";
  $conexion = conexion();

  // Verificar si la conexión a la base de datos se estableció correctamente
  if (!isset($conexion) || !$conexion) {
    throw new Exception("Error de conexión: No se pudo conectar a la base de datos. Revisa la configuración.");
  }

  $cedulaVisitante = $_GET["cedulaVisitante"] ?? "0";

  //Validar que la cedula sea un numero entero
  if (!filter_var($cedulaVisitante, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])){
    throw new Exception("Error de validación: La cédula ingresada es inválida. Debe ser un número entero mayor o igual a 1.");
  }

  $query = "SELECT cedula, nombre, telefono, direccion, sexo, fecha_registro, foto, correo, activo FROM visitantes WHERE cedula = ?";

  $stmt = $conexion->prepare($query);
  if (!$stmt){
    throw new Exception("Error en la consulta SQL: No se pudo preparar la consulta. Detalles: " . $stmt->error);
  }

  $stmt->bind_param("i", $cedulaVisitante);
  if (!$stmt->execute()){
    throw new Exception("Error en la consulta SQL: No se pudo ejecutar la consulta. Detalles: " . $stmt->error);
  }

  $resultado = $stmt->get_result();
  if (!$resultado){
    throw new Exception("Error al obtener resultados.");
  }

  $fila = $resultado->fetch_assoc();
  if (!$fila) {
    throw new Exception("Error: No se encontró el visitante con la cedula proporcionada.");
  }

  $readerData = [
  "name" => $fila["nombre"],
  "email" => $fila["correo"] ?? "Este usuario no posee correo electrónico.",
  "telefono" => $fila["telefono"],
  "id_number" => $fila["cedula"],
  "registration_date" => $fila["fecha_registro"],
  "sanctions" => $fila["sancion"] ?? "1 multa pendiente",
  "pfp" => $fila["foto"] ?? "public/img/visitantes/default.jpg",
  "direccion" => $fila["direccion"] ?? "public/img/visitantes/default.jpg",
  "sexo" => $fila["sexo"],
  'activo' => $fila["activo"]

  ];

  $stmt->close();











  //Query para obtener historial de prestamos del usuario

  $query2 = "SELECT v.cedula,   CASE
        WHEN l.es_obra_completa = 1 THEN
            CONCAT(l.titulo, ' ejemplar: ', ej.cota)
        ELSE
            CASE
                WHEN REGEXP_REPLACE(vo.nombre, '[0-9]', '') = l.titulo THEN
                    CONCAT(l.titulo, ' ', 'volumen ', vo.numero, ' ejemplar: ', ej.cota)
                ELSE
                    CONCAT(l.titulo, ' \"', vo.nombre, '\". ', ' ejemplar: ', ej.cota)
            END
    END AS titulo, p.fecha_inicio, p.fecha_fin, p.fecha_devolucion, ep.estado, vo.portada FROM libros l JOIN ejemplares ej ON l.isbn = ej.isbn_copia JOIN prestamos p ON p.cota = ej.id JOIN visitantes v ON v.cedula = p.lector JOIN estado_prestamo ep ON ep.id = p.estado LEFT JOIN volumen vo ON vo.isbn_obra = l.isbn WHERE v.cedula = ? AND vo.id = ej.isbn_vol";

  $stmt2 = $conexion->prepare($query2);
  if (!$stmt2){
    throw new Exception("Error en la segunda consulta SQL: No se pudo preparar la consulta. Detalles: " . $stmt->error);
  }

  $stmt2->bind_param("i", $cedulaVisitante);
  if (!$stmt2->execute()){
    throw new Exception("Error al ejecutar la segunda consulta.");
  }

  $resultaDos = $stmt2->get_result();
  if (!$resultaDos){
    throw new Exception("Error al obtener datos de la segunda consulta.");
  }
  
  $hoy = Date("Y-m-d");
  $fActual = strtotime($hoy);

  // Inicializa el array fuera del bucle
$loanHistory = [];
$returnedLate = [];
$returnedOnTime = [];
$pendiente = [];

while($fila = $resultaDos->fetch_assoc()){
    // Agrega cada préstamo al array (no lo sobrescribes)
    $loanHistory[] = [
        "book_title" => $fila["titulo"],
        "loan_date" => $fila["fecha_inicio"],
        "due_date" => $fila["fecha_fin"],
        "status" => $fila["estado"],
        "portada" => $fila["portada"]
    ];

    // Comprobar si se venció el préstamo
    $fechaDevolucion = $fila["fecha_devolucion"] ? strtotime($fila["fecha_devolucion"]) : null;
    $fechaFin = strtotime($fila["fecha_fin"]);
    
    if ($fila["fecha_devolucion"] == NULL && $fila["estado"] == "vencido"){
        $pendiente[] = [
            "title" => $fila["titulo"], 
            "return_date" => $fila["fecha_devolucion"], 
            "ogReturnDate" => $fila["fecha_fin"]
        ];
    }

    // Comprobar si está devuelto y si se entregó a tiempo
    if ($fila["estado"] == "devuelto" && $fechaFin >= $fActual){
        $returnedOnTime[] = [
            "title" => $fila["titulo"], 
            "return_date" => $fila["fecha_devolucion"]
        ];
    }

    // Comprobar si se entregó tarde
    if ($fechaDevolucion && $fechaDevolucion > $fechaFin){
        $returnedLate[] = [
            "title" => $fila["titulo"], 
            "return_date" => $fila["fecha_devolucion"], 
            "ogReturnDate" => $fila["fecha_fin"]
        ];
    }
}

  $stmt2->close();

  

function duplicarArray(array $array, int $veces): array
{
    $resultado = [];
    for ($i = 0; $i < $veces; $i++) {
        $resultado = array_merge($resultado, $array);
    }
    return $resultado;
}

//$loanHistory = duplicarArray($loanHistory, 5);


} catch (Exception $e) {
 // Mostrar el tipo de error y el mensaje específico
 die("<strong>¡Error!</strong><br><br>
 <strong>Tipo de error:</strong> " . get_class($e) . "<br>
 <strong>Mensaje:</strong> " . $e->getMessage() . "<br>
 <strong>Archivo:</strong> " . $e->getFile() . "<br>
 <strong>Línea:</strong> " . $e->getLine());
}

$deshabilitar = $readerData["activo"] == 0 ? '<button class="btn-suspender" style="background-color: gray" data-bs-toggle="modal" 
            data-bs-target="#modalHabilitarVisitante" id="btnHabilitarVisitante"><i class="bi bi-x-octagon"></i> Deshabilitado</button>' : '<button class="btn-suspender" data-bs-toggle="modal" 
            data-bs-target="#modalDeshabilitarVisitante" id="btnDeshabilitarVisitante"><i class="bi bi-x-octagon"></i> Deshabilitar usuario</button>';
?>

<br><br>

<link rel="stylesheet" href="public/css/fichaLector.css">
<div class="perfil-container">
    <!-- Encabezado del perfil -->
    <div class="perfil-header">
        <div class="perfil-avatar-container">
            <img src="<?php echo htmlspecialchars($readerData['pfp']); ?>" alt="Foto de perfil" class="perfil-avatar">
            <!--div class="perfil-status"></div-->
        </div>
        <div class="perfil-info">
            <h1 class="perfil-nombre"><?php echo htmlspecialchars($readerData['name']); ?></h1>
            <div class="perfil-metadata">
                <span class="perfil-cedula"><i class="bi bi-person-badge"></i> <?php echo htmlspecialchars($readerData['id_number']); ?></span>
                <span class="perfil-sexo"><i class="bi bi-gender-ambiguous"></i> <?php echo ($readerData["sexo"] == 1 ? "Masculino" : "Femenino"); ?></span>
                <?php 
                    $fechaRegistro = new DateTime($readerData['registration_date']);
                    $hoy = new DateTime();
                    $intervalo = $hoy->diff($fechaRegistro);
                    $anosRegistro = $intervalo->y;
                ?>
                <span class="perfil-edad"><i class="bi bi-calendar"></i> <?php echo $anosRegistro . ' año' . ($anosRegistro != 1 ? 's' : '') . ' como visitante'; ?></span>
            </div>
            <div class="perfil-contacto">
                <span class="perfil-telefono"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($readerData['telefono']); ?></span>
                <span class="perfil-correo"><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($readerData['email']); ?></span>
            </div>
        </div>
        <div class="perfil-actions">
            <?= $deshabilitar ?>
            
            <!--button class="btn-imprimir"><i class="bi bi-printer"></i> Imprimir</button-->
        </div>
    </div>

    <!-- Sección principal con pestañas -->
    <div class="perfil-main">
        <div class="perfil-tabs">
            <button class="tab-btn active" data-tab="informacion">Información General</button>
            <button class="tab-btn" data-tab="prestamos">Préstamos Activos</button>
            <button class="tab-btn" data-tab="historial">Historial</button>
            <!--button class="tab-btn" data-tab="estadisticas">Estadísticas</button-->
        </div>

        <div class="perfil-content">
            <!-- Pestaña de Información General -->
            <div class="tab-pane active" id="informacion">
                <div class="info-grid">
                    <div class="info-card">
                        <h3><i class="bi bi-house"></i> Dirección</h3>
                        <p><?php echo htmlspecialchars($readerData["direccion"] ?? 'No especificada'); ?></p>
                    </div>
                    <div class="info-card">
                        <h3><i class="bi bi-calendar-event"></i> Fecha de Registro</h3>
                        <p><?php echo date('d/m/Y', strtotime($readerData['registration_date'])); ?></p>
                    </div>
                    <div class="info-card">
                        <h3><i class="bi bi-exclamation-triangle"></i> Sanciones</h3>
                        <p><?php echo htmlspecialchars($readerData['sanctions']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Pestaña de Préstamos Activos -->
            <div class="tab-pane" id="prestamos">
    <div class="prestamos-container">
        <div class="prestamos-header">
            <h3>Libros en Préstamo</h3>
            <div class="prestamos-stats">
                <?php 
                    $activos = 0;
                    $porVencer = 0;
                    if (isset($loanHistory) && is_array($loanHistory)) {
                        foreach ($loanHistory as $prestamo) {
                            if ($prestamo['status'] == 'prestado' || $prestamo['status'] == 'extendido') {
                                $activos++;
                                $dias_restantes = floor((strtotime($prestamo['due_date']) - time()) / 86400);
                                if ($dias_restantes <= 3) {
                                    $porVencer++;
                                }
                            }
                        }
                    }
                ?>
                <span class="stat-box"><strong><?php echo $activos; ?></strong> activos</span>
                <span class="stat-box"><strong><?php echo $porVencer; ?></strong> por vencer</span>
            </div>
        </div>
        
        <div class="prestamos-scrollable">
            <?php if (isset($loanHistory) && is_array($loanHistory)): ?>
                <?php foreach ($loanHistory as $prestamo): ?>
                    <?php if ($prestamo['status'] == 'prestado' || $prestamo['status'] == 'extendido'): ?>
                        <?php 
                            $estado_class = '';
                            $dias_restantes = floor((strtotime($prestamo['due_date']) - time()) / 86400);
                            if ($dias_restantes <= 3) $estado_class = 'por-vencer';
                        ?>
                        <div class="prestamo-card <?php echo $estado_class; ?>">
                            <div class="prestamo-portada">
                                <img src="<?php echo !empty($prestamo['portada']) ? htmlspecialchars($prestamo['portada']) : 'public/img/default-book.png'; ?>" alt="Portada del libro">
                            </div>
                            <div class="prestamo-info">
                                <h4><?php echo htmlspecialchars($prestamo['book_title']); ?></h4>
                                <div class="prestamo-fechas">
                                    <span><i class="bi bi-calendar-plus"></i> <?php echo date('d/m/Y', strtotime($prestamo['loan_date'])); ?></span>
                                    <span><i class="bi bi-calendar-check"></i> <?php echo date('d/m/Y', strtotime($prestamo['due_date'])); ?></span>
                                </div>
                            </div>
                            <div class="prestamo-status">
                                <span class="dias-restantes"><?php echo $dias_restantes > 0 ? $dias_restantes . ' días' : 'Vencido'; ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if ($activos == 0): ?>
                    <p class="no-results">No hay préstamos activos actualmente.</p>
                <?php endif; ?>
            <?php else: ?>
                <p class="no-results">No hay préstamos activos actualmente.</p>
            <?php endif; ?>
        </div>
    </div>
            </div>

            <!-- Pestaña de Historial -->
            <div class="tab-pane" id="historial">
    <div class="historial-container">
        <div class="historial-filters">
            <select class="filter-select">
                <option>Todos los préstamos</option>
                <option>Últimos 3 meses</option>
                <option>Este año</option>
                <option>Año pasado</option>
            </select>
            <div class="historial-stats">
                <?php 
                    $total = 0;
                    $completados = 0;
                    $retrasos = 0;
                    if (isset($loanHistory) && is_array($loanHistory)) {
                        $total = count($loanHistory);
                        foreach ($loanHistory as $prestamo) {
                            if ($prestamo['status'] == 'devuelto') $completados++;
                        }
                        if (isset($returnedLate) && is_array($returnedLate)) {
                            $retrasos = count($returnedLate);
                        }
                    }
                ?>
                <span class="stat-box"><strong><?php echo $total; ?></strong> total</span>
                <span class="stat-box"><strong><?php echo $completados; ?></strong> completados</span>
                <span class="stat-box"><strong><?php echo $retrasos; ?></strong> retrasos</span>
            </div>
        </div>
        
        <div class="historial-scrollable">
            <?php if (isset($loanHistory) && is_array($loanHistory) && !empty($loanHistory)): ?>
                <?php foreach ($loanHistory as $prestamo): ?>
                    <?php 
                        $estado_icon = $prestamo['status'] == 'devuelto' ||  $prestamo['status'] == 'extendido' ? 'bi-check-circle' : 'bi-exclamation-triangle';
                        $estado_class = $prestamo['status'] == 'devuelto' ||  $prestamo['status'] == 'extendido' ? 'completado' : 'retraso';
                        $dias_retraso = 0;
                        
                        if (isset($returnedLate) && is_array($returnedLate)) {
                            foreach ($returnedLate as $retrasado) {
                                if (isset($retrasado['title']) && $retrasado['title'] == $prestamo['book_title']) {
                                    $dias_retraso = floor((strtotime($retrasado['return_date']) - strtotime($retrasado['ogReturnDate'])) / 86400);
                                }
                            }
                        }
                    ?>
                    <div class="historial-item <?php echo $estado_class; ?>">
                        <div class="historial-icon">
                            <i class="bi <?php echo $estado_icon; ?>"></i>
                        </div>
                        <div class="historial-details">
                            <h4><?php echo htmlspecialchars($prestamo['book_title']); ?></h4>
                            <div class="historial-fechas">
                                <span>Préstamo: <?php echo date('d/m/Y', strtotime($prestamo['loan_date'])); ?></span>
                                <span>Devolución: <?php echo date('d/m/Y', strtotime($prestamo['due_date'])); ?></span>
                            </div>
                        </div>
                        <?php if ($dias_retraso > 0): ?>
                            <div class="historial-retraso">+<?php echo $dias_retraso; ?> días</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-results">No hay historial de préstamos disponible.</p>
            <?php endif; ?>
        </div>
    </div>
            </div>

            <!-- Pestaña de Estadísticas -->
            <!--div class="tab-pane" id="estadisticas">
                <div class="stats-grid">
                    <div class="stats-card big">
                        <h3>Actividad Mensual</h3>
                        <div class="grafico-barras">
                            
                            <div class="barra" style="height: 30%;" data-mes="Ene"></div>
                            <div class="barra" style="height: 45%;" data-mes="Feb"></div>
                            <div class="barra" style="height: 60%;" data-mes="Mar"></div>
                            <div class="barra" style="height: 25%;" data-mes="Abr"></div>
                            <div class="barra" style="height: 40%;" data-mes="May"></div>
                            <div class="barra" style="height: 75%;" data-mes="Jun"></div>
                            <div class="barra" style="height: 80%;" data-mes="Jul"></div>
                            <div class="barra" style="height: 65%;" data-mes="Ago"></div>
                            <div class="barra" style="height: 70%;" data-mes="Sep"></div>
                            <div class="barra" style="height: 55%;" data-mes="Oct"></div>
                            <div class="barra" style="height: 40%;" data-mes="Nov"></div>
                            <div class="barra" style="height: 35%;" data-mes="Dic"></div>
                        </div>
                    </div>
                    <div class="stats-card">
                        <h3>Resumen General</h3>
                        <div class="resumen-item">
                            <span class="resumen-valor"><?php echo $total ?? 0; ?></span>
                            <span class="resumen-label">Préstamos totales</span>
                        </div>
                        <div class="resumen-item">
                            <span class="resumen-valor"><?php echo $retrasos ?? 0; ?></span>
                            <span class="resumen-label">Retrasos</span>
                        </div>
                        <div class="resumen-item">
                            <span class="resumen-valor"><?php echo $total > 0 ? round(($completados / $total) * 100) : 100; ?>%</span>
                            <span class="resumen-label">Puntualidad</span>
                        </div>
                    </div>
                </div>
            </div-->
        </div>
    </div>
</div>





<!-- Modal de Confirmación - Solo HTML -->
<div class="modal fade" id="modalDeshabilitarVisitante" tabindex="-1" aria-labelledby="modalDeshabilitarVisitanteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="modalDeshabilitarVisitanteLabel">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> Deshabilitar Visitante
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="app/controller/visitantes/habilitaciones.php" method="POST">
            <input type="hidden" name="accion" value="deshabilitar">
            <input type="hidden" name="cedula" id="cedulaVisitanteDeshabilitar" value="<?= $readerData["id_number"] ?>">
            <input type="hidden" name="nombre" id="nombreVisitanteDeshabilitar" value="<?= $readerData["name"] ?>">
        <p>¿Confirmas que deseas deshabilitar el acceso del visitante de nombre <strong class="text-danger" id="nombreAqui"><?= $readerData["name"] ?></strong> portador de la cédula <strong class="text-danger" id="cedulaAqui"><?= $readerData["id_number"] ?></strong>?</p>
        <div class="alert alert-warning mb-0">
          <i class="bi bi-info-circle-fill me-2"></i> El registro permanecerá inactivo pero no se eliminará del sistema.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i> Cancelar
        </button>
        <button type="submit" class="btn btn-warning" data-bs-dismiss="modal">
          <i class="bi bi-person-x me-1"></i> Confirmar Deshabilitación
        </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Habilitación - Solo HTML -->
<div class="modal fade" id="modalHabilitarVisitante" tabindex="-1" aria-labelledby="modalHabilitarVisitanteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalHabilitarVisitanteLabel">
          <i class="bi bi-check-circle-fill me-2"></i> Habilitar Visitante
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="app/controller/visitantes/habilitaciones.php" method="POST">
            <input type="hidden" name="accion" value="habilitar">
            <input type="hidden" name="cedula" id="cedulaVisitanteHabilitar" value="<?= $readerData["id_number"] ?>">
            <input type="hidden" name="nombre" id="nombreVisitanteHabilitar" value="<?= $readerData["name"] ?>">
        <p>¿Confirmas que deseas habilitar el acceso del visitante de nombre <strong class="text-success" id="nombreAqui"><?= $readerData["name"] ?></strong> portador de la cédula <strong class="text-success" id="cedulaAqui"><?= $readerData["id_number"] ?></strong>?</p>
        <div class="alert alert-success mb-0">
          <i class="bi bi-info-circle-fill me-2"></i> El registro volverá a estar activo en el sistema.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i> Cancelar
        </button>
        <button type="submit" class="btn btn-success" data-bs-dismiss="modal">
          <i class="bi bi-person-check me-1"></i> Confirmar Habilitación
        </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="public/js/fichaLector.js"></script>