<?php

include_once "app/config/database.php";
$conexion = conexion();
//session_start();
include "app/includes/setearSesiones.php";
include "app/includes/dolarBCV.php";
include_once "app/includes/respaldoAutomatico.php";

function obtenerTotal($conexion, $tabla) {
  $sql = "SELECT COUNT(*) AS total FROM $tabla;";
  $stmt = $conexion->prepare($sql);
  $stmt->execute();
  $resultado = $stmt->get_result()->fetch_assoc();
  $stmt->close();
  return $resultado['total'];
}

$total_libros = obtenerTotal($conexion, 'libros');
$total_prestamos_hoy = obtenerTotal($conexion, 'prestamos WHERE (estado = 1 OR estado = 4) AND DATE(fecha_inicio) = CURDATE()');
$total_asistencias_hoy = obtenerTotal($conexion, 'asistencias WHERE DATE(fecha) = CURDATE()');
$total_prestamos = obtenerTotal($conexion, 'prestamos WHERE estado = 1 OR estado = 4');



// Datos para los charts

// Bar chart
$sql = "SELECT l.titulo, p.cota, count(p.id) AS cantidad, p.fecha_inicio FROM prestamos p JOIN ejemplares e ON e.id = p.cota JOIN libros l ON e.isbn_copia = l.isbn WHERE MONTH(fecha_inicio) = MONTH(NOW()) AND YEAR(fecha_inicio) = YEAR(NOW()) group by titulo;";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado) {
  $titulosBarChart = [];
  $cantidadBarChart = [];
  while ($row = $resultado->fetch_assoc()) {
    $titulosBarChart[] = $row["titulo"];
    $cantidadBarChart[] = $row["cantidad"];
  }

  /*echo "<br><br><br>";
  for ($i = 0; $i <= 4; $i++) {
    echo $titulosBarChart[$i] . "<br>";
    echo $cantidadBarChart[$i] . "<br>";
  }*/
}


// Pie chart

//Esta consulta obtiene todos los prestamos hechos en la ultima semana por dia
$sql = "SELECT 
    SUM(CASE WHEN WEEKDAY(p.fecha_inicio) = 6 THEN 1 ELSE 0 END) AS domingo,
    SUM(CASE WHEN WEEKDAY(p.fecha_inicio) = 0 THEN 1 ELSE 0 END) AS lunes,
    SUM(CASE WHEN WEEKDAY(p.fecha_inicio) = 1 THEN 1 ELSE 0 END) AS martes,
    SUM(CASE WHEN WEEKDAY(p.fecha_inicio) = 2 THEN 1 ELSE 0 END) AS miercoles,
    SUM(CASE WHEN WEEKDAY(p.fecha_inicio) = 3 THEN 1 ELSE 0 END) AS jueves,
    SUM(CASE WHEN WEEKDAY(p.fecha_inicio) = 4 THEN 1 ELSE 0 END) AS viernes,
    SUM(CASE WHEN WEEKDAY(p.fecha_inicio) = 5 THEN 1 ELSE 0 END) AS sabado
    FROM prestamos p
    WHERE YEARWEEK(p.fecha_inicio, 1) = YEARWEEK(NOW(), 1);";

$stmt = $conexion->prepare($sql);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado) {
  $cantidadPrestadosPorDia = [];
  $row = $resultado->fetch_assoc();
  //Obtiene la cantidad de prestamos por dia y los convierte a entero para no tener problemas con apexcharts
  $cantidadPrestadosPorDia = [
    (int)($row["lunes"] ?? 0),
    (int)($row["martes"] ?? 0),
    (int)($row["miercoles"] ?? 0),
    (int)($row["jueves"] ?? 0),
    (int)($row["viernes"] ?? 0),
    (int)($row["sabado"] ?? 0),
    (int)($row["domingo"] ?? 0),
  ];

  // echo "<br><br><br>";
  //var_dump($cantidadPrestadosPorDia);
}




// Obtener libros populares
$todasLasObras = "SELECT l.isbn, l.titulo, l.portada, l.anio, e.nombre AS editorial
        FROM libros l
        LEFT JOIN prestamos p ON p.cota IN (SELECT id FROM ejemplares WHERE isbn_copia = l.isbn)
        LEFT JOIN editorial e ON l.editorial = e.id
        GROUP BY l.isbn
        ORDER BY COUNT(p.id) DESC
        LIMIT 10";
$sql = "SELECT l.isbn, l.titulo, l.portada, l.anio, e.nombre AS editorial, COUNT(p.id) AS total_prestamos
FROM libros l
INNER JOIN ejemplares ej ON ej.isbn_copia = l.isbn
INNER JOIN prestamos p ON p.cota = ej.id
INNER JOIN editorial e ON l.editorial = e.id
GROUP BY l.isbn
ORDER BY total_prestamos DESC
LIMIT 10;";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$libros_populares = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();



?>



<div class="grid-contenedor">

  <div class="div-principal">
    <div class="titulo-principal">
      <h2 style="color: beige;"></h2>
    </div>
    <br>
    <div id="buscadorRegistrosBibliograficos">
      <input type="text" id="buscador" placeholder="Buscar por ISBN, título, autor, etc." onkeyup="buscarLibros()" style="width: 100%; height: 37px; border-radius: 5px;">
      <div id="resultado"></div>
    </div>
  </div>




  <div class="cartas-principales">

    <div class="carta" onclick="location.href='index.php?vista=libros&pagina=1'">
      <div class=" carta-interna">
        <h3>Libros</h3>
        <img style=" height: 5vh;" src="public/img/libros.gif" alt="libros">
      </div>
      <h1><?php echo $total_libros; ?></h1>
    </div>

    <div class="carta">
      <div class="carta-interna">
        <h4>Asistencias de hoy</h4>
        <img style="height: 5vh;" src="public/img/asistencia.gif" alt="asistencia">
      </div>
      <h1><?php echo $total_asistencias_hoy; ?></h1>
    </div>

    <div class="carta" onclick="location.href='index.php?vista=categorias&pagina=1'">
      <div class="carta-interna">
        <h3>Prestamos del dia de hoy</h3>
        <img style="height: 5vh;" src="public/img/category_icon.gif" alt="categoria">
      </div>
      <h1><?php echo $total_prestamos_hoy; ?></h1>
    </div>

    <div class="carta" onclick="location.href='index.php?vista=prestamos&pagina=1'">
      <div class="carta-interna">
        <h3>Prestamos en curso</h3>
        <img style="height: 5vh;" src="public/img/prestamos.gif" alt="prestamos">
      </div>
      <h1><?php echo $total_prestamos; ?></h1>
    </div>

  </div>




  <div class="libros-populares">
        <h2>Obras Populares</h2>
        <div class="carrusel-container">
            <button class="carrusel-btn prev">&#10094;</button>
            <div class="carrusel">
                <?php foreach ($libros_populares as $libro): ?>
                    <div class="carrusel-item">
                        <img src="<?php echo $libro['portada']; ?>" alt="<?php echo $libro['titulo']; ?>">
                        <div class="carrusel-info">
                            <h3><?php echo $libro['titulo']; ?></h3>
                            <p><?php echo $libro['editorial']; ?> (<?php echo $libro['anio']; ?>)</p>
                            
                            <a href="index.php?vista=fichaLibro&isbn=<?php echo $libro['isbn']; ?>" class="ver-ficha-btn">
                            Ver ficha
                        </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carrusel-btn next">&#10095;</button>
        </div>
    </div>






    <?php

    if (!empty($cantidadPrestadosPorDia) && !empty($titulosBarChart) && !empty($cantidadBarChart)):
  ?>
  <div class="graficas" style="display: none;">

    <div class="carta-graficas">
      <h2 class="titulo-carta">TOP <?php echo COUNT($cantidadBarChart, COUNT_NORMAL); ?> LIBROS DEL MES</h2>
      <div id="bar-chart"></div>
    </div>

    <div class="carta-graficas">
      <h2 class="titulo-carta">PRESTADOS EN LA ÚLTIMA SEMANA</h2>
      <div id="pie-chart"></div>
    </div>

  </div>

    <?php endif; ?>


</div>
</div>

<script defer src="public/js/dist/apexcharts.js"></script>
<script>
  var titulosBarChart = <?php echo json_encode($titulosBarChart, JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_APOS); ?>;
  var cantidadBarChart = <?php echo json_encode($cantidadBarChart, JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_APOS); ?>;

  var cantidadPrestadosPorDia = <?php echo json_encode($cantidadPrestadosPorDia, JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_APOS); ?>;

  //buscadorRegistrosBibliograficos

  // Función para buscar libros en tiempo real
  function buscarLibros() {
    let query = document.getElementById('buscador').value;
    let resultadoDiv = document.getElementById('resultado');

    if (query.length < 1) {
      resultadoDiv.innerHTML = '';
      resultadoDiv.classList.remove('active');
      return;
    }

    fetch('public/js/ajax/busquedaHome.php?query=' + encodeURIComponent(query))
  .then(response => response.json())
  .then(data => {
    // Verificar si el array contiene solo ceros (no hay resultados reales)
    const hasRealResults = data.some(item => item !== 0);
    
    let html = '';

    if (hasRealResults) {
      // Filtrar los ceros por si acaso
      const librosReales = data.filter(libro => libro !== 0);
      
      librosReales.forEach(libro => {
        html += `<div class='libro' style="cursor: pointer;" onclick="window.location.href='index.php?vista=fichaLibro&isbn=${libro.isbn}'">
                     
                      <img src='${libro.portada}' alt='Portada'>
                      <div>
                          <strong>${libro.titulo}</strong><br>
                          <small>${libro.autores} - ${libro.editorialN} (${libro.anio})</small>
                      </div>
                     
                  </div>`;
      });
      resultadoDiv.innerHTML = html;
      resultadoDiv.classList.add('active');
    } else {
      resultadoDiv.innerHTML = '<center><p>No se encontraron resultados.</p></center>';
      resultadoDiv.classList.add('active');
    }
  })
  .catch(error => {
    console.error('Error al obtener los datos:', error);
    resultadoDiv.innerHTML = '<p>Ocurrió un error en la búsqueda.</p>';
    resultadoDiv.classList.add('active');
  });
  }









  document.addEventListener("DOMContentLoaded", function () {
    const carrusel = document.querySelector(".carrusel");
    const items = document.querySelectorAll(".carrusel-item");
    const prevBtn = document.querySelector(".prev");
    const nextBtn = document.querySelector(".next");
    let index = 0;
    const totalItems = items.length;

    function actualizarCarrusel() {
        carrusel.style.transition = "transform 0.5s ease-in-out";  // Asegurando la animación
        carrusel.style.transform = `translateX(-${index * 100}%)`;
    }

    function moverSiguiente() {
        index = (index + 1) % totalItems;  // Ciclado correcto, al llegar al final va al inicio
        actualizarCarrusel();
    }

    function moverAnterior() {
        index = (index - 1 + totalItems) % totalItems;  // Ciclado correcto, al llegar al inicio va al final
        actualizarCarrusel();
    }

    nextBtn.addEventListener("click", moverSiguiente);
    prevBtn.addEventListener("click", moverAnterior);

    setInterval(moverSiguiente, 10000);
});


</script>

<script>
//history.replaceState("", null, "www.athenaLS.com/vista=home");
</script>

<script defer src="public/js/home.js"></script>