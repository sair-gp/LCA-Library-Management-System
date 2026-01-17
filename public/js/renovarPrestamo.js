/*const fechaActual = new Date();
console.log(fechaActual);
const spanAdvertenciaFecha = document.getElementById("spanAdvertenciaFecha");
const nuevaFechaInput = document.getElementById("nuevaFecha");
const btnRenovarPrestamo = document.getElementById("btnRenovarPrestamo");
let inicio;
let fechaFormateada;
let fin;
function setRenovarId(id = "") {
  if (id) {
    document.getElementById("idPrestamoRenovar").value = id;
  }

  const button = event.target;
  const row = button.closest("tr");

  const fechaInicioStr = row.querySelector("td:nth-child(5)").innerText.trim();
  const fechaFinStr = row.querySelector("td:nth-child(6)").innerText.trim();

  // Convierte las fechas de las celdas al formato esperado
  inicio = new Date(fechaInicioStr); // Asume formato válido para `new Date`
  fin = new Date(fechaFinStr);
  fin.setDate(fin.getDate() + 1);

  console.log("fecha fin: " + fin);

  // Calcula la diferencia en días
  const diferencia = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24));

  if (fin < fechaActual) {
    console.log("Prestamo vencido");
    console.log("diferencia: " + diferencia);
    //  return;
  }

  if (diferencia < 14) {
    const diasExtensionPrestamo = 14 - diferencia;
    crearSpan(
      spanAdvertenciaFecha,
      spanAdvertenciaFecha.nextElementSibling,
      `El usuario puede solicitar una renovación de un tiempo máximo de ${diasExtensionPrestamo} día(s).`,
      "green",
      false,
      true
    );
    console.log("diferencia dentro del if: " + diferencia);
    console.log("fecha fin dentro if diferencia: " + fin);
    // Calcula la nueva fecha
    const nuevaFecha = new Date(fechaActual);
    nuevaFecha.setDate(fin.getDate() + diasExtensionPrestamo);

    // Formatea la fecha en `yyyy-MM-dd`
    const anio = nuevaFecha.getFullYear();
    const mes = String(nuevaFecha.getMonth() + 1).padStart(2, "0");
    const dia = String(nuevaFecha.getDate()).padStart(2, "0");
    fechaFormateada = `${anio}-${mes}-${dia}`;
    console.log(fechaFormateada);

    // Actualiza el campo de entrada
    nuevaFechaInput.value = fechaFormateada;
    // verificarFechaFormulario(inicio, fechaFormateada);
  } else {
    console.log("El usuario no es apto para solicitar renovación.");
    btnRenovarPrestamo.disabled = true;
  }
}

nuevaFechaInput.addEventListener("input", function () {
  const fechaIngresada = nuevaFechaInput.value
    ? new Date(nuevaFechaInput.value)
    : null;

  if (
    !fechaIngresada ||
    fechaIngresada < fin ||
    fechaIngresada > new Date(fechaFormateada)
  ) {
    crearSpan(
      nuevaFechaInput,
      nuevaFechaInput.nextElementSibling,
      "La fecha no puede ser menor a la fecha de inicio ni mayor a la fecha máxima permitida.",
      "red",
      ""
    );

    btnRenovarPrestamo.disabled = true;
  } else {
    crearSpan(
      nuevaFechaInput,
      nuevaFechaInput.nextElementSibling,
      "",
      "",
      true
    );
    btnRenovarPrestamo.disabled = false;
  }
});

*/