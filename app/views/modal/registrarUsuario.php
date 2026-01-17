<?php
    include "app/model/checks.php";

    
    $check = new check();
?>
<!-- Modal: Agregar Usuario -->
<div class="modal fade" id="agregarUsuario" tabindex="-1" aria-labelledby="agregarUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content modal-content-custom">
      <div class="modal-header">
        <h5 class="modal-title" id="agregarUsuarioLabel">Registro de Usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="row g-3" action="app/controller/usuarios/c_registro_usuarios.php" method="POST">
          <!-- Campos del formulario -->
          <div class="col-md-6">
            <label for="cedula" class="form-label">Cédula de Identidad</label>
            <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Cédula de identidad" required>
          </div>
          <div class="col-md-6">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" required>
          </div>
          <div class="col-md-6">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Apellido" required>
          </div>
          <div class="col-md-6">
            <label for="fecha_nac" class="form-label">Fecha de Nacimiento</label>
            <input type="date" class="form-control" id="fecha_nac" name="fecha_nac" required>
          </div>
          <div class="col-md-6">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección" required>
          </div>
          <div class="col-md-6">
            <label for="telefono" class="form-label">Teléfono</label>
            <div class="input-group">
              <select class="form-select" id="dominio" name="dominio">
                <option>--Seleccione--</option>
                <option value="0424">0424</option>
                <option value="0414">0414</option>
                <option value="0412">0412</option>
                <option value="0426">0426</option>
                <option value="0416">0416</option>
              </select>
              <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Teléfono" required>
            </div>
          </div>
          <div class="col-md-6">
            <label for="correo" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="correo" name="correo" placeholder="Correo electrónico" required>
          </div>
          <div class="col-md-6">
            <label for="sexo" class="form-label">Género</label>
            <select class="form-select" id="sexo" name="sexo" required>
              <option selected>Selecciona Género</option>
              <option value="0">Femenino</option>
              <option value="1">Masculino</option>
            </select>
          </div>
          <div class="col-md-6">
            <label for="rol" class="form-label">Nivel de Usuario</label>
            <select class="form-select" id="rol" name="rol" required>
              <option selected>Selecciona Nivel</option>
              <option value="1">Administrador</option>
              <option value="2">Usuario</option>
            </select>
          </div>
          <div class="col-md-6">
            <label for="clave" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="clave" name="clave" placeholder="Contraseña" required>
          </div>

          <!-- Permisos -->
          <div class="col-12">
            <h5 class="mt-3">Permisos</h5>
            <hr>
            <?php
              // Asegúrate de que este código PHP se ejecute correctamente en el servidor
              // y que la función `generarCheckboxesPermisos` cree los checkboxes necesarios.
              $check->generarCheckboxesPermisos($conexion);
            ?>
          </div>

          <!-- Botón para registrar -->
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-success btn-lg">Registrar Usuario</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Estilos CSS -->
<style>
  .modal-content-custom {
    border-radius: 15px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    background: linear-gradient(135deg, #f7f7f7, #e0e0e0);
  }

  .modal-header {
    background-color: #4CAF50;
    color: white;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
  }

  .modal-title {
    font-size: 1.5rem;
    font-weight: 600;
  }

  .btn-close {
    color: white;
    opacity: 0.7;
  }

  .btn-close:hover {
    opacity: 1;
  }

  .form-label {
    font-weight: bold;
    color: #333;
  }

  .input-group {
    display: flex;
  }

  .input-group select, .input-group input {
    border-radius: 5px;
  }

  .input-group .form-select {
    width: 80px;
  }

  .btn-lg {
    width: 100%;
    padding: 12px;
    font-size: 1.1rem;
  }

  .modal-body {
    padding: 2rem;
  }

  .col-md-6 {
    padding: 10px;
  }

  /* Estilos específicos para los checkboxes */
  .checkbox-permission {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .checkbox-permission label {
    font-size: 1rem;
    color: #555;
    cursor: pointer;
  }

  .checkbox-permission input[type="checkbox"] {
    margin-right: 8px;
  }
</style>
