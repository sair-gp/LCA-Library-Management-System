<style>
  /* General Modal Styles */
  .modal-content {
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
  }

  /* Section Headings */
  .update-section h6 {
    font-size: 1.1rem;
    font-weight: bold;
    margin-bottom: 10px;
  }

  /* Profile Picture Section */
  .profile-picture-container {
    text-align: center;
  }

  .profile-picture-container img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ddd;
  }

  .profile-picture-container input {
    display: block;
    margin: 10px auto;
    max-width: 300px;
  }

  /* Form Styling */
  .form-control {
    border-radius: 5px;
  }

  .btn-primary {
    width: 100%;
    background-color: #007bff;
    border: none;
    padding: 10px;
    font-size: 1rem;
  }

  .btn-primary:hover {
    background-color: #0056b3;
  }
</style>

<!-- User Details Modal -->
<!-- Modal -->
<div class="modal fade" id="userConfigModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailsModalLabel">Actualizar datos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">Actualizar Foto de Perfil</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">Cambiar Contraseña</button>
          </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <!-- Profile Picture Update -->
            <div class="update-section">

              <div class="profile-picture-container">
                <img id="currentProfilePic" src='<?php echo $_SESSION["fotoPerfil"]; ?>' alt="Foto de perfil actual">
                <input type="file" id="profilePicInput" accept="image/*" class="form-control mt-2">
              </div>

              <!-- Área de recorte (inicialmente oculta) -->
              <div id="cropperContainer" style="display: none; text-align: center;">
                <img id="imageToCrop" style="max-width: 100%;">
              </div>

              <button type="button" id="cropButton" class="btn btn-primary" style="display: none;">Recortar y Guardar</button>

              <button type="button" id="botonActualizaLaFoto" class="btn btn-success form-control">
                Actualizar la foto <i class="bi bi-arrow-up"></i>
              </button>
            </div>
          </div>
          <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
            <!-- Password Update -->
            <div class="update-section">

              <form id="passwordUpdateForm" action="app/controller/usuarios/c_actualizarContrasena.php">
                <div class="mb-3">
                  <label for="currentPassword" class="form-label">Contraseña actual</label>
                  <input type="password" id="currentPassword" class="form-control" oninput="validarContrasenas({inputCurrentPassword: this, inputPassword: document.getElementById('newPassword'), inputConfirmPassword: document.getElementById('confirmNewPassword'), boton: document.getElementById('updatePasswordButton')})" required>
                </div>
                <div class="mb-3">
                  <label for="newPassword" class="form-label">Nueva contraseña</label>
                  <input type="password" id="newPassword" class="form-control" oninput="validarContrasenas({inputCurrentPassword: document.getElementById('currentPassword'), inputPassword: this, inputConfirmPassword: document.getElementById('confirmNewPassword'), boton: document.getElementById('updatePasswordButton')})" required>
                </div>
                <div class="mb-3">
                  <label for="confirmNewPassword" class="form-label">Confirmar nueva contraseña</label>
                  <input type="password" id="confirmNewPassword" class="form-control" oninput="validarContrasenas({inputCurrentPassword: document.getElementById('currentPassword') ,inputPassword: document.getElementById('newPassword'), inputConfirmPassword: this, boton: document.getElementById('updatePasswordButton')})" required>
                </div>
                <button type="submit" class="btn btn-primary" id="updatePasswordButton">Actualizar contraseña</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<link href="node_modules/cropperjs/dist/cropper.min.css" rel="stylesheet">
<script src="node_modules/cropperjs/dist/cropper.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const profilePicInput = document.getElementById("profilePicInput");
    const imageToCrop = document.getElementById("imageToCrop");
    const cropperContainer = document.getElementById("cropperContainer");
    const cropButton = document.getElementById("cropButton");
    const currentProfilePic = document.getElementById("currentProfilePic");
    const uploadButton = document.getElementById("botonActualizaLaFoto");
    let cropper, croppedImage;
    let sidebarPic = document.getElementById("fotoPerfilSidebar");

    profilePicInput.addEventListener("change", function(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          imageToCrop.src = e.target.result;
          cropperContainer.style.display = "block";
          cropButton.style.display = "block";

          if (cropper) {
            cropper.destroy();
          }

          cropper = new Cropper(imageToCrop, {
            aspectRatio: 1, // Mantener imagen cuadrada
            viewMode: 2,
            movable: true,
            zoomable: true,
            rotatable: false,
            scalable: false
          });
        };
        reader.readAsDataURL(file);
      }
    });

    cropButton.addEventListener("click", function() {
      if (cropper) {
        const canvas = cropper.getCroppedCanvas({
          width: 200,
          height: 200
        });

        croppedImage = canvas.toDataURL(); // Guardar la imagen en base64 temporalmente
        currentProfilePic.src = croppedImage; // Actualizar la imagen en la vista previa
        //alert("Recorte listo. Presiona 'Actualizar la foto' para subirla.");
        cropperContainer.style.display = "none";
        cropButton.style.display = "none";
      }
    });

    uploadButton.addEventListener("click", function() {
      if (croppedImage) {
        fetch("app/controller/usuarios/c_actualizarFoto.php", {
            method: "POST",
            body: JSON.stringify({
              image: croppedImage
            }),
            headers: {
              "Content-Type": "application/json"
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              currentProfilePic.src = data.filePath; // Reemplazar imagen con la nueva URL del servidor
              sidebarPic.src = data.filePath; //Actualiza en el sidebar
              Swal.fire({
                title: "¡Foto actualizada correctamente!",
                text: ".",
                icon: "success",
              });
              cropperContainer.style.display = "none";
              cropButton.style.display = "none";
            } else {
              alert("Hubo un problema al subir la imagen.");
            }
          })
          .catch(error => console.error("Error al subir la imagen:", error));
      } else {
        alert("Primero recorta la imagen antes de subirla.");
      }
    });
  });
</script>



<script>
  function validarContrasenas({
    inputCurrentPassword = null,
    inputPassword = null,
    inputConfirmPassword = null,
    boton = null
  }) {
    let contrasenaValida = true; // Indica si las contraseñas coinciden.
    const passActual = <?php echo $_SESSION["contrasena"]; ?>;



    if (passActual && inputCurrentPassword) {
      cPasword = inputCurrentPassword.value.trim();
      if (cPasword == "") {
        crearSpan(inputCurrentPassword, inputCurrentPassword.nextElementSibling, "Campo obligatorio.", "", true);
        contrasenaValida = false;
      } else if (cPasword != passActual) {
        crearSpan(inputCurrentPassword, inputCurrentPassword.nextElementSibling, "La contraseña ingresada no coincide con la actual.", "red");
        contrasenaValida = false

      } else {
        crearSpan(inputCurrentPassword, inputCurrentPassword.nextElementSibling, "", "", true);
        contrasenaValida = true;
      }

    }



    if (inputPassword && inputConfirmPassword) {
      const password = inputPassword.value.trim();
      const confirmPassword = inputConfirmPassword.value.trim();

      if (password === "" || confirmPassword === "") {
        crearSpan(inputConfirmPassword, inputConfirmPassword.nextElementSibling, "Ambos campos son obligatorios.", "red", false);
        contrasenaValida = false;
      } else if (password !== confirmPassword) {
        crearSpan(inputConfirmPassword, inputConfirmPassword.nextElementSibling, "Las contraseñas no coinciden.", "red", false);
        contrasenaValida = false;
      } else {
        crearSpan(inputConfirmPassword, inputConfirmPassword.nextElementSibling, "", "", "", true);
      }

      if (inputCurrentPassword) {
        if (passActual == password || passActual == confirmPassword) {
          crearSpan(inputConfirmPassword, inputConfirmPassword.nextElementSibling, "La nueva contraseña no puede ser igual a la anterior", "red", false);
          contrasenaValida = false;
        }
      }


    }



    // Habilitar o deshabilitar el botón según la validación
    if (boton) {
      boton.disabled = !contrasenaValida;
    }
  }
</script>






<!--script src="public/js/actualizarDatosUsuario.js">
  /*   document.addEventListener("DOMContentLoaded", () => {
  // Profile Picture Update
const updateButton = document.getElementById("botonActualizaLaFoto");
const profilePicInput = document.getElementById("profilePicInput");
const currentProfilePic = document.getElementById("currentProfilePic");

// Manejar el cambio en el input de archivo
profilePicInput.addEventListener("change", (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            currentProfilePic.src = e.target.result; // Actualizar vista previa
        };
        reader.readAsDataURL(file);
    }
});

// Función para actualizar la foto de perfil
async function actualizarFotoPerfil() {
    const file = profilePicInput.files[0]; // Obtener el archivo de imagen seleccionado
    if (!file) {
      Swal.fire({
            icon: 'error',
            title: 'No se ha seleccionado ningún archivo',
            text: 'Por favor, selecciona una imagen antes de actualizar.',
        });
        return;
    }

    const formData = new FormData();
    formData.append("photo", file); // Agregar la imagen al FormData con la clave 'photo'

    try {
        const response = await fetch("public/js/ajax/actualizarFotoPerfil.php", {
            method: "POST",
            body: formData,
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                // La imagen se envió correctamente, puedes hacer algo con la respuesta
              Swal.fire({
                icon: "success",
                title: "Operación exitosa",
                text: "La foto ha sido actualizada exitosamente.",
              });
                console.log("Nueva foto de perfil:", data.fotoPerfil);
               
              // Actualizar la foto de perfil en el sidebar
              const fotoPerfilSidebar = document.getElementById("fotoPerfilSidebar");
                if (fotoPerfilSidebar && data.fotoPerfil) {
                    fotoPerfilSidebar.src = data.fotoPerfil; // Cambiar el src de la imagen en el sidebar
                }


            } else {
                // SweetAlert de error del servidor
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Hubo un error al procesar la imagen.',
                });
            }
        } else {
             // SweetAlert en caso de error en la respuesta
             Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `La imagen no pudo ser actualizada. Intente nuevamente o con otra imagen.`//${response.status},
            });
        }
    } catch (error) {
        // SweetAlert de error de red
        Swal.fire({
            icon: 'error',
            title: 'Error de red',
            text: error.message || 'Ocurrió un problema al intentar enviar la imagen.',
        });
    }
}

// Manejar el clic en el botón para enviar la imagen
updateButton.addEventListener("click", async () => {
    const file = profilePicInput.files[0];
    if (!file) {
        Swal.fire({
          icon: "warning",
          title: "Selecciona una imagen",
          text: "Por favor, seleccione una imagen antes de continuar",
        });
        return;
    }
    await actualizarFotoPerfil();
});






  





 // Selección del formulario y elementos relacionados
const formContrasena = document.getElementById("passwordUpdateForm");
const nuevaContra = document.getElementById("newPassword");
const confirmarNuevaContra = document.getElementById("confirmNewPassword");
const contrasenaActual = document.getElementById("currentPassword");

let checkContrasenas = false; // Estado de coincidencia entre nuevas contraseñas

// Función para comparar las nuevas contraseñas ingresadas
function compararContrasenas() {
    if (nuevaContra.value && confirmarNuevaContra.value && nuevaContra.value === confirmarNuevaContra.value) {
        crearSpan(confirmarNuevaContra, confirmarNuevaContra.nextElementSibling, "Las contraseñas son iguales.", "green");
        checkContrasenas = true; // Actualizamos el estado
    } else {
        crearSpan(confirmarNuevaContra, confirmarNuevaContra.nextElementSibling, "Las contraseñas deben ser iguales.", "red");
        checkContrasenas = false;
    }
}

// Asignar eventos para detectar cambios en los inputs de contraseña
nuevaContra.addEventListener("input", compararContrasenas);
confirmarNuevaContra.addEventListener("input", compararContrasenas);


// Función para verificar y actualizar la contraseña
async function compararContrasenaActual(event) {
    event.preventDefault(); // Prevenir el envío por defecto

    try {
        // Enviar las contraseñas al servidor
        const response = await fetch("public/js/ajax/actualizarContra.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `contrasenaActual=${encodeURIComponent(contrasenaActual.value)}&` +
                  `nuevaContrasena=${encodeURIComponent(nuevaContra.value)}&` +
                  `confirmarNuevaContrasena=${encodeURIComponent(confirmarNuevaContra.value)}`
        });

        const data = await response.json(); // Procesar la respuesta JSON

        if (data.respuesta) {
            // Si la actualización fue exitosa
            Swal.fire({
                icon: "success",
                title: "Éxito",
                text: data.mensaje,
            });
            contrasenaActual.value = "";
            nuevaContra.value = "";
            confirmarNuevaContra.value = "";
        } else {
            // Mostrar mensaje de error del servidor
            Swal.fire({
                icon: "error",
                title: "Error",
                text: data.mensaje,
            });
        }
    } catch (error) {
        console.error("Error inesperado:", error);
        Swal.fire({
            icon: "error",
            title: "Error inesperado",
            text: "Hubo un problema, por favor intente nuevamente.",
        });
    }
}

// Asociar el evento submit del formulario
formContrasena.addEventListener("submit", compararContrasenaActual);




// Asociar el evento submit del formulario a la función de comparación
formContrasena.addEventListener("submit", compararContrasenaActual);


  
});
*/
</script-->