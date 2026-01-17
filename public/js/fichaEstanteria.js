
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const buscador = document.getElementById('buscador-libros');
    const librosGrid = document.querySelector('.libros-grid');
    const librosCards = Array.from(document.querySelectorAll('.libro-card'));
    const toggleViewBtn = document.getElementById('toggle-view');
    const groupBySelect = document.getElementById('group-by');
    const sortBySelect = document.getElementById('sort-by');
    const filterTags = document.querySelectorAll('.filter-tag');
    const itemsPerPageSelect = document.getElementById('items-per-page');
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');
    const pageIndicator = document.getElementById('page-indicator');
    const librosMostradosSpan = document.getElementById('libros-mostrados');
    const prevPageBottomBtn = document.getElementById('prev-page-bottom');
    const nextPageBottomBtn = document.getElementById('next-page-bottom');
    const pageIndicatorBottom = document.getElementById('page-indicator-bottom');
    const librosMostradosBottomSpan = document.getElementById('libros-mostrados-bottom');

    // Variables de estado
    let currentPage = 1;
    let itemsPerPage = 6;
    let filteredBooks = librosCards;
    let currentGroupBy = 'none';
    let currentSortBy = 'posicion';
    let currentFilter = 'all';
    let isCompactView = false;

    // Función para aplicar todos los filtros y ordenamientos
    function applyFiltersAndUpdate() {
        // 1. Aplicar búsqueda
        const searchTerm = buscador.value.toLowerCase().trim();
        
        filteredBooks = librosCards.filter(card => {
            const titulo = card.querySelector('.libro-titulo').textContent.toLowerCase();
            const autor = card.querySelector('.libro-autor').textContent.toLowerCase();
            const posicion = card.querySelector('.libro-posicion').textContent.toLowerCase();
            
            // Filtro de búsqueda
            const searchMatch = searchTerm === '' || 
                              titulo.includes(searchTerm) || 
                              autor.includes(searchTerm) || 
                              posicion.includes(searchTerm);
            
            // Filtro por estado
            let filterMatch = true;
            if (currentFilter === 'disponible') {
                const disponibles = parseInt(card.dataset.disponibles);
                filterMatch = disponibles > 0;
            } else if (currentFilter === 'prestado') {
                const disponibles = parseInt(card.dataset.disponibles);
                const ejemplares = parseInt(card.dataset.ejemplares);
                filterMatch = disponibles < ejemplares;
            } else if (currentFilter === 'danado') {
                const estado = card.dataset.estado;
                filterMatch = estado.includes('dañado') || estado.includes('danado');
            }
            
            return searchMatch && filterMatch;
        });

        // 2. Ordenar
        sortBooks();

        // 3. Agrupar (si es necesario)
        if (currentGroupBy !== 'none') {
            groupBooks();
        } else {
            // Limpiar agrupación si no está activa
            document.querySelectorAll('.group-header').forEach(el => el.remove());
        }

        // 4. Actualizar paginación
        currentPage = 1;
        updatePagination();

        // 5. Mostrar mensaje si no hay resultados
        const mensajeExistente = document.getElementById('mensaje-no-resultados');
        if (searchTerm !== '' && filteredBooks.length === 0) {
            if (!mensajeExistente) {
                const mensaje = document.createElement('div');
                mensaje.id = 'mensaje-no-resultados';
                mensaje.textContent = 'No se encontraron libros con ese criterio.';
                mensaje.className = 'sin-resultados';
                librosGrid.appendChild(mensaje);
            }
        } else if (mensajeExistente) {
            mensajeExistente.remove();
        }
    }

    // Función para ordenar libros
    function sortBooks() {
        filteredBooks.sort((a, b) => {
            const aValue = getSortValue(a);
            const bValue = getSortValue(b);
            
            if (aValue < bValue) return -1;
            if (aValue > bValue) return 1;
            return 0;
        });
    }

    function getSortValue(card) {
        switch (currentSortBy) {
            case 'titulo':
                return card.querySelector('.libro-titulo').textContent.toLowerCase();
            case 'autor':
                return card.querySelector('.libro-autor').textContent.toLowerCase();
            case 'prestamos':
                const disponibles = parseInt(card.dataset.disponibles);
                const ejemplares = parseInt(card.dataset.ejemplares);
                return ejemplares - disponibles; // Más prestados primero
            case 'posicion':
            default:
                return card.querySelector('.libro-posicion').textContent.toLowerCase();
        }
    }

    // Función para agrupar libros
    function groupBooks() {
        const groups = {};
        
        filteredBooks.forEach(card => {
            let groupValue;
            if (currentGroupBy === 'autor') {
                groupValue = card.dataset.autor;
            } else if (currentGroupBy === 'estado') {
                groupValue = card.dataset.estado.charAt(0).toUpperCase() + card.dataset.estado.slice(1);
            }
            
            if (!groups[groupValue]) groups[groupValue] = [];
            groups[groupValue].push(card);
        });

        // Limpiar el grid
        librosGrid.innerHTML = '';

        // Ordenar grupos alfabéticamente
        const sortedGroups = Object.keys(groups).sort();
        
        // Añadir grupos al grid
        sortedGroups.forEach(group => {
            const header = document.createElement('div');
            header.className = 'group-header';
            header.textContent = group;
            librosGrid.appendChild(header);
            
            groups[group].forEach(card => {
                librosGrid.appendChild(card);
            });
        });
    }

    // Función para actualizar la paginación
    function updatePagination() {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const booksToShow = currentGroupBy === 'none' ? 
            filteredBooks.slice(startIndex, endIndex) : 
            filteredBooks; // Mostrar todos si están agrupados
        
        // Ocultar todos los libros
        librosCards.forEach(card => card.style.display = 'none');
        
        // Mostrar solo los libros de la página actual (si no hay agrupación)
        if (currentGroupBy === 'none') {
            booksToShow.forEach(card => card.style.display = isCompactView ? 'flex' : 'block');
        } else {
            // Mostrar todos los libros si están agrupados
            filteredBooks.forEach(card => card.style.display = isCompactView ? 'flex' : 'block');
        }
        
        // Actualizar controles de paginación
        const totalPages = Math.ceil(filteredBooks.length / itemsPerPage);
        
        librosMostradosSpan.textContent = booksToShow.length;
        librosMostradosBottomSpan.textContent = booksToShow.length;
        pageIndicator.textContent = currentPage;
        pageIndicatorBottom.textContent = currentPage;
        
        prevPageBtn.disabled = currentPage === 1;
        nextPageBtn.disabled = currentPage === totalPages || filteredBooks.length <= itemsPerPage;
        prevPageBottomBtn.disabled = currentPage === 1;
        nextPageBottomBtn.disabled = currentPage === totalPages || filteredBooks.length <= itemsPerPage;
        
        // Scroll suave al principio
        if (currentGroupBy === 'none') {
            //librosGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // Event Listeners
    buscador.addEventListener('input', applyFiltersAndUpdate);
    
    toggleViewBtn.addEventListener('click', function() {
        isCompactView = !isCompactView;
        
        librosGrid.classList.toggle('compact-view');
        librosCards.forEach(card => card.classList.toggle('compact'));
        
        const icon = this.querySelector('i');
        if (isCompactView) {
            icon.className = 'bi bi-grid';
            this.innerHTML = '<i class="bi bi-grid"></i> Vista Normal';
        } else {
            icon.className = 'bi bi-list-ul';
            this.innerHTML = '<i class="bi bi-list-ul"></i> Vista Compacta';
        }
        
        updatePagination();
    });
    
    groupBySelect.addEventListener('change', function() {
        currentGroupBy = this.value;
        applyFiltersAndUpdate();
    });
    
    sortBySelect.addEventListener('change', function() {
        currentSortBy = this.value;
        applyFiltersAndUpdate();
    });
    
    filterTags.forEach(tag => {
        tag.addEventListener('click', function() {
            filterTags.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            applyFiltersAndUpdate();
        });
    });
    
    itemsPerPageSelect.addEventListener('change', function() {
        itemsPerPage = parseInt(this.value);
        currentPage = 1;
        updatePagination();
    });
    
    prevPageBtn.addEventListener('click', goToPrevPage);
    nextPageBtn.addEventListener('click', goToNextPage);
    prevPageBottomBtn.addEventListener('click', goToPrevPage);
    nextPageBottomBtn.addEventListener('click', goToNextPage);
    
    function goToPrevPage() {
        if (currentPage > 1) {
            currentPage--;
            updatePagination();
        }
    }
    
    function goToNextPage() {
        if ((currentPage * itemsPerPage) < filteredBooks.length) {
            currentPage++;
            updatePagination();
        }
    }

    // Inicialización
    applyFiltersAndUpdate();
});













document.addEventListener('DOMContentLoaded', function() {
    const libroModal = new bootstrap.Modal(document.getElementById('libroModal'));
    
    // Manejar clic en botón de detalles
    document.querySelectorAll('[data-action="details"]').forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        const card = this.closest('.libro-card');
        const libroData = JSON.parse(card.dataset.libro);
        
        // Cargar datos básicos
        document.getElementById('modalTitulo').textContent = libroData.titulo;
        document.getElementById('modalAutor').textContent = libroData.autor;
        document.getElementById('modalPortada').src = libroData.portada;
        document.getElementById('modalIsbn').textContent = libroData.isbn;
        document.getElementById('modalFichaLibroBtn').href = 'index.php?vista=fichaLibro&isbn=' + libroData.isbn;
        document.getElementById('modalEditorial').textContent = libroData.editorial;
        document.getElementById('modalAnio').textContent = libroData.anio;
        document.getElementById('modalEdicion').textContent = libroData.edicion;
        document.getElementById('modalCategorias').textContent = libroData.categorias;
        document.getElementById('modalDisponibles').textContent = libroData.disponibles;
        document.getElementById('modalTotalEjemplares').textContent = libroData.ejemplares;
        document.getElementById('modalPrestados').textContent = libroData.en_circulacion;
        document.getElementById('modalDanados').textContent = libroData.danados;
        
        // Calcular porcentaje de disponibilidad
        const porcentaje = (parseInt(libroData.disponibles) / parseInt(libroData.ejemplares)) * 100;
        document.getElementById('modalDisponibilidadBar').style.width = `${porcentaje}%`;
        
        // Llenar tabla de ubicaciones
        const tbodyUbicaciones = document.querySelector('#tablaUbicaciones tbody');
        tbodyUbicaciones.innerHTML = '';
        
        libroData.ubicaciones.forEach(ubicacion => {
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${ubicacion.fila}</td>
            <td>${ubicacion.ejemplares}</td>
            <td>${ubicacion.disponibles}</td>
            <td>${ubicacion.prestados}</td>
          `;
          tbodyUbicaciones.appendChild(row);
        });
        
        // Cargar historial de préstamos (simulado - en producción sería con AJAX)
        const historialList = document.getElementById('historialPrestamos');
        historialList.innerHTML = '';
        
        // Esto es un ejemplo - en tu implementación real deberías hacer una petición AJAX
        const ejemploHistorial = [
          { fecha: '2023-05-15', usuario: 'Juan Pérez', estado: 'Devuelto' },
          { fecha: '2023-04-10', usuario: 'María Gómez', estado: 'Prestado' }
        ];
        
        // Historial
  fetch(`app/controller/estanterias/c_historial_prestamos.php?isbn=${libroData.isbn}`)
    .then(response => response.json())
    .then(historial => {
      const historialList = document.getElementById('historialPrestamos');
      historialList.innerHTML = '';
      
      historial.forEach(item => {
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        li.innerHTML = `
          <div>
            <strong>${item.fecha_formateada}</strong> - ${item.nombre}
            <div class="text-muted small">${item.estado}</div>
          </div>
          <span class="badge ${item.estado === 'Activo' ? 'bg-info' : 'bg-secondary'}">
            ${item.estado}
          </span>
        `;
        historialList.appendChild(li);
      });
    })
    .catch(error => {
      console.error('Error al cargar historial:', error);
      document.getElementById('historialPrestamos').innerHTML = 
        '<li class="list-group-item text-muted">No se pudo cargar el historial</li>';
    });
        
        libroModal.show();
      });
    });
    
    // Limpiar el backdrop al cerrar el modal
    document.getElementById('libroModal').addEventListener('hidden.bs.modal', function() {
      document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
      document.body.classList.remove('modal-open');
    });
  });
  