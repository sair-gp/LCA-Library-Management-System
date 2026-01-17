/*inputPaLasAlertas = document.getElementById("alertaDulce").value;

if (inputPaLasAlertas !== "") {
  switch (inputPaLasAlertas) {
    case "exito":
      Swal.fire({
        title: "¡Éxito!",
        text: "La operación se ha realizado correctamente.",
        icon: "success",
      });
      break;
    case "error":
      Swal.fire({
        title: "¡Error!",
        text: "Ha ocurrido un error inesperado. Por favor, intente nuevamente.",
        icon: "error",
      });
      break;

    case "incompleto":
      Swal.fire({
        title: "¡Error!",
        text: "Datos incompletos. Por favor, rellene todos los campos necesarios e intente nuevamente.",
        icon: "error",
      });
      break;
    case "desincorporado":
      Swal.fire({
        title: "¡Aviso!",
        text: "El ejemplar ha sido desincorporado del sistema.",
        icon: "warning",
      });
      break;

    case "warning":
      Swal.fire({
        title: "¡Aviso!",
        text: ".",
        icon: "warning",
      });
      break;

    case "!login":
      Swal.fire({
        title: "¡Datos incorrectos!",
        text: "Verifique e intente nuevamente",
        icon: "warning",
      });
      break;


      case "!conexion":
      Swal.fire({
        title: "¡Error de conexión!",
        text: "No se ha podido establecer la conexión con el servidor, intente nuevamente más tarde.",
        icon: "warning",
      });
      break;

  }
}*/


function alertaDulce(tipoAlerta) {
  let rtn = false;
  if (tipoAlerta) {
      switch (tipoAlerta) {
          case "exito":
              Swal.fire({
                  title: "¡Éxito!",
                  text: "La operación se ha realizado correctamente.",
                  icon: "success",
              });
              rtn = true;
              break;
          case "error":
              Swal.fire({
                  title: "¡Error!",
                  text: "Ha ocurrido un error inesperado. Por favor, intente nuevamente.",
                  icon: "error",
              });
              rtn = true;
              break;
          case "incompleto":
              Swal.fire({
                  title: "¡Error!",
                  text: "Datos incompletos. Por favor, rellene todos los campos necesarios e intente nuevamente.",
                  icon: "error",
              });
              rtn = true;
              break;
          case "desincorporado":
              Swal.fire({
                  title: "¡Aviso!",
                  text: "El ejemplar ha sido desincorporado del sistema.",
                  icon: "warning",
              });
              rtn = true;
              break;
          case "warning":
              Swal.fire({
                  title: "¡Aviso!",
                  text: ".",
                  icon: "warning",
              });
              rtn = true;
              break;
          case "!login":
              Swal.fire({
                  title: "¡Datos incorrectos!",
                  text: "Verifique e intente nuevamente",
                  icon: "warning",
              });
              rtn = true;
              break;
          case "!conexion":
              Swal.fire({
                  title: "¡Error de conexión!",
                  text: "No se ha podido establecer la conexión con el servidor, intente nuevamente más tarde.",
                  icon: "warning",
              });
              rtn = true;
              break;
          default:
              rtn = false;
      }
  }
  return rtn;
}

// =============================================
// MANEJADOR DE NOTIFICACIONES DESDE LA URL
// =============================================
document.addEventListener('DOMContentLoaded', function() {
  const urlParams = new URLSearchParams(window.location.search);
  const alerta = urlParams.get('alerta'); // Ej: 'exito', 'error', etc.
  
  if (alerta) {  
      if (alertaDulce(alerta)) { // Pasamos el parámetro de la URL a la función
          // Limpiamos la URL después de mostrar la alerta
          urlParams.delete('alerta');
          const newUrl = window.location.pathname + 
                       (urlParams.toString() ? '?' + urlParams.toString() : '');
          window.history.replaceState({}, '', newUrl);
      }
  }
});
