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
      <div id="respuesta"> </div>      
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




<script>
  // Expresiones regulares para validaciones
const expresiones = {
    password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/,
    email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9-]+\.(com|net|org|edu|gov|mil|int)$/,
    nombre: /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s']+$/ // Permite letras, acentos, espacios y apóstrofes
};

// Estado de validación de campos
const estadoValidacion = {
    cedula: false,
    nombre: false,
    apellido: false,
    fechaNacimiento: true,
    direccion: false,
    telefono: false,
    correo: false,
    genero: false,
    rol: false,
    clave: false
};

// Permisos por rol
const permisosPorRol = {
    "Bibliotecario": ["home", "libros", "prestamos", "visitantes", "multas"],
    "Coordinador": ["home", "libros", "prestamos", "categorias", "actividades", "asistencias", "visitantes", "editoriales", "autores", "reportes", "multas"]
};

// DOM Elements
const elements = {
    formulario: document.getElementById("formulario"),
    botonRegistro: document.getElementById("botonEF"),
    passInput: document.getElementById("passInput"),
    visibilidadIcon: document.getElementById("visibilidadIcon"),
    spanVisibilidad: document.getElementById("idSpanVisibilidad"),
    emailInput: document.getElementById("userEmail"),
    fechaNacimiento: document.getElementById("fecha_nac"),
    cedulaInput: document.getElementById("validarCedulaBD"),
    selectCargo: document.getElementById("selectCargo"),
    checksContainer: document.getElementById("checksPermisos"),
    dominioSelect: document.getElementById("dominio"),
    telefonoInput: document.querySelector('input[name="telefono"]'),
    nombreInput: document.querySelector('input[name="nombre"]'),
    apellidoInput: document.querySelector('input[name="apellido"]'),
    direccionInput: document.querySelector('input[name="direccion"]'),
    generoSelect: document.querySelector('select[name="sexo"]')
};

// Inicialización de eventos
function inicializarEventos() {
    // Eventos de validación
    elements.passInput.addEventListener("input", validarPassword);
    elements.emailInput.addEventListener("input", validarEmail);
    elements.fechaNacimiento.addEventListener("input", validarEdad);
    elements.cedulaInput.addEventListener("input", validarCedula);
    elements.selectCargo.addEventListener("change", generarChecks);
    
    // Eventos para selects
    elements.selectCargo.addEventListener("change", () => {
        estadoValidacion.rol = elements.selectCargo.value !== "0";
        validarFormulario();
    });
    
    elements.generoSelect.addEventListener("change", () => {
        estadoValidacion.genero = elements.generoSelect.value !== "Genero";
        validarFormulario();
    });
    
    elements.dominioSelect.addEventListener("change", validarTelefono);
    elements.telefonoInput.addEventListener("input", validarTelefono);
    elements.nombreInput.addEventListener("input", validarNombreApellido);
    elements.nombreInput.addEventListener("keydown", prevenirCaracteresInvalidos);
    elements.apellidoInput.addEventListener("input", validarNombreApellido);
    elements.apellidoInput.addEventListener("keydown", prevenirCaracteresInvalidos);
    elements.direccionInput.addEventListener("input", validarDireccion);
    
    // Evento para visibilidad de contraseña
    elements.visibilidadIcon.parentElement.addEventListener("click", visibilidadPassword);
    
    // Inicializar estado del botón
    validarFormulario();
}

// Función principal de validación del formulario
function validarFormulario() {
    const todosValidos = Object.values(estadoValidacion).every(v => v);
    elements.botonRegistro.disabled = !todosValidos;
}

// Funciones de validación específicas
function validarPassword() {
    const esValida = expresiones.password.test(elements.passInput.value);
    estadoValidacion.clave = esValida;
    
    if (esValida) {
        crearSpan(elements.spanVisibilidad, elements.spanVisibilidad.nextElementSibling, "", "", false);
    } else {
        crearSpan(elements.spanVisibilidad, elements.spanVisibilidad.nextElementSibling, 
            "La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una minúscula, un número y un carácter especial.", 
            "red", false);
    }
    
    validarFormulario();
}

function validarEmail(event) {
    const emailInput = elements.emailInput;
    let email = emailInput.value;
    const existingSpan = emailInput.nextElementSibling;

    // Validación básica de formato
    const esValido = expresiones.email.test(email);
    estadoValidacion.correo = esValido;
    
    if (esValido) {
        // Verificar si el correo existe en la base de datos
        $.ajax({
            url: "public/js/ajax/verificarCorreo.php",
            type: "GET",
            data: { userEmail: email },
            dataType: 'json',
            success: function(response) {
                if (response && response.existe === true) {
                    crearSpan(emailInput, existingSpan, "El correo electrónico ya está registrado.", "red");
                    estadoValidacion.correo = false;
                } else {
                    crearSpan(emailInput, existingSpan, "", "", true);
                    estadoValidacion.correo = true;
                }
                validarFormulario();
            },
            error: function(xhr, status, error) {
                console.error("Error AJAX:", status, error, xhr.responseText);
                crearSpan(emailInput, existingSpan, "Error al verificar el correo electrónico.", "red");
                estadoValidacion.correo = false;
                validarFormulario();
            }
        });
    } else {
        crearSpan(emailInput, existingSpan, "Formato de correo electrónico inválido.", "red");
        estadoValidacion.correo = false;
        validarFormulario();
    }
}

function validarEdad() {
    const fechaNacimiento = elements.fechaNacimiento.value;
    
    if (!fechaNacimiento) {
        crearSpan(elements.fechaNacimiento, elements.fechaNacimiento.nextElementSibling, "", "", true);
        estadoValidacion.fechaNacimiento = false;
        validarFormulario();
        return;
    }

    const fechaNac = new Date(fechaNacimiento);
    const hoy = new Date();
    let edad = hoy.getFullYear() - fechaNac.getFullYear();
    const mes = hoy.getMonth() - fechaNac.getMonth();

    if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
        edad--;
    }

    if (edad >= 18 && edad <= 90) {
        crearSpan(elements.fechaNacimiento, elements.fechaNacimiento.nextElementSibling, "Edad válida.", "green");
        estadoValidacion.fechaNacimiento = true;
    } else if (edad < 18) {
        crearSpan(elements.fechaNacimiento, elements.fechaNacimiento.nextElementSibling, "Debes ser mayor de 18 años.", "red");
        estadoValidacion.fechaNacimiento = false;
    } else {
        crearSpan(elements.fechaNacimiento, elements.fechaNacimiento.nextElementSibling, "La edad no puede ser mayor a 90 años.", "red");
        estadoValidacion.fechaNacimiento = false;
    }
    
    validarFormulario();
}

function validarCedula() {
    const cedula = elements.cedulaInput.value;
    
    if (!cedula) {
        crearSpan(elements.cedulaInput, elements.cedulaInput.nextElementSibling, "", "", true);
        estadoValidacion.cedula = false;
        validarFormulario();
        return;
    }

    fetch("public/js/ajax/validarCamposUnicos.php", {
        method: "POST",
        headers: {"Content-type": "application/json"},
        body: JSON.stringify({validarCedulaVisitante: cedula})
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return response.json();
    })
    .then(data => {
        crearSpan(elements.cedulaInput, elements.cedulaInput.nextElementSibling, data.message, data.class, "");
        estadoValidacion.cedula = data.class !== "red";
        validarFormulario();
    })
    .catch(error => {
        console.error("Error al validar la cédula:", error);
        crearSpan(elements.cedulaInput, elements.cedulaInput.nextElementSibling, "Error al validar la cédula.", "red");
        estadoValidacion.cedula = false;
        validarFormulario();
    });
}

function validarTelefono() {
    const telefonoCompleto = elements.dominioSelect.value + elements.telefonoInput.value;
    const esValido = elements.dominioSelect.value !== "--Seleccione--" && 
                     elements.telefonoInput.value.length === 7 && 
                     /^[0-9]{7}$/.test(elements.telefonoInput.value);
    
    estadoValidacion.telefono = esValido;
    validarFormulario();
}

function validarNombreApellido(event) {
    const input = event.target;
    const valorOriginal = input.value;
    
    // Filtrar caracteres no permitidos
    const valorFiltrado = valorOriginal.split('').filter(char => {
        return expresiones.nombre.test(char);
    }).join('');
    
    // Si hay diferencia, actualizar el valor
    if (valorOriginal !== valorFiltrado) {
        input.value = valorFiltrado;
        
        // Mover el cursor al final
        setTimeout(() => {
            input.selectionStart = input.selectionEnd = valorFiltrado.length;
        }, 0);
    }
    
    // Actualizar estado de validación
    estadoValidacion.nombre = elements.nombreInput.value.trim().length > 0;
    estadoValidacion.apellido = elements.apellidoInput.value.trim().length > 0;
    validarFormulario();
}

function prevenirCaracteresInvalidos(event) {
    // Permitir teclas de control (backspace, delete, flechas, etc.)
    if (event.ctrlKey || event.altKey || event.metaKey || 
        [8, 9, 13, 16, 17, 18, 20, 27, 37, 38, 39, 40, 46].includes(event.keyCode)) {
        return;
    }
    
    // Verificar si el carácter es permitido
    if (!expresiones.nombre.test(event.key)) {
        event.preventDefault();
    }
}

function validarDireccion() {
    estadoValidacion.direccion = elements.direccionInput.value.trim().length > 0;
    validarFormulario();
}

// Funciones auxiliares
function visibilidadPassword() {
    if (elements.passInput.type === 'text') {
        elements.passInput.type = 'password';
        elements.visibilidadIcon.classList = "bi bi-eye-slash-fill";
    } else {
        elements.passInput.type = 'text';
        elements.visibilidadIcon.classList = "bi bi-eye";
    }
}

function crearSpan(elemento, siguienteElemento, mensaje, color, limpiar) {
    if (limpiar) {
        if (siguienteElemento && siguienteElemento.tagName === 'SPAN') {
            siguienteElemento.remove();
        }
        return;
    }

    let span = siguienteElemento && siguienteElemento.tagName === 'SPAN' ? 
               siguienteElemento : 
               document.createElement('span');
    
    span.textContent = mensaje;
    span.style.color = color || 'inherit';
    
    if (!siguienteElemento || siguienteElemento.tagName !== 'SPAN') {
        elemento.parentNode.insertBefore(span, elemento.nextSibling);
    }
}

function generarChecks() {
    const selectedOption = elements.selectCargo.options[elements.selectCargo.selectedIndex];
    const selectedCargoText = selectedOption.textContent;
    const checkboxes = document.querySelectorAll('#checksPermisos input[type="checkbox"]');

    if (permisosPorRol[selectedCargoText]) {
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = false;
            if (permisosPorRol[selectedCargoText].includes(checkbox.id)) {
                checkbox.checked = true;
            }
        });
    }
    
    // Validar que se haya seleccionado un rol válido
    estadoValidacion.rol = elements.selectCargo.value !== "0";
    validarFormulario();
}

// Inicializar la aplicación cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", inicializarEventos);
</script>
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


