<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="agregarAutor" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Encabezado limpio -->
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fs-6 text-uppercase letter-spacing-1">Nuevo Autor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      
      <form action="app/controller/autores/c_agregar_Autor.php" method="POST" enctype="multipart/form-data">
        <div class="modal-body pt-0">
          
          <!-- Foto de perfil -->
          <div class="avatar-upload mb-4">
            <div class="avatar-preview">
              <img id="fotoAutor" src="public/img/autores/default.jpg" alt="Previsualización" class="rounded-circle">
            </div>
            <div class="avatar-edit">
              <input type="file" id="fotoAutorInput" name="fotoAutor" accept="image/*" class="d-none">
              <label for="fotoAutorInput" class="btn-avatar-edit">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
              </label>
            </div>
          </div>

          <!-- Campos del formulario -->
          <div class="form-grid">
            <!-- Nombre -->
            <div class="form-group">
              <label for="id_autorNombre">Nombre completo *</label>
              <input type="text" id="id_autorNombre" name="autorNombre" required class="form-control-sm">
              <div class="error-message">Solo se permiten letras y acentos (mínimo 2 caracteres)</div>
            </div>

            <!-- Fecha nacimiento -->
            <div class="form-group">
              <label for="fechaNacimiento">Fecha nacimiento (opcional)</label>
              <input type="date" id="fechaNacimiento" name="fechaNacimiento" class="form-control-sm">
              <div class="error-message">Fecha no válida.</div>
            </div>

            <!-- Biografía -->
            <div class="form-group full-width">
              <label for="biografia">Biografía (opcional)</label>
              <textarea id="biografia" name="biografia" rows="3" class="form-control-sm"></textarea>
              <div class="error-message">Máximo 500 caracteres</div>
              <div class="text-end small text-muted mt-1 char-counter"></div>
            </div>
          </div>
        </div>

        <!-- Botones -->
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-text" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary-sm">Guardar autor</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  /* Estilos minimalistas */
  .modal-content {
    border: none;
    border-radius: 8px;
    padding: 1.5rem;
  }
  
  .modal-title {
    font-weight: 500;
    color: #333;
    letter-spacing: 0.5px;
  }
  
  /* Avatar upload */
  .avatar-upload {
    position: relative;
    max-width: 120px;
    margin: 0 auto;
  }
  
  .avatar-preview {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 1px solid #eee;
    overflow: hidden;
  }
  
  .avatar-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .btn-avatar-edit {
    position: absolute;
    right: 5px;
    bottom: 5px;
    background: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    cursor: pointer;
    border: 1px solid #e0e0e0;
  }
  
  /* Formulario */
  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }
  
  .form-group {
    margin-bottom: 1rem;
  }
  
  .form-group.full-width {
    grid-column: span 2;
  }
  
  label {
    display: block;
    font-size: 0.8rem;
    color: #666;
    margin-bottom: 0.3rem;
  }
  
  .form-control-sm {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    font-size: 0.9rem;
  }
  
  textarea.form-control-sm {
    min-height: 80px;
    resize: vertical;
  }
  
  /* Botones */
  .btn-text {
    background: none;
    border: none;
    color: #666;
    padding: 0.5rem 1rem;
  }
  
  .btn-primary-sm {
    background: #2c3e50;
    color: white;
    border: none;
    padding: 0.5rem 1.5rem;
    border-radius: 4px;
    font-size: 0.9rem;
  }
  
  .btn-primary-sm:hover {
    background: #1a252f;
  }

  /* Añade esto a tu hoja de estilos */
input:invalid, textarea:invalid {
  border-color: #dc3545 !important;
}

input:valid, textarea:valid {
  border-color: #28a745 !important;
}

.error-message {
  color: #dc3545;
  font-size: 0.75rem;
  margin-top: 0.25rem;
  display: none;
}

input:invalid + .error-message, 
textarea:invalid + .error-message {
  display: block;
}

.is-invalid {
  border-color: #dc3545 !important;
}

.invalid-feedback {
  display: none;
  width: 100%;
  margin-top: 0.25rem;
  font-size: 0.875em;
  color: #dc3545;
}

.is-invalid ~ .invalid-feedback {
  display: block;
}
</style>

<script>
    //script para cambiar la foto del usuario

    const fotoAutor = document.getElementById("fotoAutor");
    const fotoAutorInput = document.getElementById("fotoAutorInput");

    fotoAutorInput.addEventListener("change", () => {
        const file = fotoAutorInput.files[0];
        //const firstFile = file[0];
        if (file){

        const imgURL = URL.createObjectURL(file); 
        fotoAutor.src = imgURL;
        //console.log(file.name)


        }
        
    });


</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
  // Elementos del formulario
  const form = document.querySelector('#agregarAutor form');
  const nombreInput = document.getElementById('id_autorNombre');
  const fechaInput = document.getElementById('fechaNacimiento');
  const bioInput = document.getElementById('biografia');
  const submitBtn = form.querySelector('button[type="submit"]');
  const fotoInput = document.getElementById('fotoAutorInput');
  
  // Elementos de feedback
  const nombreFeedback = document.createElement('div');
  nombreFeedback.className = 'invalid-feedback';
  nombreInput.parentNode.appendChild(nombreFeedback);

  const fechaFeedback = document.createElement('div');
  fechaFeedback.className = 'invalid-feedback';
  fechaInput.parentNode.appendChild(fechaFeedback);

  // Contador de caracteres para biografía
  const charCounter = document.createElement('div');
  charCounter.className = 'text-end small text-muted mt-1';
  bioInput.parentNode.insertBefore(charCounter, bioInput.nextSibling);

  // Event listeners
  nombreInput.addEventListener('input', async () => {
    await validateNombre();
    updateSubmitButton();
  });

  fechaInput.addEventListener('change', () => {
    validateFecha();
    updateSubmitButton();
  });

  bioInput.addEventListener('input', () => {
    validateBiografia();
    updateCharCounter();
  });

  fotoInput.addEventListener('change', updateSubmitButton);

  // Validación inicial
  updateSubmitButton();

  async function updateSubmitButton() {
    const isNombreValid = await validateNombre();
    const isFechaValid = validateFecha();
    
    submitBtn.disabled = !(isNombreValid && isFechaValid);
  }

  async function validateNombre() {
    const value = nombreInput.value.trim();
    const regex = /^[\p{L}\s']+$/u;
    
    // Reset estado
    nombreInput.setCustomValidity('');
    nombreInput.classList.remove('is-invalid');
    nombreFeedback.textContent = '';
    
    // Validación básica
    if (!value) {
      nombreInput.setCustomValidity('El nombre es requerido');
      nombreFeedback.textContent = 'El nombre es requerido';
      nombreInput.classList.add('is-invalid');
      return false;
    }
    
    // Validar caracteres
    if (!regex.test(value)) {
      nombreInput.value = nombreInput.value.slice(0, -1); // Eliminar último carácter inválido
      nombreInput.setCustomValidity('Solo se permiten letras y acentos');
      nombreFeedback.textContent = 'Solo se permiten letras y acentos';
      nombreInput.classList.add('is-invalid');
      return false;
    }
    
    // Validar existencia en servidor
    try {
      const response = await fetch('app/controller/autores/check_autor.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `nombre=${encodeURIComponent(value)}`
      });
      
      if (!response.ok) throw new Error('Error en la verificación');
      
      const data = await response.json();
      
      if (data.exists) {
        nombreInput.setCustomValidity('Este autor ya existe');
        nombreFeedback.textContent = 'Este autor ya existe';
        nombreInput.classList.add('is-invalid');
        return false;
      }
      
      return true;
      
    } catch (error) {
      console.error('Error:', error);
      return true; // Permitir en caso de error en la verificación
    }
  }

  function validateFecha() {
    // Si el campo está vacío, es válido (es opcional)
    if (!fechaInput.value) return true;
    
    const fechaNacimiento = new Date(fechaInput.value);
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    
    let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
    const mes = hoy.getMonth() - fechaNacimiento.getMonth();
    
    if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
      edad--;
    }
    
    const isFutureDate = fechaNacimiento > hoy;
    const isAdult = edad >= 18;
    
    if (isFutureDate) {
      fechaInput.setCustomValidity('La fecha no puede ser futura');
      fechaFeedback.textContent = 'La fecha no puede ser futura';
      fechaInput.classList.add('is-invalid');
      return false;
    } else if (!isAdult) {
      fechaInput.setCustomValidity('El autor debe ser mayor de edad (18+ años)');
      fechaFeedback.textContent = 'El autor debe ser mayor de edad (18+ años)';
      fechaInput.classList.add('is-invalid');
      return false;
    } else {
      fechaInput.setCustomValidity('');
      fechaFeedback.textContent = '';
      fechaInput.classList.remove('is-invalid');
      return true;
    }
  }

  function validateBiografia() {
    const isValid = bioInput.value.length <= 500;
    
    if (!isValid) {
      bioInput.setCustomValidity('Máximo 500 caracteres');
    } else {
      bioInput.setCustomValidity('');
    }
    
    return true; // No bloquea el envío, solo muestra advertencia
  }

  function updateCharCounter() {
    const remaining = 500 - bioInput.value.length;
    charCounter.textContent = `${remaining} caracteres restantes`;
    charCounter.style.color = remaining < 0 ? '#dc3545' : '#6c757d';
  }

  // Validación al enviar
  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const isNombreValid = await validateNombre();
    const isFechaValid = validateFecha();
    
    if (isNombreValid && isFechaValid) {
      form.submit();
    } else {
      // Mostrar todos los errores
      if (!isNombreValid) nombreInput.reportValidity();
      if (!isFechaValid && fechaInput.value) fechaInput.reportValidity();
    }
  });
});
</script>