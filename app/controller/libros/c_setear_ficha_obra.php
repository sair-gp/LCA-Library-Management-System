<?php


if (session_status() == PHP_SESSION_NONE) {
    // La sesión no está activa, podemos iniciarla
    session_start();
    //echo "Sesión iniciada correctamente.";
  } else if (session_status() == PHP_SESSION_ACTIVE) {
    // La sesión ya está activa, no es necesario iniciarla
   // echo "La sesión ya está activa.";
  } else if (session_status() == PHP_SESSION_DISABLED){
    //echo "Las sesiones estan deshabilitadas";
  }
  
  $rol = $_SESSION["rol"];
  $displayNone = $rol !== 'Admin' ? 'style="display: none"' : "";
  //echo "<br><br><br>" . $_SESSION["rol"];
  
  $isbn = $_GET["isbn"] ?? 0;
  
  $query = "SELECT libros.es_obra_completa ,libros.cota, libros.portada, libros.isbn, libros.titulo, libros.fecha_registro, GROUP_CONCAT(DISTINCT CONCAT(autores.nombre) SEPARATOR ', ') AS autores, libros.anio, editorial.nombre AS editorialN, libros.edicion, GROUP_CONCAT(DISTINCT categorias.nombre SEPARATOR ', ') AS categorias, (SELECT COUNT(*) FROM ejemplares WHERE ejemplares.isbn_copia = libros.isbn AND ejemplares.delete_at = 1) AS cantidad_ejemplares, (SELECT COUNT(*) FROM prestamos JOIN ejemplares ON prestamos.cota = ejemplares.id WHERE ejemplares.isbn_copia = libros.isbn AND prestamos.estado IN (1, 3, 4) AND ejemplares.delete_at = 1) AS en_circulacion, (SELECT COUNT(*) FROM ejemplares JOIN estado_ejemplar ON ejemplares.estado = estado_ejemplar.id WHERE estado_ejemplar.id = 3 AND ejemplares.isbn_copia = libros.isbn) AS copias_danadas FROM libros JOIN libro_autores ON libros.isbn = libro_autores.isbn_libro JOIN autores ON libro_autores.id_autor = autores.id JOIN editorial ON libros.editorial = editorial.id JOIN libro_categoria ON libros.isbn = libro_categoria.isbn_libro JOIN categorias ON libro_categoria.id_categoria = categorias.id WHERE libros.isbn = ? GROUP BY libros.isbn ORDER BY libros.isbn;";
  
  $stmt = $conexion->prepare($query);
  $stmt->bind_param('s', $isbn);
  $stmt->execute();
  $resultado = $stmt->get_result();
  
  if ($row = $resultado->fetch_assoc()) {
    $bookData = [
      "image" => $row['portada'] ?? "public/img/libros/default.jpg", // Usar imagen por defecto si está vacío
      "title" => $row['titulo'],
      "author" => $row['autores'],
      "publisher" => $row['editorialN'],
      "edition" => $row['edicion'],
      "description" => "", // Se puede reemplazar con el valor real de la base de datos si es necesario
      "isbn" => $row['isbn'],
      "subjects" => $row['categorias'],
      "ddc" => $row["cota"], // Se puede reemplazar con el valor real de la base de datos si es necesario
      "holdings" => $row['en_circulacion'] ?? 0,
      "copias" => $row["cantidad_ejemplares"] ?? 0,
      "ubicacion" => $row["ubicacion"] ?? NULL,
      "disponibles" => $row["cantidad_ejemplares"] - $row["en_circulacion"] ?? 0,
      "es_obra_completa" => $row["es_obra_completa"]
    ];
  } else {
    // En caso de que no se encuentre ningún libro con el ISBN proporcionado
    echo "<div class='error-message'>
    <h2>Libro no encontrado</h2>
    <p>No se encontró ningún libro con el ISBN proporcionado.</p>
    <a href='index.php?vista=agregarLibro' class='btn'>Agregar Nuevo Libro</a>
  </div>";
    exit;
  }
  
  $query = "SELECT volumen.id, volumen.isbn_obra, volumen.isbn_vol, volumen.numero, volumen.nombre, volumen.anio, volumen.portada, GROUP_CONCAT(DISTINCT LEFT(ej.cota, 3) ORDER BY LEFT(ej.cota, 3) SEPARATOR ', ') AS cotas_base, COUNT(DISTINCT ej.id) AS copias, (SELECT COUNT(*) FROM prestamos JOIN ejemplares e ON prestamos.cota = e.id WHERE prestamos.estado IN (1, 3, 4) AND e.estado = 2 AND e.isbn_vol = volumen.id AND e.delete_at = 1) AS circulacion FROM volumen JOIN libros ON volumen.isbn_obra = libros.isbn JOIN ejemplares ej ON ej.isbn_vol = volumen.id WHERE volumen.isbn_obra = '$isbn' AND libros.es_obra_completa = " . $bookData["es_obra_completa"] ." AND ej.delete_at = 1 GROUP BY volumen.id, volumen.isbn_obra, volumen.isbn_vol, volumen.numero, volumen.nombre, volumen.anio, volumen.portada;";
  $resultado = $conexion->query($query);
  
  $volumenes = array(); // Array para almacenar los datos de los volúmenes
  
  // Bucle while para recorrer los resultados y almacenarlos en el array $volumenes
  while ($row = $resultado->fetch_assoc()) {
    $volumen = [
      "id" => $row["id"],
      "isbn_obra" => $row['isbn_obra'],
      "isbn_vol" => $row['isbn_vol'],
      "numero" => $row['numero'],
      "nombre" => $row['nombre'],
      "anio" => $row['anio'],
      "localizacion" => "estante",
      "copias" => intval($row['copias']) ?? 0,
      "circulacion" => $row['circulacion'] ?? 0,
      "disponibles" => $row['copias'] - $row['circulacion'] ?? 0,
      "portada" => $row['portada'],
      "cotasVol" => $row['cotas_base']
    ];
  
    $volumenes[] = $volumen; // Agregar el volumen actual al array de volúmenes
    //echo '<br><br><br>' . $row['cotas_base'];
    $ddcDes = $bookData["ddc"];
    $isbnVolDes = $volumenes[0]["isbn_vol"];
    $tituloDes = $bookData["title"];
  }
  //echo "<br><br>";
  //var_dump($volumenes);
  
