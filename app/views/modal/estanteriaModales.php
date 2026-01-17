<!-- Modal de Detalles del Libro -->
<div class="modal fade" id="libroModal" tabindex="-1" aria-labelledby="libroModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="libroModalLabel">Detalles del Libro</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="rowe">
          <div class="col-md-4 text-center">
            <img id="modalPortada" src="" alt="Portada del libro" class="img-fluid rounded shadow mb-3">
            <div class="d-grid gap-2">
              <a class="btn btn-outline-primary" id="modalFichaLibroBtn" href="">
                <i class="bi bi-eye-fill"></i> Ir a ficha de la obra
              </a>
              <!--button class="btn btn-outline-success">
                <i class="bi bi-printer-fill"></i> Imprimir Código
              </button-->
            </div>
          </div>
          
          <div class="col-md-8" style="
    padding-left: 10px;
">
            <h3 id="modalTitulo"></h3>
            <p class="text-muted" id="modalAutor"></p>
            
            <div class="row mb-3">
              <div class="col-md-6">
                <div class="card mb-3">
                  <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Información Básica</h6>
                    <ul class="list-unstyled">
                      <li><strong>ISBN:</strong> <span id="modalIsbn"></span></li>
                      <li><strong>Editorial:</strong> <span id="modalEditorial"></span></li>
                      <li><strong>Año:</strong> <span id="modalAnio"></span></li>
                      <li><strong>Edición:</strong> <span id="modalEdicion"></span></li>
                      <li><strong>Categorías:</strong> <span id="modalCategorias"></span></li>
                    </ul>
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="card mb-3">
                  <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Disponibilidad</h6>
                    <div class="availability-chart mb-3">
                      <div class="d-flex justify-content-between mb-1">
                        <span>Disponibles: <strong id="modalDisponibles"></strong></span>
                        <span>Total: <strong id="modalTotalEjemplares"></strong></span>
                      </div>
                      <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 0%" id="modalDisponibilidadBar"></div>
                      </div>
                    </div>
                    <ul class="list-unstyled">
                      <li><strong>En préstamo:</strong> <span id="modalPrestados"></span></li>
                      <li><strong>Dañados:</strong> <span id="modalDanados"></span></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="card mb-3">
              <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Ubicaciones en Estantería</h6>
                <div class="table-responsive">
                  <table class="table table-sm" id="tablaUbicaciones">
                    <thead>
                      <tr>
                        <th>Fila</th>
                        <th>Ejemplares</th>
                        <th>Disponibles</th>
                        <th>Prestados</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Se llenará dinámicamente -->
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            
            <div class="card">
              <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Historial Reciente</h6>
                <ul class="list-group list-group-flush" id="historialPrestamos">
                  <!-- Se llenará dinámicamente -->
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle"></i> Cerrar
        </button>
        <!--button type="button" class="btn btn-primary">
          <i class="bi bi-pencil"></i> Editar Libro
        </button-->
      </div>
    </div>
  </div>
</div>


















<!-- Modal Mover Copia -->
<div class="modal fade" id="moverCopiaModal" tabindex="-1" aria-labelledby="moverCopiaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="moverCopiaModalLabel">Mover Ejemplar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Paso 1: Búsqueda de ejemplar -->
        <div id="pasoBusqueda">
          <div class="card mb-4">
            <div class="card-header bg-light">
              <h6 class="mb-0">Buscar Ejemplar a Mover</h6>
            </div>
            <div class="card-body">
              <div class="row mb-3">
                <div class="col-md-8">
                  <div class="input-group">
                    <input type="text" id="buscarEjemplar" class="form-control" placeholder="Buscar por título, ISBN, autor o cota...">
                    <button class="btn btn-primary" type="button" id="btnBuscarEjemplar">
                      <i class="bi bi-search"></i> Buscar
                    </button>
                  </div>
                </div>
                <div class="col-md-4">
                  <select class="form-select" id="filtroBusqueda">
                    <option value="all">Todos los campos</option>
                    <option value="titulo">Título</option>
                    <option value="isbn">ISBN</option>
                    <option value="autor">Autor</option>
                    <option value="cota">Cota</option>
                  </select>
                </div>
              </div>
              
              <div class="table-responsive">
                <table class="table table-hover" id="tablaResultados">
                  <thead>
                    <tr>
                      <th>Cota</th>
                      <th>Título</th>
                      <th>Autor</th>
                      <th>ISBN</th>
                      <th>Estantería Actual</th>
                      <th>Fila</th>
                      <th>Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Resultados de búsqueda se cargarán aquí -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Paso 2: Selección de nueva ubicación -->
        <div id="pasoUbicacion" style="display: none;">
          <form id="formMoverCopia">
            <input type="hidden" id="ejemplarId">
            
            <!-- Información del ejemplar seleccionado -->
            <div class="card mb-4">
              <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Ejemplar Seleccionado</h6>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCambiarEjemplar">
                  <i class="bi bi-arrow-left"></i> Cambiar ejemplar
                </button>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3">
                    <img id="ejemplarPortada" src="" alt="Portada" class="img-thumbnail" style="max-height: 150px;">
                  </div>
                  <div class="col-md-9">
                    <div class="row">
                      <div class="col-md-6">
                        <p><strong>Título:</strong> <span id="ejemplarTitulo"></span></p>
                        <p><strong>Autor:</strong> <span id="ejemplarAutor"></span></p>
                        <p><strong>ISBN:</strong> <span id="ejemplarIsbn"></span></p>
                      </div>
                      <div class="col-md-6">
                        <p><strong>Cota:</strong> <span id="ejemplarCota"></span></p>
                        <p><strong>Estantería Actual:</strong> <span id="ejemplarEstanteria"></span></p>
                        <p><strong>Fila Actual:</strong> <span id="ejemplarFila"></span></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Selector de nueva ubicación -->
            <div class="card">
              <div class="card-header bg-light">
                <h6 class="mb-0">Seleccionar Nueva Ubicación</h6>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="nuevaEstanteria" class="form-label">Nueva Estantería</label>
                    <select class="form-select" id="nuevaEstanteria" required>
                      <option value="" selected disabled>Seleccionar estantería...</option>
                      <!-- Se llenará dinámicamente -->
                    </select>
                    <div class="form-text">Capacidad: <span id="capacidadEstanteria">0</span>/<span id="maximaEstanteria">0</span></div>
                  </div>
                  
                  <div class="col-md-6 mb-3">
                    <label for="nuevaFila" class="form-label">Nueva Fila</label>
                    <select class="form-select" id="nuevaFila" disabled required>
                      <option value="" selected disabled>Primero seleccione una estantería</option>
                    </select>
                    <div class="form-text">Espacio disponible: <span id="espacioFila">0</span>/<span id="capacidadFila">0</span></div>
                  </div>
                </div>
                
                <div class="alert alert-info mt-3">
                  <i class="bi bi-info-circle"></i> Puede mover este ejemplar a cualquier estantería y fila con espacio disponible.
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-warning" id="confirmarMovimiento" style="display: none;">Confirmar Movimiento</button>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
  // 1. Inicialización del modal y variables
  const moverCopiaModalEl = document.getElementById('moverCopiaModal');
  if (!moverCopiaModalEl) {
    console.error('No se encontró el modal con ID "moverCopiaModal"');
    return;
  }
  
  const moverCopiaModal = new bootstrap.Modal(moverCopiaModalEl);
  let estanteriasData = [];
  let ejemplarSeleccionado = null;

  // 2. Configuración del botón para abrir el modal
  const btnMoverCopia = document.querySelector('[data-action="scan"]');
  if (!btnMoverCopia) {
    console.error('No se encontró el botón con data-action="scan"');
    return;
  }

  btnMoverCopia.addEventListener('click', function() {
    resetearModal();
    moverCopiaModal.show();
  });

  // 3. Función para resetear el modal
  function resetearModal() {
    const pasoBusqueda = document.getElementById('pasoBusqueda');
    const pasoUbicacion = document.getElementById('pasoUbicacion');
    const confirmarBtn = document.getElementById('confirmarMovimiento');
    
    if (!pasoBusqueda || !pasoUbicacion || !confirmarBtn) {
      console.error('Elementos del modal no encontrados');
      return;
    }
    
    pasoBusqueda.style.display = 'block';
    pasoUbicacion.style.display = 'none';
    confirmarBtn.style.display = 'none';
    
    const tablaResultados = document.getElementById('tablaResultados');
    if (tablaResultados && tablaResultados.tBodies.length > 0) {
      tablaResultados.tBodies[0].innerHTML = '';
    }
    
    const buscarInput = document.getElementById('buscarEjemplar');
    if (buscarInput) buscarInput.value = '';
  }

  // 4. Funcionalidad de búsqueda
  const btnBuscar = document.getElementById('btnBuscarEjemplar');
  const buscarInput = document.getElementById('buscarEjemplar');
  
  if (btnBuscar && buscarInput) {
    btnBuscar.addEventListener('click', buscarEjemplares);
    buscarInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') buscarEjemplares();
    });
  }

  function buscarEjemplares() {
    const buscarInput = document.getElementById('buscarEjemplar');
    const filtroSelect = document.getElementById('filtroBusqueda');
    const tablaResultados = document.getElementById('tablaResultados');
    
    if (!buscarInput || !filtroSelect || !tablaResultados) {
      console.error('Elementos de búsqueda no encontrados');
      return;
    }
    
    const query = buscarInput.value.trim();
    const filtro = filtroSelect.value;
    
    if (query.length < 2) {
      mostrarAlerta('warning', 'Ingrese al menos 2 caracteres para buscar');
      return;
    }
    
    fetch(`app/controller/estanterias/c_buscar_ejemplares.php?query=${encodeURIComponent(query)}&filtro=${filtro}`)
      .then(response => {
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        return response.json();
      })
      .then(data => mostrarResultadosBusqueda(data))
      .catch(error => {
        console.error('Error al buscar ejemplares:', error);
        mostrarAlerta('danger', 'Error al realizar la búsqueda');
      });
  }

  function mostrarResultadosBusqueda(data) {
    const tbody = document.querySelector('#tablaResultados tbody');
    if (!tbody) {
      console.error('No se encontró el tbody en la tabla de resultados');
      return;
    }
    
    tbody.innerHTML = '';
    
    if (data.length === 0) {
      const row = document.createElement('tr');
      row.innerHTML = '<td colspan="7" class="text-center">No se encontraron ejemplares</td>';
      tbody.appendChild(row);
      return;
    }
    
    data.forEach(ejemplar => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${ejemplar.cota || ''}</td>
        <td>${ejemplar.titulo || ''}</td>
        <td>${ejemplar.autor || ''}</td>
        <td>${ejemplar.isbn || ''}</td>
        <td>${ejemplar.estanteria || ''} (${ejemplar.tematica || ''})</td>
        <td>Fila ${ejemplar.fila || ''}</td>
        <td>
          <button class="btn btn-sm btn-outline-primary btn-seleccionar" 
                  data-id="${ejemplar.id || ''}"
                  data-cota="${ejemplar.cota || ''}"
                  data-titulo="${ejemplar.titulo || ''}"
                  data-autor="${ejemplar.autor || ''}"
                  data-isbn="${ejemplar.isbn || ''}"
                  data-estanteria="${ejemplar.estanteria || ''}"
                  data-tematica="${ejemplar.tematica || ''}"
                  data-fila="${ejemplar.fila || ''}"
                  data-portada="${ejemplar.portada || ''}">
            Seleccionar
          </button>
        </td>
      `;
      tbody.appendChild(row);
    });
    
    // Agregar event listeners a los nuevos botones
    document.querySelectorAll('.btn-seleccionar').forEach(btn => {
      btn.addEventListener('click', function() {
        seleccionarEjemplar(this.dataset);
      });
    });
  }

  // 5. Selección de ejemplar
  function seleccionarEjemplar(datos) {
    ejemplarSeleccionado = datos;
    
    // Mostrar datos del ejemplar seleccionado
    document.getElementById('ejemplarTitulo').textContent = datos.titulo || '';
    document.getElementById('ejemplarAutor').textContent = datos.autor || '';
    document.getElementById('ejemplarIsbn').textContent = datos.isbn || '';
    document.getElementById('ejemplarCota').textContent = datos.cota || '';
    document.getElementById('ejemplarEstanteria').textContent = `${datos.estanteria || ''} (${datos.tematica || ''})`;
    document.getElementById('ejemplarFila').textContent = datos.fila || '';
    
    const portada = document.getElementById('ejemplarPortada');
    if (portada) {
      portada.src = datos.portada || 'img/portada-default.jpg';
      portada.alt = `Portada de ${datos.titulo || 'ejemplar'}`;
    }
    
    document.getElementById('ejemplarId').value = datos.id || '';
    
    // Cambiar al paso de selección de ubicación
    document.getElementById('pasoBusqueda').style.display = 'none';
    document.getElementById('pasoUbicacion').style.display = 'block';
    document.getElementById('confirmarMovimiento').style.display = 'block';
    
    // Cargar estanterías disponibles
    cargarEstanteriasDisponibles();
  }

  // 6. Botón para volver a la búsqueda
  const btnCambiarEjemplar = document.getElementById('btnCambiarEjemplar');
  if (btnCambiarEjemplar) {
    btnCambiarEjemplar.addEventListener('click', function() {
      resetearModal();
    });
  }

  // 7. Cargar estanterías disponibles
  function cargarEstanteriasDisponibles() {
    fetch('app/controller/estanterias/c_estanterias_api.php')
      .then(response => {
        if (!response.ok) throw new Error('Error al cargar estanterías');
        return response.json();
      })
      .then(data => {
        estanteriasData = data;
        const select = document.getElementById('nuevaEstanteria');
        if (!select) return;
        
        select.innerHTML = '<option value="" selected disabled>Seleccionar estantería...</option>';
        
        data.forEach(estanteria => {
          const espacioDisponible = estanteria.capacidad - estanteria.ocupacion;
          if (espacioDisponible > 0) {
            const option = document.createElement('option');
            option.value = estanteria.id;
            option.textContent = `${estanteria.codigo} (${estanteria.descripcion}) - ${espacioDisponible} espacios`;
            option.dataset.capacidad = estanteria.capacidad;
            option.dataset.ocupacion = estanteria.ocupacion;
            select.appendChild(option);
          }
        });
      })
      .catch(error => {
        console.error('Error al cargar estanterías:', error);
        mostrarAlerta('danger', 'Error al cargar estanterías disponibles');
      });
  }

  // 8. Manejo de selección de estantería y fila
  const nuevaEstanteriaSelect = document.getElementById('nuevaEstanteria');
  if (nuevaEstanteriaSelect) {
    nuevaEstanteriaSelect.addEventListener('change', function() {
      const estanteriaId = this.value;
      const estanteria = estanteriasData.find(e => e.id == estanteriaId);
      
      if (!estanteria) return;
      
      document.getElementById('capacidadEstanteria').textContent = estanteria.ocupacion;
      document.getElementById('maximaEstanteria').textContent = estanteria.capacidad;
      
      // Habilitar y cargar filas
      const filasSelect = document.getElementById('nuevaFila');
      if (!filasSelect) return;
      
      filasSelect.disabled = false;
      filasSelect.innerHTML = '<option value="" selected disabled>Seleccionar fila...</option>';
      
      fetch(`app/controller/estanterias/c_filas.php?estanteriaId=${estanteriaId}`)
        .then(response => {
          if (!response.ok) throw new Error('Error al cargar filas');
          return response.json();
        })
        .then(filas => {
          filas.forEach(fila => {
            const espacioDisponible = fila.capacidad - fila.ocupacion;
            if (espacioDisponible > 0) {
              const option = document.createElement('option');
              option.value = fila.id;
              option.textContent = `Fila ${fila.numero} - ${espacioDisponible} espacios disponibles`;
              option.dataset.capacidad = fila.capacidad;
              option.dataset.ocupacion = fila.ocupacion;
              filasSelect.appendChild(option);
            }
          });
          
          if (filasSelect.options.length === 1) {
            filasSelect.innerHTML = '<option value="" disabled>No hay filas con espacio disponible</option>';
          }
        })
        .catch(error => {
          console.error('Error al cargar filas:', error);
          mostrarAlerta('danger', 'Error al cargar filas disponibles');
        });
    });
  }

  // 9. Mostrar capacidad de fila seleccionada
  const nuevaFilaSelect = document.getElementById('nuevaFila');
  if (nuevaFilaSelect) {
    nuevaFilaSelect.addEventListener('change', function() {
      if (this.value && this.options[this.selectedIndex]) {
        const capacidad = this.options[this.selectedIndex].dataset.capacidad;
        const ocupacion = this.options[this.selectedIndex].dataset.ocupacion;
        document.getElementById('espacioFila').textContent = ocupacion;
        document.getElementById('capacidadFila').textContent = capacidad;
      }
    });
  }

  // 10. Confirmar movimiento con actualización en tiempo real
  const confirmarBtn = document.getElementById('confirmarMovimiento');
  if (confirmarBtn) {
    confirmarBtn.addEventListener('click', function() {
      const form = document.getElementById('formMoverCopia');
      if (!form) return;
      
      if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
      }
      
      const datos = {
        ejemplarId: document.getElementById('ejemplarId').value,
        nuevaEstanteria: document.getElementById('nuevaEstanteria').value,
        nuevaFila: document.getElementById('nuevaFila').value
      };
      
      fetch('app/controller/estanterias/c_mover_ejemplar.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(datos)
      })
      .then(response => {
        if (!response.ok) throw new Error('Error en la respuesta del servidor');
        return response.json();
      })
      .then(data => {
        if (data.success) {
          mostrarAlerta('success', data.message);
          
          // Actualizar la interfaz sin recargar
          actualizarInterfaz(data);
          
          // Cerrar el modal después de 1.5 segundos
          setTimeout(() => {
            moverCopiaModal.hide();
            window.location.reload();
          }, 1000);
        } else {
          throw new Error(data.message || 'Error al mover el ejemplar');
        }
      })
      .catch(error => {
        console.error('Error al mover ejemplar:', error);
        mostrarAlerta('danger', error.message || 'Error al mover el ejemplar');
      });
    });
  }

  // 11. Función para actualizar la interfaz en tiempo real
  function actualizarInterfaz(data) {
    if (!data.ejemplar) return;
    
    // 1. Actualizar el card del ejemplar movido
    const cardEjemplar = document.querySelector(`.libro-card[data-id="${data.ejemplar.id}"]`);
    if (cardEjemplar) {
      // Actualizar posición
      const posicionElement = cardEjemplar.querySelector('.libro-posicion');
      if (posicionElement) {
        posicionElement.textContent = `Fila ${data.ejemplar.fila}`;
      }
      
      // Actualizar datos en atributos
      cardEjemplar.dataset.estanteria = data.ejemplar.estanteria;
      cardEjemplar.dataset.fila = data.ejemplar.fila;
    }
    
    // 2. Actualizar contadores de estanterías
    if (data.estanteriaOrigen) {
      actualizarContadoresEstanteria(data.estanteriaOrigen);
    }
    if (data.ejemplar.estanteria) {
      actualizarContadoresEstanteria(data.ejemplar.estanteria);
    }
  }

  // 12. Función para actualizar contadores de estantería
  function actualizarContadoresEstanteria(codigoEstanteria) {
    if (!codigoEstanteria) return;
    
    fetch(`app/controller/estanterias/c_estanteria_info.php?codigo=${codigoEstanteria}`)
    .then(response => response.json())
    .then(data => {
      // Actualizar el encabezado de la estantería
      const estanteriaHeader = document.querySelector('.estanteria-header');
      if (estanteriaHeader) {
        const capacidadElement = estanteriaHeader.querySelector('.estanteria-capacidad');
        if (capacidadElement) {
          capacidadElement.innerHTML = `<i class="bi bi-book"></i> ${data.cantidadTotal} / ${data.capacidad} libros`;
        }
        
        // Actualizar porcentaje de ocupación
        const porcentaje = Math.round((data.cantidadTotal / data.capacidad) * 100);
        const progressBar = estanteriaHeader.querySelector('.progress-bar');
        if (progressBar) {
          progressBar.style.width = `${porcentaje}%`;
        }
        const statValue = estanteriaHeader.querySelector('.stat-value');
        if (statValue) {
          statValue.textContent = `${porcentaje}%`;
        }
      }
      
      // Actualizar stats de préstamos si es necesario
      actualizarStatsEstanteria();
    })
    .catch(error => console.error('Error al actualizar contadores:', error));
  }

  // 13. Función para actualizar stats de la estantería
  function actualizarStatsEstanteria() {
    fetch('app/controller/estanterias/c_estanteria_stats.php')
    .then(response => response.json())
    .then(data => {
      const statsPrestamos = document.querySelector('.stat-card:nth-child(2) .stat-value');
      const statsDanados = document.querySelector('.stat-card:nth-child(3) .stat-value');
      
      if (statsPrestamos) statsPrestamos.textContent = data.prestamos_activos || 0;
      if (statsDanados) statsDanados.textContent = data.ejemplares_danados || 0;
    })
    .catch(error => console.error('Error al actualizar stats:', error));
  }

  // 14. Función para mostrar alertas
  function mostrarAlerta(tipo, mensaje) {
    const modalBody = document.querySelector('#moverCopiaModal .modal-body');
    if (!modalBody) return;
    
    // Eliminar alertas anteriores
    document.querySelectorAll('#moverCopiaModal .alert').forEach(el => el.remove());
    
    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
    alerta.innerHTML = `
      ${mensaje}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    modalBody.prepend(alerta);
    
    setTimeout(() => {
      alerta.remove();
    }, 5000);
  }
});
</script>