<style>
    /* Estilo para el modal */
#agregarVisitante .modal-content {
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Estilo para los inputs */
#cedula-visitante,
#nombre-visitante,
#numero-visitante,
#direccion-visitante {
  font-size: 1rem;
  padding: 0.75rem;
  border-radius: 0.375rem;
  border: 1px solid #ced4da;
  /*margin-bottom: 1rem;*/
}

#cedula-visitante::placeholder,
#nombre-visitante::placeholder,
#numero-visitante::placeholder,
#direccion-visitante::placeholder {
  color: #6c757d;
  opacity: 1; /* Asegura que el placeholder sea visible */
}

/* Estilo para el select */
#prefijo-visitante {
  font-size: 1rem;
  padding: 0.75rem;
  border-radius: 0.375rem;
  border: 1px solid #ced4da;
  width: 120px;
}

/* Estilo para el botón de submit */
#aggVisitanteForm .btn {
  padding: 10px 20px;
  border-radius: 0.375rem;
  font-size: 1rem;
}

.btn-success {
  background-color: #28a745;
  border-color: #28a745;
}

.btn-danger {
  background-color: #dc3545;
  border-color: #dc3545;
}

/* Estilo adicional para los márgenes */
.mb-3 {
  margin-bottom: 1.5rem;
}

/* Asegura que los botones estén alineados al final */
.text-end {
  text-align: right;
}

/* Flexbox para el teléfono */
.d-flex {
  display: flex;
  align-items: center;
}

.gap-2 {
  gap: 0.5rem;
}



</style>

<div class="modal fade" id="agregarVisitante" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5 text-primary" id="exampleModalLabel">Registrar Visitante</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="aggVisitanteForm" action="app/controller/visitantes/c_visitantes.php" method="POST">
          <div class="mb-3">
            <label for="cedula-visitante" class="form-label">Cédula</label>
            <input type="text" name="cedulaVisitante" id="validarCedulaBD" class="form-control" placeholder="Ingrese la cédula" maxlength="9" pattern="[0-9]+" title="Solo se permiten números" oninput="this.value = this.value.replace(/\D/g, '')" required>
          </div>
          <div class="mb-3">
            <label for="nombre-visitante" class="form-label">Nombre</label>
            <input type="text" name="nombreVisitante" id="nombre-visitante" class="form-control" placeholder="Ingrese el nombre">
          </div>
          <div class="mb-3">
            <label for="telefono-visitante" class="form-label">Teléfono</label>
            <div class="d-flex gap-2">
              <!-- Campo para el prefijo -->
              <select name="prefijoVisitante" id="prefijo-visitante" class="form-select" style="width: auto;">
                <option value="0412">0412 (Digitel)</option>
                <option value="0414">0414 (Movistar)</option>
                <option value="0416">0416 (Movilnet)</option>
                <option value="0424">0424 (Movistar)</option>
                <option value="0426">0426 (Movilnet)</option>
                <option value="0293">0293 (Teléfono Fijo)</option>
              </select>

              <!-- Campo para el número -->
              <input type="text" name="numeroVisitante" id="numero-visitante" class="form-control" placeholder="Número principal" maxlength="7" pattern="[0-9]+" title="Solo se permiten números" oninput="this.value = this.value.replace(/\D/g, '')" required>
            </div>
          </div>
          <div class="mb-3">
            <label for="direccion-visitante" class="form-label">Dirección</label>
            <input type="text" name="dirVisitante" id="direccion-visitante" class="form-control" placeholder="Ingrese la dirección">
          </div>

          <div class="mb-3">
          <select class="form-select" name="sexoVisitante">
            <option selected>Genero</option>
            <option value="0">F</option>
            <option value="1">M</option>
            </select> 
          </div>

          <div class="text-end">
            <button type="submit" id="botonEF" class="btn btn-success">Agregar</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
