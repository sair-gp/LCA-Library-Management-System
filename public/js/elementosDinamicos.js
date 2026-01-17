/**
 * Crear o actualizar un <span> dinámico para mostrar mensajes de validación o información.
 *
 * @param {HTMLElement} elemento - El elemento de entrada (input, textarea, etc.) asociado al mensaje.
 * @param {HTMLElement} existingSpan - Un <span> existente que se está reutilizando (si lo hay).
 * @param {string} texto - El mensaje que se desea mostrar en el <span>.
 * @param {string} redOrGreen - Clase opcional para definir el color del mensaje (e.g., "red" para error o "green" para éxito).
 * @param {boolean} remove - Bandera para indicar si el <span> debe ser eliminado. Si es `true`, elimina el <span> y reinicia el borde del elemento.
 */

function crearSpan(
  elemento,
  existingSpan,
  texto,
  redOrGreen = "",
  remove = ""
) {
  if (remove == true) {
    if (existingSpan && existingSpan.classList.contains("span-message")) {
      existingSpan.innerHTML = "";
      existingSpan.removeAttribute("class");
      elemento.style.border = "";
    }
    return; // Salir porque no es necesario continuar
  }

  if (elemento.value === "") {
    // Si el campo está vacío, limpia el mensaje de error
    if (existingSpan && existingSpan.classList.contains("span-message")) {
      existingSpan.innerHTML = "";
      elemento.style.border = "";
    }
    return; // Salir porque no es necesario continuar
  }

  if (existingSpan && existingSpan.classList.contains("span-message")) {
    // Si ya existe un <span> de error, actualiza su contenido
    existingSpan.innerHTML = texto;

    // Eliminar cualquier clase anterior (red o green)
    const clasesExistentes = ["red", "green"];
    clasesExistentes.forEach((clase) => {
      if (existingSpan.classList.contains(clase)) {
        existingSpan.classList.remove(clase);
      }
    });

    if (redOrGreen) {
      // Agregar nueva clase y borde al input
      existingSpan.classList.add(redOrGreen);
      elemento.style.border = redOrGreen ? "1px solid " + redOrGreen : "";
    } else {
      elemento.style.border = "";
    }
  } else {
    //Agregar borde al elemento
    elemento.style.border = redOrGreen ? "1px solid " + redOrGreen : "";

    // Crear un nuevo <span> si no existe
    const span = document.createElement("span");
    span.classList.add("span-message");
    if (redOrGreen) {
      span.classList.add(redOrGreen);
    }
    span.innerHTML = texto;

    // Insertar el nuevo <span> después del elemento de entrada
    elemento.parentNode.insertBefore(span, elemento.nextSibling);
  }
}
