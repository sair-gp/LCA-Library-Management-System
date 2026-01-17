<?php
include "app/model/checks.php";
include 'app/config/database.php';
$conexion = conexion();
$check = new check();
?>

<div class="tittle-div-head">
  <h3><img style="height: 5vh;" src="public/img/prestamos.gif" alt="prestamos">Registro de Usuarios</h3>
</div>

<div class="container">
  
  <!-- Columna izquierda: Formulario de registro -->
  <div class="left-column">
    <form id="formulario" class="col-10" action="app/controller/usuarios/c_registro_usuarios.php" method="POST">
      <h2>Registro de usuarios</h2>
      <hr>
      
      <div class="input-group mb-3">
        <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
        <input type="text" id="validarCedulaBD" class="cedulaForm form-control" placeholder="Cedula de identidad" name="cedula" required>
      </div>
      
      <div class="input-group mb-3">
        <span class="input-group-text"><i class="bi bi-file-person-fill"></i></span>
        <input type="text" class="nombreUsuarioForm form-control" placeholder="Nombre" name="nombre" maxlength="100" required>
        <input type="text" class="form-control" placeholder="Apellido" name="apellido" maxlength="100" required>
      </div>
      
      <div class="input-group mb-3">
        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
        <?php // Obtener la fecha actual
$fechaActual = new DateTime();

// Restar 18 años a la fecha actual
$fechaActual->modify('-18 years');

// Formatear la fecha en el formato Y-m-d (requerido por <input type="date">)
$fechaHace18Anos = $fechaActual->format('Y-m-d'); ?>
        <input type="date" class="form-control" id="fecha_nac" name="fecha_nac" style="width: 70%;" value="<?= $fechaHace18Anos ?>" required>
      
      </div>
      
      <div class="input-group mb-3">
        <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
        <input type="text" class="form-control" placeholder="Direccion" name="direccion" required>
      </div>
      
      <div class="input-group mb-3">
        <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
        <select name="dominio" id="dominio">
          <option>--Seleccione--</option>
          <option value="0424">0424</option>
          <option value="0414">0414</option>
          <option value="0412">0412</option>
          <option value="0426">0426</option>
          <option value="0416">0416</option>
        </select>
        <input type="text" class="form-control" placeholder="Telefono" name="telefono" maxlength="7" pattern="[0-9]{7}" required>
      </div>
      
      <div class="input-group mb-3">
        <span class="input-group-text">@</span>
        <input type="email" class="form-control" id="userEmail" placeholder="Correo electronico" name="correo" required>
      </div>
      
      <select class="form-select" name="sexo" required>
        <option selected>Genero</option>
        <option value="0">F</option>
        <option value="1">M</option>
      </select>
      
      <select class="form-select" name="rol" id="selectCargo" onchange="generarChecks()" required>
        <option value="0" selected>Nivel de usuario</option>
        <?php
        $query = "SELECT * from rol WHERE nombre != 'Admin';";
        $resultado = mysqli_query($conexion, $query);
        if ($resultado) {
          while ($fila = $resultado->fetch_assoc()) {
            echo '<option value="' . $fila['id_rol'] . '">' . $fila['nombre'] . '</option>';
          }
        }
        ?>
      </select>
      
      <div id="idSpanVisibilidad" class="input-group mb-3">
        <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
        <input id="passInput" type="password" class="form-control" placeholder="Contraseña" name="clave" required>
        <span class="toggle-btn" onclick="visibilidadPassword()">
          <i id="visibilidadIcon" class="bi bi-eye"></i>
        </span>
      </div>
    
  </div>
  
  <!-- Columna derecha: Permisos -->
  <div class="right-column" id="checksPermisos">
    <h2>Permisos</h2>
    <hr>
    <?php $check->generarCheckboxesPermisos($conexion); ?>
    <br>
    <center>
      <button type="submit" id="botonEF" class="btn btn-success" style="width: 400px;">Registrar Usuario</button>
    </center>
    </form>
  </div>
</div>

<style>
  .container {
    margin-top: 3%;
    width: 80%;
    margin-left: 10%;
    display: flex;
    justify-content: space-between;
  }
  .left-column, .right-column {
    flex: 1;
    padding: 10px;
  }
  .toggle-btn {
    position: absolute;
    right: 5%;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
  }

  /* Contenedor en cuadrícula */
.checks-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); /* Ajusta el número de columnas */
    gap: 15px;
    padding: 20px;
    background: #f4f4f4;
    border-radius: 10px;
}

/* Tarjetas para cada opción */
.form-check {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    position: relative;
}

.form-check:hover {
    transform: translateY(-2px);
    box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15);
}

/* Estilo del switch */
.form-check-input {
    width: 42px;
    height: 24px;
    appearance: none;
    background: #d1d1d1;
    border-radius: 50px;
    position: relative;
    cursor: pointer;
    transition: background 0.3s ease;
    outline: none;
}

.form-check-input::before {
    content: "";
    position: absolute;
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    top: 2px;
    left: 2px;
    transition: transform 0.3s ease;
}

/* Cuando está activado */
.form-check-input:checked {
    background: #6ecf77;
}

.form-check-input:checked::before {
    transform: translateX(18px);
}

/* Texto del label */
.form-check-label {
    font-size: 15px;
    color: #333;
    font-weight: 600;
    text-transform: capitalize;
}

</style>





<script>


document.getElementById("fecha_nac").addEventListener("input", function () {
    validarEdad(this);
});

function validarEdad(inputFecha) {
    const fechaNacimiento = inputFecha.value;

    // Verificar si se ingresó una fecha
    if (!fechaNacimiento) {
        crearSpan(inputFecha, inputFecha.nextElementSibling, "", "", true); // Limpiar mensaje si el campo está vacío
        return;
    }

    // Convertir la fecha de nacimiento a un objeto Date
    const fechaNac = new Date(fechaNacimiento);
    const hoy = new Date(); // Fecha actual

    // Calcular la edad
    let edad = hoy.getFullYear() - fechaNac.getFullYear();
    const mes = hoy.getMonth() - fechaNac.getMonth();

    // Ajustar la edad si aún no ha pasado el mes de cumpleaños
    if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
        edad--;
    }

    // Verificar si la edad está entre 18 y 90 años
    if (edad >= 18 && edad <= 90) {
        crearSpan(inputFecha, inputFecha.nextElementSibling, "Edad válida.", "green");
    } else if (edad < 18) {
        crearSpan(inputFecha, inputFecha.nextElementSibling, "Debes ser mayor de 18 años.", "red");
    } else {
        crearSpan(inputFecha, inputFecha.nextElementSibling, "La edad no puede ser mayor a 90 años.", "red");
    }
}



var botonEF = document.getElementById("botonEF");
var passValida = false;
var emailValido = false;
var cedulaValida = false;

function validarCampos() {
    botonEF.disabled = !(passValida && emailValido && cedulaValida);
}

  const passInput = document.getElementById("passInput");
  const iconoVisibilidad = document.getElementById("visibilidadIcon")
  const spanVisibilidad = document.getElementById("idSpanVisibilidad");
  const permisosBibliotecario = ["home", "libros", "prestamos", "visitantes", "multas"];
  const permisosCoordinador = ["home", "libros", "prestamos", "categorias", "actividades", "asistencias", "visitantes", "editoriales", "autores", "reportes", "multas"];


  function visibilidadPassword() {
    if (passInput.type === 'text') {
      passInput.type = 'password';
      iconoVisibilidad.classList = "";
      iconoVisibilidad.classList = "bi bi-eye-slash-fill";

    } else {
      passInput.type = 'text';
      iconoVisibilidad.classList = "";
      iconoVisibilidad.classList = "bi bi-eye";


    }
  }

  passInput.addEventListener("input", () => {

    if (expresiones.password.test(passInput.value)) {
      passValida = true;
      validarCampos();
      crearSpan(spanVisibilidad, spanVisibilidad.nextElementSibling, "", "", false);
    } else {
      passValida = false;
      validarCampos();
      crearSpan(spanVisibilidad, spanVisibilidad.nextElementSibling, "La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una minúscula, un número y un carácter especial.", "red", false);
    }

  })


  

  document.getElementById("userEmail").addEventListener("input", function (event) {
    botonEF.disabled = true;;
    const emailInput = this;
    let email = emailInput.value;
    const existingSpan = emailInput.nextElementSibling; // Posible <span> existente

    // Lista de dominios populares permitidos
    const allowedDomains = ["gmail", "yahoo", "outlook", "hotmail", "aol", "icloud", "protonmail", "yandex", "zoho", "mail"];
    const allowedExtensions = ["com", "net", "org", "edu", "gov", "mil", "int"];

    // Expresión regular para permitir solo caracteres válidos
    const validEmailPattern = /^[a-zA-Z0-9._%+-@]*$/;

    // Si el usuario introduce un carácter inválido, eliminarlo
    if (!validEmailPattern.test(email)) {
        emailInput.value = email.slice(0, -1);
        return;
    }

    // Dividir el correo en partes
    let parts = email.split("@");

    // Si hay más de un @, eliminar el último carácter
    if (parts.length > 2) {
        emailInput.value = email.slice(0, -1);
        crearSpan(emailInput, existingSpan, "Formato incorrecto. Solo se permite un '@'.", "red");
        return;
    }

    let localPart = parts[0]; // Nombre de usuario antes del @
    let domainAndExtension = parts[1] || ""; // Nombre de dominio + extensión

    // Si el nombre de usuario supera 64 caracteres, eliminar el último
    if (localPart.length > 64) {
       // emailInput.value = email.slice(0, -1);
        crearSpan(emailInput, existingSpan, "Has alcanzado el límite de 64 caracteres para el nombre del correo.", "red");
        return;
    }

    if (domainAndExtension) {
        let domainParts = domainAndExtension.split(".");
        let domainName = domainParts[0] || ""; // Nombre del dominio antes del punto
        let domainExtension = domainParts[1] || ""; // Extensión después del punto

        // Si el nombre del dominio supera 255 caracteres, eliminar el último
        if (domainName.length > 255) {
           // emailInput.value = email.slice(0, -1);
            crearSpan(emailInput, existingSpan, "Has alcanzado el límite de 255 caracteres para el nombre de dominio.", "red");
            return;
        }

        // Si el dominio no está permitido, eliminar el último carácter
        if (!allowedDomains.includes(domainName)) {
            //emailInput.value = email.slice(0, -1);
            crearSpan(emailInput, existingSpan, "El dominio ingresado no está permitido. Usa un dominio popular como Gmail o Yahoo.", "red");
            return;
        }

        // Si la extensión no es válida, eliminar el último carácter
        if (domainExtension && !allowedExtensions.includes(domainExtension)) {
            //emailInput.value = email.slice(0, -1);
            crearSpan(emailInput, existingSpan, "La extensión del dominio no es válida. Usa .com, .net, .org, etc.", "red");
            return;
        }
    }

    // Si el email es válido, eliminar el mensaje de error
    if (/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9-]+\.(com|net|org|edu|gov|mil|int)$/.test(email)) {
        emailValido = true;
        validarCampos();
        crearSpan(emailInput, existingSpan, "", "", true);
    } else {
      emailValido = false;
        validarCampos();
    }
});

var cedulaVisitante = document.getElementById("validarCedulaBD"); 

cedulaVisitante.addEventListener("input", () => {
    fetch("public/js/ajax/validarCamposUnicos.php", {
    method: "POST",
    headers: {"Content-type" : "application/json"},
    body: JSON.stringify({validarCedulaVisitante: cedulaVisitante.value})
}).then(response => {
    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }
    return response.json(); // Procesar la respuesta JSON
}).then(data => {
    console.log(data.message);
    crearSpan(cedulaVisitante, cedulaVisitante.nextElementSibling, data.message, data.class, "");
    if (cedulaVisitante.value == ""){
    crearSpan(cedulaVisitante, cedulaVisitante.nextElementSibling, "", "", true);
    }

    var botonEF = document.getElementById("botonEF")

    data.class == "red" ? cedulaValida = false : cedulaValida = true;
    validarCampos();

}).catch(error => {
    console.error("Error al validar la cédula:", error); // Manejo de errores
    // Puedes mostrar un mensaje de error al usuario aquí
});;

});






  // Activar checks dependiendo del cargo seleccionado

  function generarChecks() {
    const selectElement = document.getElementById("selectCargo");
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const selectedCargoText = selectedOption.textContent; // Obtener el texto de la opción seleccionada

    //console.log(selectedCargoText)
    const checksContainer = document.getElementById("checksPermisos");


    var checkboxes = document.querySelectorAll('#checksPermisos input[type="checkbox"]');

    if (selectedCargoText == "Coordinador") {
      //desmarcar todos los permisos
      checkboxes.forEach(function(checkbox) {
        if (checkbox.checked) {
          checkbox.checked = false;
        }
        if (permisosCoordinador.includes(checkbox.id)) {
          checkbox.checked = true;
        }
      });
    }

    if (selectedCargoText == "Bibliotecario") {
      //desmarcar todos los permisos
      checkboxes.forEach(function(checkbox) {
        if (checkbox.checked) {
          checkbox.checked = false;
        }
        if (permisosBibliotecario.includes(checkbox.id)) {
          checkbox.checked = true;
        }
      });
    }

    /*fetch("../../public/js/ajax/generarChecks.php", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        cargo: selectedCargoText
      })
    }).then(response => {
      if (!response.ok) {
        throw new Error('La respuesta del servidor no fue exitosa');
      }
      return response.text(); // Obtener el texto de la respuesta
    }).then(data => {
      console.log('HTML recibido:', data);
      checksContainer.innerHTML = data;
    }).catch(error => {
      console.error('Error en la solicitud AJAX:', error);
    });

*/

  }
</script>




<!--script src="public/js/validarCedulaUnica.js"></script-->





<!--div>
<form class="col-4" action="app/controller/usuarios/c_registro_usuarios.php" method="POST">
<h2>Registro de usuarios</h2>
<hr>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon1"><i class="bi bi-person-vcard"></i></span>
  <input type="text" class="form-control" placeholder="Cedula de identidad" name="cedula">
</div>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon1"><i class="bi bi-file-person-fill"></i></i></span>
   <input type="text" class="form-control" placeholder="Nombre" name="nombre">
  <input type="text" class="form-control" placeholder="Apellido" name="apellido">
</div>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon1"><i class="bi bi-calendar3"></i></span>
  <input type="date" class="form-control" name="fecha_nac">
</div>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon1"><i class="bi bi-geo-alt-fill"></i></span>
  <input type="text" class="form-control" placeholder="Direccion" name="direccion">
</div>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon1"><i class="bi bi-telephone-fill"></i></span>
  <select name="dominio" id="dominio">
  <option>--Seleccione--</option>
  <option value="0424">0424</option>
  <option value="0414">0414</option>
  <option value="0412">0412</option>
  <option value="0426">0426</option>
  <option value="0416">0416</option>
</select>
  <input type="text" class="form-control" placeholder="Telefono" name="telefono">
</div>
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon1">@</span>
  <input type="text" class="form-control" placeholder="Correo electronico" name="correo">
</div>
<select class="form-select" name="sexo">
  <option selected>Genero</option>
  <option value="0">F</option>
  <option value="1">M</option>
</select> 
<select class="form-select" name="rol">
  <option selected>Nivel de usuario</option>
  <option value="1">Administrador</option>
  <option value="2">Usuario</option>
</select> 
<div class="input-group mb-3">
  <span class="input-group-text" id="basic-addon1"><i class="bi bi-person-vcard"></i></span>
  <input type="password" class="form-control" placeholder="Contraseña" name="clave">
</div>
  <button>Registrar</button>




<div class="form-check form-switch">
  <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" checked>
  <label class="form-check-label" for="flexSwitchCheckChecked">Checked switch checkbox input</label>
</div>






</form>
</div-->





































<!--form class="col-4" action="app/controller/c_registro_usuarios.php" method="POST">
    <label>Cedula</label>
    <input type="text" class="form-control" name="cedula">
    <label>Nombre</label>
    <input type="text" class="form-control" name="nombre">
    <label for="exampleInputEmail1" class="form-label">Apellido</label>
    <input type="text" class="form-control" name="apellido">
    <label for="exampleInputEmail1" class="form-label">Fecha de Nacimiento</label>
    <input type="date" class="form-control" name="fecha_nac">
    <label for="exampleInputEmail1" class="form-label">Direccion</label>
    <input type="text" class="form-control" name="direccion">
    <label for="exampleInputEmail1" class="form-label">Telefono</label>
    <input type="text" class="form-control" name="telefono">
    <label for="exampleInputEmail1" class="form-label">Correo</label>
    <input type="email" class="form-control" name="correo">
    <label for="exampleInputEmail1" class="form-label">Sexo</label>
    <input type="text" class="form-control" name="sexo">
    <label for="exampleInputEmail1" class="form-label">Rol</label>
    <input type="text" class="form-control" name="rol">
    <label for="exampleInputEmail1" class="form-label">Clave</label>
    <input type="password" class="form-control" name="clave">
  <button type="submit" class="btn btn-primary" name="btn-reg-usuario">Registrar</button>
</form-->