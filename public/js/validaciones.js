var formulario = document.getElementById("formulario");
let inputs;

inputs = document.querySelectorAll("#formulario input");

const expresiones = {
  usuario: /^[a-zA-Z0-9\_\-]{4,16}$/, // Letras, numeros, guion y guion bajo
  nombre: /^[A-Za-zÁáÉéÍíÓóÚúÑñÜü\s]+$/, //Letras y espacios, puede llevar acentos
  password: /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.*\s).{8,}$/,
  /*^: Coincide con el inicio de la cadena.
    (?=.*\d): Al menos un dígito.
    (?=.*[a-z]): Al menos una letra minúscula.
    (?=.*[A-Z]): Al menos una letra mayúscula.
    (?=.*\W): Al menos un carácter especial.
    (?!.*\s): No se permiten espacios en blanco.
    .{8,}: Al menos 8 caracteres en total.
    $: Coincide con el final de la cadena. */
  correo: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
  cedula: /^\d{6,9}$/,
  telefono: /^\d{1,7}$/, //11 numeros
  FechaNacimiento: /^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{4}$/,
};

var validarFormulario = (e) => {
  const targetElement = e.target;
  var span = document.createElement("span");
  var existingSpan;

  switch (targetElement.className) {
    case "nombreUsuarioForm":
      if (expresiones.nombre.test(targetElement.value)) {
        existingSpan = targetElement.nextElementSibling;

        if (existingSpan && existingSpan.classList.contains("error-message")) {
          // Si ya existe un <span> de mensaje de error, simplemente actualiza su contenido
          existingSpan.innerHTML = "";
        }
        targetElement.style.border = "3px solid green";
        targetElement.parentNode.insertBefore(span, targetElement.nextSibling);
      } else {
        targetElement.style.border = "3px solid red";

        // Verificar si ya existe un <span> para este mensaje de error
        existingSpan = targetElement.nextElementSibling;

        if (existingSpan && existingSpan.classList.contains("error-message")) {
          // Si ya existe un <span> de mensaje de error, simplemente actualiza su contenido
          existingSpan.innerHTML = "Este campo solo puede contener letras.";
        } else {
          // Si no existe un <span> de mensaje de error, crea uno nuevo
          span.classList.add("error-message");
          span.innerHTML = "Este campo solo puede contener letras.";

          // Insertar el nuevo <span> después del elemento de entrada
          targetElement.parentNode.insertBefore(
            span,
            targetElement.nextSibling
          );
        }
      }
      if (targetElement.value === "") {
        existingSpan = targetElement.nextElementSibling;

        if (existingSpan && existingSpan.classList.contains("error-message")) {
          // Si ya existe un <span> de mensaje de error, simplemente actualiza su contenido
          existingSpan.innerHTML = "";
        }
      }

      break;

    case "correoForm":
      if (expresiones.correo.test(targetElement.value)) {
        existingSpan = targetElement.nextElementSibling;

        if (existingSpan && existingSpan.classList.contains("error-message")) {
          // Si ya existe un <span> de mensaje de error, simplemente actualiza su contenido
          existingSpan.innerHTML = "";
        }
        targetElement.style.border = "3px solid green";
        targetElement.parentNode.insertBefore(span, targetElement.nextSibling);
      } else {
        targetElement.style.border = "3px solid red";

        // Verificar si ya existe un <span> para este mensaje de error
        existingSpan = targetElement.nextElementSibling;

        if (existingSpan && existingSpan.classList.contains("error-message")) {
          // Si ya existe un <span> de mensaje de error, simplemente actualiza su contenido
          existingSpan.innerHTML =
            "Por favor, introduce una dirección de correo electrónico válida";
        } else {
          // Si no existe un <span> de mensaje de error, crea uno nuevo
          var span = document.createElement("span");
          span.classList.add("error-message");
          span.innerHTML =
            "Por favor, introduce una dirección de correo electrónico válida";

          // Insertar el nuevo <span> después del elemento de entrada
          targetElement.parentNode.insertBefore(
            span,
            targetElement.nextSibling
          );
        }
      }
      if (targetElement.value === "") {
        existingSpan = targetElement.nextElementSibling;

        if (existingSpan && existingSpan.classList.contains("error-message")) {
          // Si ya existe un <span> de mensaje de error, simplemente actualiza su contenido
          existingSpan.innerHTML = "";
        }
      }
      break;

    case "telefonoForm":
      if (expresiones.telefono.test(targetElement.value)) {
        existingSpan = targetElement.nextElementSibling;

        if (existingSpan && existingSpan.classList.contains("error-message")) {
          // Si ya existe un <span> de mensaje de error, simplemente actualiza su contenido
          existingSpan.innerHTML = "";
        }
        targetElement.style.border = "3px solid green";
        targetElement.parentNode.insertBefore(span, targetElement.nextSibling);
      } else {
        targetElement.style.border = "3px solid red";

        // Verificar si ya existe un <span> para este mensaje de error
        existingSpan = targetElement.nextElementSibling;

        if (existingSpan && existingSpan.classList.contains("error-message")) {
          // Si ya existe un <span> de mensaje de error, simplemente actualiza su contenido
          existingSpan.innerHTML =
            "Este campo solo puede contener números con un maximo de 7 digitos.";
        } else {
          // Si no existe un <span> de mensaje de error, crea uno nuevo
          var span = document.createElement("span");
          span.classList.add("error-message");
          span.innerHTML =
            "Este campo solo puede contener números con un maximo de 7 digitos.";

          // Insertar el nuevo <span> después del elemento de entrada
          targetElement.parentNode.insertBefore(
            span,
            targetElement.nextSibling
          );
        }
      }
      if (targetElement.value === "") {
        existingSpan = targetElement.nextElementSibling;

        if (existingSpan && existingSpan.classList.contains("error-message")) {
          // Si ya existe un <span> de mensaje de error, simplemente actualiza su contenido
          existingSpan.innerHTML = "";
        }
      }
      break;

    case "cedulaForm":
      let texto =
        "Este campo debe contener de 6 a 9 numeros, no puede contener letras ni carácteres especiales.";
      if (
        expresiones.cedula.test(targetElement.value) &&
        !expresiones.nombre.test(targetElement.value)
      ) {
        //se usa nombre para comprobar si tiene letras
        existingSpan = targetElement.nextElementSibling;

        if (existingSpan && existingSpan.classList.contains("span-message")) {
          // Si ya existe un <span> de mensaje de error, simplemente actualiza su contenido

          existingSpan.innerHTML = "";
          existingSpan.className = "";
        }
        targetElement.style.border = "3px solid green";
        crearSpan(
          targetElement,
          existingSpan,
          "Formato de cédula válido.",
          (redOrGreen = "green")
        );
      } else {
        targetElement.style.border = "3px solid red";

        // Verificar si ya existe un <span> para este mensaje de error
        existingSpan = targetElement.nextElementSibling;

        if (existingSpan && existingSpan.classList.contains("span-message")) {
          // Si ya existe un <span> de mensaje de error, simplemente actualiza su contenido
          existingSpan.innerHTML = "Este campo debe contener de 6 a 9 numeros.";
        } else {
          // Si no existe un <span> de mensaje de error, crea uno nuevo
          crearSpan(targetElement, existingSpan, texto, (redOrGreen = "red"));
        }
      }
      if (targetElement.value === "") {
        existingSpan = targetElement.nextElementSibling;

        if (existingSpan && existingSpan.classList.contains("span-message")) {
          // Si ya existe un <span> de mensaje de error, simplemente actualiza su contenido
          existingSpan.innerHTML = "";
          existingSpan.className = "";
        }
      }
      break;

    case "fechaForm":
      if (expresiones.FechaNacimiento.test(targetElement.value)) {
        existingSpan = targetElement.nextElementSibling;

        if (existingSpan && existingSpan.classList.contains("error-message")) {
          // Si ya existe un <span> de mensaje de error, simplemente actualiza su contenido
          existingSpan.innerHTML = "";
        }
        targetElement.style.border = "3px solid green";
        targetElement.parentNode.insertBefore(span, targetElement.nextSibling);
      } else {
        targetElement.style.border = "3px solid red";

        // Verificar si ya existe un <span> para este mensaje de error
        existingSpan = targetElement.nextElementSibling;

        if (existingSpan && existingSpan.classList.contains("error-message")) {
          // Si ya existe un <span> de mensaje de error, simplemente actualiza su contenido
          existingSpan.innerHTML = "Introduzca una fecha de nacimiento válida.";
        } else {
          // Si no existe un <span> de mensaje de error, crea uno nuevo
          span.classList.add("error-message");
          span.innerHTML = "Introduzca una fecha de nacimiento válida.";

          // Insertar el nuevo <span> después del elemento de entrada
          targetElement.parentNode.insertBefore(
            span,
            targetElement.nextSibling
          );
        }
      }
      if (targetElement.value === "") {
        existingSpan = targetElement.nextElementSibling;

        if (existingSpan && existingSpan.classList.contains("error-message")) {
          // Si ya existe un <span> de mensaje de error, simplemente actualiza su contenido
          existingSpan.innerHTML = "";
        }
      }
      break;
  }
};

const validarPassword = () => {
  const span = document.createElement("span");
  var existingSpan;

  if (ogPassword) {
    if (expresiones.password.test(ogPassword.value)) {
      existingSpan = ogPassword.nextElementSibling;

      if (existingSpan && existingSpan.classList.contains("error-message")) {
        // Si ya existe un <span> de mensaje de error, simplemente actualiza su contenido
        existingSpan.innerHTML = "";
      }
      ogPassword.style.border = "3px solid green";
      ogPassword.parentNode.insertBefore(span, ogPassword.nextSibling);

      // -------- COMPARAR PASSWORDS --------
      label.style.display = "block";
      confirmPassword.style.display = "block";
      if (ogPassword.value !== confirmPassword.value) {
        confirmPassword.style.border = "3px solid red";
        existingSpan = confirmPassword.nextElementSibling;

        if (existingSpan && existingSpan.classList.contains("error-message")) {
          existingSpan.innerHTML = "Las contraseñas no coinciden.";
        } else {
          span.classList.add("error-message");
          span.innerHTML = "Las contraseñas no coinciden.";

          confirmPassword.parentNode.insertBefore(
            span,
            confirmPassword.nextSibling
          );
        }
      } else {
        confirmPassword.style.border = "3px solid green";
        ogPassword.style.border = "3px solid green";
        console.log("Las contraseñas coinciden");

        existingSpan = confirmPassword.nextElementSibling;
        if (existingSpan) {
          existingSpan.innerHTML = "";
        }
      }
    } else {
      ogPassword.style.border = "3px solid red";

      // Verificar si ya existe un <span> para este mensaje de error
      existingSpan = ogPassword.nextElementSibling;

      if (existingSpan && existingSpan.classList.contains("error-message")) {
        // Si ya existe un <span> de mensaje de error, simplemente actualiza su contenido
        existingSpan.innerHTML =
          "La contraseña debe tener al menos 8 caracteres y contener al menos una letra mayúscula, una minúscula, un número y un carácter especial como: !@#$%&*.";
      } else {
        span.classList.add("error-message");
        span.innerHTML =
          "La contraseña debe tener al menos 8 caracteres y contener al menos una letra mayúscula, una minúscula, un número y un carácter especial como: !@#$%&*.";

        // Insertar el nuevo <span> después del elemento de entrada
        ogPassword.parentNode.insertBefore(span, ogPassword.nextSibling);
      }
    }
    if (ogPassword.value === "") {
      var existingSpan = targetElement.nextElementSibling;

      if (existingSpan && existingSpan.classList.contains("error-message")) {
        // Si ya existe un <span> de mensaje de error, simplemente actualiza su contenido
        existingSpan.innerHTML = "";
        ogPassword.style.border = "none";
      }
    }
  }
};

// -------- VALIDAR ISBN DE 10 DIGITOS ------------

const validarISBN10 = (isbn) => {
  // Verificar si el ISBN tiene 10 caracteres
  if (isbn.length !== 10) {
    return false;
  }

  let suma = 0;
  for (let i = 0; i < 9; i++) {
    suma += parseInt(isbn[i]) * (10 - i);
  }

  console.log("La suma es: ", suma);

  const resto = suma % 11;

  console.log("El resto es: ", resto);

  const digitoControl = resto === 0 ? 0 : 11 - resto;

  console.log("El digito de control es: ", digitoControl);

  // Convertir el dígito de control a 'X' si es 10
  const digitoControlString =
    digitoControl === 10 ? "X" : digitoControl.toString();

  if (digitoControlString === isbn[9]) {
    console.log("El ISBN de 10 es válido.");
  } else {
    console.log("El ISBN de 10 no es válido.");
  }

  return digitoControlString === isbn[9];
};

// -------- VALIDAR ISBN DE 13 DIGITOS ------------

const validarISBN13 = (isbn) => {
  //console.log("dentro de la funcion");

  // Eliminar posibles guiones
  //isbn = isbn.replace(/-/g, '');

  // Verificar si el ISBN tiene 13 caracteres
  if (isbn.length !== 13) {
    return false;
  }

  let suma = 0;
  for (let i = 0; i < 12; i++) {
    suma += parseInt(isbn[i]) * (i % 2 === 0 ? 1 : 3); //Operador ternario para verificar si el indice es par o impar. Si es par se multiplica por 1, si es impar, por 3
  }
  console.log("La suma es: ", suma);

  const resto = suma % 10;

  console.log("El resto es: ", resto);

  const digitoControl = 10 - resto;

  console.log("El digito de control es: ", digitoControl);

  if (digitoControl === parseInt(isbn[12])) {
    console.log("El ISBN de 13 es válido.");
  } else {
    console.log("El ISBN de 13 no es válido.");
  }

  return digitoControl % 10 === parseInt(isbn[12]);
};

// ------ VERIFICAR SI EL ISBN YA EXISTE EN LA BASE DE DATOS -------

async function verificarSiYaExisteISBN(isbn, input) {
  try {
    const response = await fetch(
      "public/js/ajax/validarInputsRegistroLibro.php",
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ isbn: isbn }),
      }
    );

    if (!response.ok) {
      throw new Error("No respondió la red");
    }

    const data = await response.json();
    console.log("El servidor respondió", data);

    if (data.result) {
      // Mostrar errores cuando el ISBN ya exista
      console.log("El ISBN ya existe");
      crearSpan(
        input,
        input.nextElementSibling,
        "Este ISBN ya existe. Por favor, elija otro.",
        "red"
      );
    } else {
      // Mostrar que el ISBN ya esta disponible
      console.log("ISBN está disponible");

      crearSpan(
        input,
        input.nextElementSibling,
        "El ISBN es válido y está disponible.",
        "green"
      );
      document.getElementById("submitBtnRegConIsbn").disabled = false;

    }
  } catch (error) {
    console.error("Error:", error);
  }
}

inputs.forEach((input) => {
  switch (input.className) {
    case "passwordForm":
      ogPassword = input;
      ogPassword.addEventListener("input", validarPassword);
      break;

    case "passwordConfirmForm":
      confirmPassword = input;
      confirmPassword.style.display = "none";
      label = confirmPassword.previousElementSibling;
      label.style.display = "none";
      confirmPassword.addEventListener("input", validarPassword);
      break;

    case "isbnRegistro":
      input.addEventListener("input", () => {

        document.getElementById("submitBtnRegConIsbn").setAttribute('disabled', '');

        // Limitar entrada a números y guiones
        input.value = input.value.replace(/[^0-9-X]/g, "");

        // Si el input ya tiene 13 digitos, no se ejecuta el script, ya que se estaria ejecutando en vano
        if (input.value.length === 17) {
          // Corta la longitud máxima a 13
          input.value = input.value.slice(0, 16);
          return;
        }

        const isbn = input.value;
        const isbnSinGuiones = isbn.replace(/-/g, "");

        if (isbnSinGuiones.length === 13) {
          if (validarISBN13(isbnSinGuiones)) {
            verificarSiYaExisteISBN(isbn, input);
          } else {
            crearSpan(input, input.nextElementSibling, "ISBN inválido.", "red");
          }
        } else if (isbnSinGuiones.length === 10) {
          if (validarISBN10(isbnSinGuiones)) {
            verificarSiYaExisteISBN(isbn, input);
          } else {
            crearSpan(input, input.nextElementSibling, "ISBN inválido.", "red");
          }
        } else {
          // Puedes mostrar un mensaje de error aquí si la longitud es inválida
          console.log("El ISBN debe tener entre 10 y 13 caracteres.");
          crearSpan(
            input,
            input.nextElementSibling,
            "El ISBN debe tener entre 10 y 13 caracteres."
          );
        }
      });

      break;
  }

  if (input.classList.contains("fechaForm")) {
    input.addEventListener("keydown", (e) => {
      e.preventDefault();
    });
  } else {
    input.addEventListener("keyup", validarFormulario);

    input.addEventListener("blur", validarFormulario);
  }
});

/*form.addEventListener("submit", (e) => {
    e.preventDefault();
});*/

/**
 * Valida fechas en un formulario y muestra mensajes de error si es necesario.
 * También puede corregir fechas inválidas y habilitar/deshabilitar un botón según los resultados.
 *
 * @param {Object} params - Objeto de parámetros para personalizar la validación.
 * @param {HTMLElement} [params.inputFechaInicio=null] - Campo de fecha de inicio (opcional).
 * @param {HTMLElement} [params.inputFechaFinal=null] - Campo de fecha de fin (opcional).
 * @param {HTMLElement} [params.boton=null] - Botón que se habilita o deshabilita según la validación.
 * @param {boolean} [params.reemplazar=false] - Si es `true`, reemplaza fechas inválidas con la actual.
 */
function validarFechas({
  inputFechaInicio = null,
  inputFechaFinal = null,
  boton = null,
  reemplazar = false,
}) {
  // Obtiene la fecha actual sin hora (00:00:00) para evitar errores por diferencias horarias.
  const currentDate = new Date(
    new Date().toLocaleString("en-US", {
      timeZone: "America/Caracas",
    })
  );
  currentDate.setHours(0, 0, 0, 0);

  //console.log(currentDate);

  let fechaInicioValida = true; // Indica si la fecha de inicio es válida.
  let rangoFechasValido = true; // Indica si la relación entre inicio y fin es válida.

  // --- Validación de la fecha de inicio ---
  if (inputFechaInicio) {
    const fechaInicio = new Date(inputFechaInicio.value + "T00:00:00"); // Convierte el valor del input a una fecha.
    fechaInicio.setHours(0, 0, 0, 0);
    //console.log(fechaInicio);

    // Si la fecha no es válida, muestra un error y marca la validación como falsa.
    if (isNaN(fechaInicio.getTime())) {
      crearSpan(
        inputFechaInicio,
        inputFechaInicio.nextElementSibling,
        "La fecha ingresada no es válida.",
        "red",
        false
      );
      fechaInicioValida = false;
    }
    // Si la fecha de inicio es menor que la actual, es inválida.
    else if (fechaInicio < currentDate) {
      if (fechaInicio != currentDate) {
        fechaInicioValida = false;
        if (reemplazar) {
          inputFechaInicio.value = currentDate.toISOString().split("T")[0]; // Reemplaza con la fecha actual.
        }
        crearSpan(
          inputFechaInicio,
          inputFechaInicio.nextElementSibling,
          "La fecha no puede ser menor a la actual.",
          "red",
          false
        );
      }
    }
    // Si la fecha es válida, elimina cualquier mensaje de error.
    else {
      crearSpan(
        inputFechaInicio,
        inputFechaInicio.nextElementSibling,
        "",
        "",
        "",
        true
      );
    }
  }

  // --- Validación del rango de fechas (Inicio ≤ Fin) ---
  if (inputFechaInicio && inputFechaFinal) {
    const fechaInicio = new Date(inputFechaInicio.value + "T00:00:00");
    const fechaFinal = new Date(inputFechaFinal.value + "T00:00:00");

    // Si alguna fecha no es válida, muestra un error.
    if (isNaN(fechaInicio.getTime()) || isNaN(fechaFinal.getTime())) {
      crearSpan(
        inputFechaFinal,
        inputFechaFinal.nextElementSibling,
        "Las fechas ingresadas no son válidas.",
        "red",
        false
      );
      rangoFechasValido = false;
    }
    // Si la fecha de inicio es mayor que la fecha final, es un error.
    else if (fechaInicio > fechaFinal) {
      rangoFechasValido = false;
      if (reemplazar) {
        inputFechaInicio.value = currentDate.toISOString().split("T")[0];
        inputFechaFinal.value = currentDate.toISOString().split("T")[0];
      }
      crearSpan(
        inputFechaFinal,
        inputFechaFinal.nextElementSibling,
        "La fecha de inicio no puede ser mayor a la fecha final.",
        "red",
        false
      );
    }
    // Si las fechas son correctas, elimina cualquier mensaje de error.
    else {
      crearSpan(
        inputFechaFinal,
        inputFechaFinal.nextElementSibling,
        "",
        "",
        "",
        true
      );
    }
  }

  // --- Habilitar o deshabilitar el botón según la validación ---
  if (boton) {
    boton.disabled = !(fechaInicioValida && rangoFechasValido); // Se habilita solo si ambas validaciones son correctas.
  }
}
