document.addEventListener("DOMContentLoaded", function () {
  function escapeSQL(value) {
    if (typeof value !== "string") return value;
    return value
      .replace(/\\/g, "\\\\") // Escapa barras invertidas
      .replace(/'/g, "\\'") // Escapa comillas simples
      .replace(/"/g, '\\"') // Escapa comillas dobles
      .replace(/\n/g, "\\n") // Escapa nuevas líneas
      .replace(/\r/g, "\\r") // Escapa retornos de carro
      .replace(/\x00/g, "\\x00") // Escapa caracteres nulos
      .replace(/\x1a/g, "\\x1a"); // Escapa el carácter de sustitución
  }

  document
    .getElementById("inputTermino")
    .addEventListener("input", function () {
      const terminoBusqueda = document.getElementById("inputTermino").value;
      const idTabla = document.querySelector("tbody").id; // Identifica la tabla activa
      console.log(idTabla);

      if (!idTabla) {
        console.error("No se pudo identificar la tabla activa.");
        return;
      }

      // Validar que no esté vacío
      if (terminoBusqueda.trim() === "") {
        console.log("Por favor ingrese un término de búsqueda.");
      }

      // URL del backend
      const url = "public/js/ajax/busquedaUniversal.php";

      // Parámetros para la solicitud
      const params = new URLSearchParams({
        term: terminoBusqueda,
        idTabla: idTabla,
      });

      // Realizar la solicitud AJAX
      const xhr = new XMLHttpRequest();
      xhr.open("GET", `${url}?${params.toString()}`, true);

      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          try {
            // Parsear los resultados
            const resultados = JSON.parse(xhr.responseText);
            actualizarTabla(resultados, idTabla);
          } catch (error) {
            console.error("Error procesando los datos recibidos:", error);
          }
        }
      };
      xhr.send();
    });

  // Función para actualizar la tabla con los resultados
  function actualizarTabla(data, idTabla) {
    const tabla = document.getElementById(idTabla);
    if (!tabla) return;

    let html = "";
    if (data && data.length > 0) {
      let consulta;
      let consultaSerializada;
      data.forEach((row) => {
        switch (idTabla) {
          case "tablaVisitantes":
            html += `
                  <tr>
                      <td>${row.cedula}</td>
                      <td>${row.nombre}</td>
                      <td>${row.telefono}</td>
                      <td>${row.direccion}</td>
                      <td>${row.sexo_descripcion}</td>
                       <td>
                                <button type="button" class="modalBtnAsistencias btn btn-success" data-bs-toggle='modal' data-bs-target='#asistenciasModal'><i class="bi bi-person-lines-fill"></i></button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-success" onclick="window.location.href='index.php?vista=fichaLector&cedulaVisitante=<?php echo $row['cedula'] ?>'"><i class="bi bi-list-nested"></i></button>
                            </td>
                  </tr>`;
            break;

          case "tablaAutores":
            let nombre = escapeSQL(row["nombre"]);
            //let apellido = escapeSQL(row["apellido"]);

            // Generar la consulta SQL
            consulta = `SELECT libros.isbn, libros.titulo, GROUP_CONCAT(DISTINCT categorias.nombre SEPARATOR ', ') AS categorias FROM libros JOIN libro_autores ON libros.isbn = libro_autores.isbn_libro JOIN autores ON libro_autores.id_autor = autores.id JOIN libro_categoria ON libros.isbn = libro_categoria.isbn_libro JOIN categorias ON libro_categoria.id_categoria = categorias.id WHERE libros.delete_at = 0 AND autores.nombre LIKE '%${nombre}%' GROUP BY libros.isbn ORDER BY libros.isbn`;

            // Serializar y escapar la consulta para HTML
            consultaSerializada = JSON.stringify(consulta)
              .replace(/'/g, "\\'")
              .replace(/"/g, "&quot;");

            // Generar la tabla con contenido escapado
            html += `
                <tr>
                    <!--td>${row.id}</td-->
                    <td class="editable">
                                        <div class="d-flex align-items-center">
                                            <img src=" ${row.foto} " 
                                                 alt="Foto del autor" 
                                                 class="rounded-circle me-3" 
                                                 style="width: 48px; height: 48px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0"> ${row.nombre} </h6>
                                                <!--small class="text-muted">ID: <?= $row['id'] ?></small-->
                                            </div>
                                        </div>
                                    </td>

                    <td class="text-center">
                                        <a href="index.php?vista=fichaAutor&idAutor=${row.id}" 
                                           class="btn btn-sm btn-outline-success"
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top"
                                           title="Ver detalles">
                                            <i class="bi bi-info-circle"></i> Detalles
                                        </a>
                                    </td>
                </tr>`;
            break;

          case "tablaLibros":
            html += `
                  <tr>
                      <td style="display: none;" class="notEditable">${row.isbn}</td>
                      <td class="notEditable">${row.titulo}</td>
                      <td class="notEditable">${row.autores}</td>
                      <td style="display: none;" class="notEditable">${row.anio}</td>
                      <td class="notEditable" style="display:none;">${row.editorialN}</td>
                      <td class="notEditable">${row.edicion}</td>
                      <td class="notEditable">${row.volumen}</td>
                      <td class="notEditable" style="display:none;">${row.categorias}</td>
                      <td>
                          <button class="btn-general modalbtnDetalles" data-bs-toggle="modal" data-bs-target="#detallesModal">
                              <i class="bi bi-info-circle"></i>
                          </button>
                      </td>
                      <td class="copiasTd">${row.cantidad_ejemplares}</td>
                  </tr>`;
            break;
          case "tablaAsistencias":
            html += `
                  <tr>
                      <td>${row.id}</td>
                      <td>${row.nombre}</td>
                      <td>${row.origen}</td>
                      <td>${row.descripcion}</td>
                      <td>${row.fecha}</td>
                  </tr>`;
            break;
          case "tablaCategorias":
            // Generar la consulta SQL
            consulta = `SELECT libros.isbn, libros.titulo, GROUP_CONCAT(DISTINCT categorias.nombre SEPARATOR ', ') AS categorias FROM libros JOIN libro_autores ON libros.isbn = libro_autores.isbn_libro JOIN autores ON libro_autores.id_autor = autores.id JOIN libro_categoria ON libros.isbn = libro_categoria.isbn_libro JOIN categorias ON libro_categoria.id_categoria = categorias.id WHERE libros.delete_at = 0 AND categorias.nombre LIKE '%${row.nombre}%' GROUP BY libros.isbn ORDER BY libros.isbn`;

            // Serializar y escapar la consulta para HTML
            consultaSerializada = JSON.stringify(consulta)
              .replace(/'/g, "\\'")
              .replace(/"/g, "&quot;");

            html += `
                  <tr>
                      <td>${row.id}</td>
                      <td>${row.nombre}</td>
                      <td style="display: flex; justify-content:center;">
                          <button onclick="showDataModal('${consultaSerializada}')" class="btn btn-success">
                              <i class="bi bi-book"></i>
                          </button>
                      </td>
                  </tr>`;
            break;
          case "tablaEditoriales":
            // Generar la consulta SQL
          
            html += `
                  <tr>
                      <td style="display: none;">${row.id}</td>
                      <td>${row.nombre}</td>
                      <td>${row.origen}</td>
                       <td class="text-center">
                                        <button onclick="window.location.href='index.php?vista=librosEditorial&id=${row.id}'"
                                                class="btn btn-sm btn-outline-success"
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top"
                                                title="Ver libros">
                                            <i class="bi bi-book me-1"></i> Libros
                                        </button>
                        </td>
                  </tr>`;
            break;
          case "tablaHistorial":
            html += `
                  <tr>
                      <td>${row.id}</td>
                      <td>${row.fecha}</td>
                      <td>${row.accion}</td>
                      <td style="font-size: 15.51px;">${row.detalles}</td>
                      <td>${row.nombre}</td>
                  </tr>`;
            break;
          case "tablaPapelera":
            html += `
                  <tr>
                      <td>${row.isbn}</td>
                      <td>${row.titulo}</td>
                      <td>${row.nombre} ${row.apellido}</td>
                      <td>${row.anio}</td>
                      <td>${row.editorialN}</td>
                      <td>${row.edicion}</td>
                      <td>${row.categoria}</td>
                      <td>
                          <button type="button" class="btn btn-success modalbtnRetornar" data-bs-toggle="modal" data-bs-target="#retornarEjemplar">
                              <i class="bi bi-recycle"></i>
                          </button>
                      </td>
                  </tr>`;
            break;
            case "tablaPrestamos":
    // Obtener valores de sesión (simulados en JS)
    const unidad = sessionStorage.getItem("unidades") || "dias"; // Valor por defecto "dias"
    const periodo = parseInt(sessionStorage.getItem("periodo_renovaciones")) || 14; // Valor por defecto 14 días

    // Calcular fechas
    const fechaInicio = new Date(row.fecha_inicio);
    const fechaFin = new Date(row.fecha_fin);
    const fechaActual = new Date();
    fechaActual.setHours(0, 0, 0, 0); // Asegurar que la hora sea 00:00

    // Calcular diferencia en días entre fecha_inicio y fecha_fin
    const diferencia = Math.floor((fechaFin - fechaInicio) / (1000 * 60 * 60 * 24)) + 1;

    // Calcular la fecha máxima de renovación
    const fechaMaxRenovacion = new Date(fechaFin);
    switch (unidad) {
        case "semanas":
            fechaMaxRenovacion.setDate(fechaMaxRenovacion.getDate() + periodo * 7);
            break;
        case "meses":
            fechaMaxRenovacion.setMonth(fechaMaxRenovacion.getMonth() + periodo);
            break;
        default: // "dias"
            fechaMaxRenovacion.setDate(fechaMaxRenovacion.getDate() + periodo);
            break;
    }

    // Determinar si puede renovar
    const puedeRenovar =
        diferencia < 14 &&
        (row.estado === "prestado" || row.estado === "extendido") &&
        fechaFin >= fechaActual &&
        fechaMaxRenovacion >= fechaActual;

    // Resaltar fila si el ID coincide con un parámetro en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const idPrestamoURL = urlParams.get('id');
    const resaltarFila = idPrestamoURL && row.id == idPrestamoURL ? "style='background-color: #ffcc00;'" : "";

    // Definir el título para el botón de "Devolver"
    const titleDevolver = row.estado === 'vencido' && row.tieneMulta
        ? "title='Este préstamo ha sido multado'"
        : "title='El ejemplar ya ha sido devuelto.'";

    // Definir el botón de "Devolver"
    let botonDevolver;
    if (row.estado !== 'devuelto' && row.estado !== 'Devolución tardía' && row.estado !== 'vencido') {
        botonDevolver = `<button type="button" class="modalDevolver btn-general" data-bs-toggle="modal" data-bs-target="#devolver">Devolver</button>`;
    } else if (row.estado === 'vencido' && !row.tieneMulta) {
        botonDevolver = `<button type="button" class="modalDevolverConMulta btn-general" style="background-color: red;" data-bs-toggle="modal" data-bs-target="#modalRegistrarMulta">Multar</button>`;
    } else if (row.estado === 'vencido' && row.tieneMulta) {
        botonDevolver = `<button type="button" class="modalDevolverConMulta btn-general" style="background-color: lightgrey;" data-bs-toggle="modal" data-bs-target="#modalRegistrarMulta" title="Este préstamo ha sido multado" disabled>Multar</button>`;
    } else {
        botonDevolver = `<button type="button" class="btn-general" style="background-color: lightgrey;" ${titleDevolver} disabled>Devolver</button>`;
    }

    // Definir el botón de "Renovar"
    let botonRenovar;
    if (row.estado !== "devuelto" && row.estado !== "vencido") {
        const disabled = puedeRenovar ? "" : "disabled";
        const style = puedeRenovar ? "" : "style='background-color: lightgrey;'";
        const title = puedeRenovar ? "" : "title='No se puede renovar ya que se ha alcanzado el límite máximo de días para el préstamo.'";
        botonRenovar = `<button type="button" class="btn-general" data-bs-toggle="modal" data-bs-target="#renovar" onclick="setRenovarId(event, ${row.id}, ${row.cedula})" ${disabled} ${style} ${title}>Renovar</button>`;
    } else {
        botonRenovar = `<button type="button" class="btn-general" style="background-color: lightgrey;" disabled title="No se puede renovar un préstamo ${row.estado}.">Renovar</button>`;
    }

    // Definir el botón de impresión de ticket
    const botonTicket = `<a href="app/reportes/ticket.php?idPrestamo=${row.id}" target="_blank" class="btn btn-success"><i class="bi bi-receipt"></i></a>`;

    // Determinar si al menos un botón está habilitado
    const botonesHabilitados = [
        !botonDevolver.includes("disabled"),
        !botonRenovar.includes("disabled")
    ].some(habilitado => habilitado);

    // Construir la fila de la tabla
    html += `
        <tr ${resaltarFila}>
            <td>${row.id}</td>
            <td>${row.titulo}</td>
            <td>${row.cota}</td>
            <td>${row.nombre}</td>
            <td>${row.fecha_inicio}</td>
            <td>${row.fecha_fin}</td>
            <td>${row.estado}</td>
            <td>
                ${botonesHabilitados ? botonDevolver : ""}
                ${botonesHabilitados ? botonRenovar : ""}
                ${!botonesHabilitados ? botonDevolver : ""} <!-- Mostrar solo uno deshabilitado si ninguno está habilitado -->
            </td>
            <td>
                ${botonTicket}
            </td>
        </tr>`;
    break;

    case "tablaLibros":
      html += `
          <tr>
              <td>${row.isbn}</td>
              <td>${row.titulo}</td>
              <td>${row.autores}</td>
              <td>${row.anio}</td>
              <td>${row.editorialN}</td>
              <td>${row.volumen}</td>
              <td>${row.edicion}</td>
              <td>${row.categorias}</td>
              <td>${row.cantidad_ejemplares}</td>
              <td style="display: flex; justify-content:center;">
                  <button type="button" class="btn btn-info modalbtnDetalles" data-bs-toggle="modal" data-bs-target="#detallesLibro" data-isbn="${row.isbn}">
                      <i class="bi bi-eye"></i>
                  </button>
              </td>
          </tr>`;
      break;

      case "tablaUsuarios":
    data.forEach((row) => {
        // Verificar si el usuario es admin para mostrar la columna de permisos
        const esAdmin = true;

        // Construir la fila de la tabla
        html += `
            <tr>
                <td class="editable">
                    <div class="user-info-container">
                        <!-- Mostrar la imagen de perfil o una por defecto -->
                        <img src="${row.foto_perfil ? `data:image/jpeg;base64,${row.foto_perfil}` : 'public/img/userDefault.jpg'}" 
                             alt="me" class="user-img fotoPerfilTablaUser">
                        <div>
                            <div class="user-name">
                                ${row.nombre} ${row.apellido}
                            </div>
                            <div class="user-email">
                                ${row.correo}
                            </div>
                        </div>
                    </div>
                </td>
                <td style="text-align: center;" class="notEditable">${row.cedula}</td>
                <td style="text-align: center;" class="notEditable">${row.rol_nombre}</td>
                <td style="text-align: center;" class="notEditable">${row.direccion}</td>
                <td style="text-align: center;" class="notEditable">${row.telefono}</td>
                <td style="text-align: center;" class="notEditable">${row.sexo_descripcion}</td>
                <td style="text-align: center;" class="notEditable">${row.edad}</td>
                ${esAdmin ? `
                    <td style="display: flex; justify-content:center;">
                        <button type="button" class="btn btn-warning modalbtnPermisos" 
                                data-bs-toggle="modal" data-bs-target="#editarPermisos" 
                                data-cedula="${row.cedula}">
                            <i class="bi bi-key"></i>
                        </button>
                    </td>
                ` : ''}
            </tr>
        `;
    });
    break;
  
          default:
            html += `<tr><td colspan="9">Tabla no soportada.</td></tr>`;
        }
      });
    } else {
      html = `<tr><td colspan="9">No se encontraron registros.</td></tr>`;
    }

    tabla.innerHTML = html;
  }
});
