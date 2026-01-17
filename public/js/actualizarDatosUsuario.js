document.addEventListener("DOMContentLoaded", () => {
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
  