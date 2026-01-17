function showDataModal(query) {
  // Mostrar un mensaje de carga mientras se obtienen los datos
  $("#modalBody").html("<p>Cargando datos...</p>");

  // Crear una instancia del modal y abrirlo
  const modal = new bootstrap.Modal(document.getElementById("resultModal"));
  modal.show();

  // Hacer la solicitud AJAX para obtener los datos
  $.post(
    "ajax/fetch_record.php",
    {
      query: query,
    },
    function (data) {
      // Actualizar el contenido del modal con los datos recibidos
      $("#modalBody").html(data);
    }
  ).fail(function () {
    // Manejar errores en la solicitud
    $("#modalBody").html(
      "<p>Error al obtener los datos. Por favor, intenta nuevamente.</p>"
    );
  });
}
