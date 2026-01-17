<style>
  .nav-tabs {
    --bs-nav-link-padding-x: 0.5rem;
    --bs-nav-link-padding-y: 0.5rem;
  }
</style>

<!-- Modal con pestañas -->
<div class="modal fade" id="detallesModalVolumenes" tabindex="-1" aria-labelledby="detallesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detallesModalLabel">Detalles del Libro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <input type="hidden" id="isbnDetalles">

        <!-- Pestañas -->
        <ul class="nav nav-tabs" id="detailsVolTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="details-vol-tab" data-bs-toggle="tab" data-bs-target="#detailsVol" type="button" role="tab" aria-controls="detailsVol" aria-selected="true">Detalles</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="add-vol-tab" data-bs-toggle="tab" data-bs-target="#add-vol" type="button" role="tab" aria-controls="add-vol" aria-selected="false">Agregar Ejemplar</button>
          </li>
          <!-- Tercera Pestaña: Desincorporar Ejemplar -->
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="desincorporate-vol-tab" data-bs-toggle="tab" data-bs-target="#desincorporate-vol" type="button" role="tab" aria-controls="desincorporate-vol" aria-selected="false">Desincorporar Ejemplar</button>
          </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content mt-3" id="detailsVolTabContent">
          <!-- Pestaña Detalles -->
          <div class="tab-pane fade show active" id="detailsVol" role="tabpanel" aria-labelledby="details-vol-tab">
            <div class="container-fluid">
              <div class="row mb-3">
                <div class="col-12">
                  <h5 class="text-primary">Detalles de volúmenes</h5>
                </div>
                <div class="col-6">
                  <p><strong>ISBN:</strong> <span id="vol-isbn-obra">978-1234567890</span></p>
                  <p><strong>ISBN VOLUMEN:</strong> <span id="vol-isbn">978-1234567890</span></p>
                  <p><strong>Título:</strong> <span id="vol-title">Ejemplo de Título</span></p>
                  <p><strong>Autor(es):</strong> <span id="vol-authors">John Doe, Jane Smith</span></p>
                  <p><strong>Año de Publicación:</strong> <span id="vol-year">2020</span></p>
                </div>
                <div class="col-6">
                  <p><strong>Editorial:</strong> <span id="vol-editorial">Editorial Ejemplo</span></p>
                  <p><strong>Categorías:</strong> <span id="vol-categories">Ficción, Misterio</span></p>
                  <p><strong>Fecha de Ingreso:</strong> <span id="vol-entry-date">01/01/2022</span></p>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-12">
                  <h5 class="text-primary">Estado de los Ejemplares</h5>
                </div>
                <div class="col-6">
                  <p><strong>Total de Copias:</strong> <span id="vol-copies">15</span></p>
                  <p><strong>En Circulación:</strong> <span id="vol-circulating">8</span></p>
                </div>
                <div class="col-6">
                  <p><strong>Copias en Mal Estado:</strong> <span id="vol-damaged">2</span></p>
                </div>
              </div>
            </div>
          </div>

          <!-- Pestaña Agregar Ejemplar -->
          <div class="tab-pane fade" id="add-vol" role="tabpanel" aria-labelledby="add-vol-tab">
            <div class="container-fluid">
              <div class="row">
                <div class="col-12">
                  <h5 class="text-primary">Agregar Nuevo Ejemplar</h5>

                  <form id="add-vol-form">
                    <!-- Input para la Cota -->
                    <div class="mb-3">
                      <label for="vol-cota" class="form-label">Cota</label>
                      <input type="text" class="form-control" id="vol-cota" placeholder="Ingrese la cota">
                    </div>
                    <!-- Input para la Ubicación -->
                    <!--div class="mb-3">
                      <label for="copy-location" class="form-label">Ubicación</label>
                      <input type="text" class="form-control" id="copy-location" placeholder="Estante, Sala, etc.">
                    </div-->

                    <div class="mb-3">
                      <label for="vol-location" class="form-label">Ubicacion:</label>
                      <select class="form-select" id="vol-location">
                        <option value="0">Seleccione la ubicación</option>

                      </select>
                    </div>
                    <!-- Select para el Estado -->
                    <div class="mb-3">
                      <label for="vol-source" class="form-label">Suministrado mediante:</label>
                      <select class="form-select" id="vol-source">
                        <option value="2">Donación</option>
                        <option value="1">Red de bibliotecas</option>
                        <!--option value="3">Dañado</option-->
                      </select>
                    </div>
                    <!-- Botón para guardar -->
                    <div class="text-end">
                      <button type="submit" id="botonAgregarEjemplarVol" class="btn btn-success">
                        <span id="botonTextoVol">Agregar Ejemplar</span>
                        <span id="botonCargandoVol" style="display: none;">Cargando...</span> <!-- Indicador de carga -->
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Pestaña Desincorporar Ejemplar -->
          <div class="tab-pane fade" id="desincorporate-vol" role="tabpanel" aria-labelledby="desincorporate-vol-tab">
            <div class="container-fluid">
              <div class="row">
                <div class="col-12">
                  <h5 class="text-primary">Desincorporar Ejemplar</h5>

                  <form id="desincorporate-vol-form">
                    <!-- Input para la Cota -->
                    <div class="mb-3">
                      <label for="vol-cota-des" class="select-label">Cota</label>
                      <select class="form-select" id="vol-cota-des" name="vol-cota-des">

                      </select>
                    </div>

                    <!-- Select para el Estado -->
                    <div class="mb-3">
                      <label for="copy-detail" class="form-label">Descripcion</label>
                      <input type="text" class="form-control" id="vol-detail" name="vol-detail" placeholder="Descripción de la condición física del libro (opcional).">
                    </div>

                    <!-- Botón para guardar -->
                    <div class="text-end">
                      <button type="submit" id="botonDesincorporarEjemplarVol" class="btn btn-success">
                        <span id="botonTextoDesincorporarVol">Desincorporar Ejemplar</span>
                        <span id="botonCargandoDesincorporarVol" style="display: none;">Cargando...</span> <!-- Indicador de carga -->
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>