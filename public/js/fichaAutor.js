
   

    // Función para renderizar la tabla
    function renderTable(filteredLibros, page) {
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedLibros = filteredLibros.slice(start, end);

        const librosBody = document.getElementById('librosBody');
        librosBody.innerHTML = '';

        paginatedLibros.forEach(libro => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${libro.titulo}</td>
                <td>${libro.anio}</td>
                <td>${libro.categoria}</td>
                <!--td>${libro.extension}</td-->
                <td>
                <a href="index.php?vista=fichaLibro&isbn=${libro.isbn}">
                        <button class="btn" style="color: white; background-color: #2c3e50; ">
                        <i class="bi bi-card-heading"></i>
                        </button>
                        </a>
                </td>
            `;
            librosBody.appendChild(row);
        });

        renderPagination(filteredLibros.length, page);
    }

    // Función para renderizar la paginación
    function renderPagination(totalItems, currentPage) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const button = document.createElement('button');
            button.innerText = i;
            button.addEventListener('click', () => {
                currentPage = i;
                renderTable(filteredLibros, currentPage);
            });
            if (i === currentPage) {
                button.classList.add('active');
            }
            pagination.appendChild(button);
        }
    }

    // Función para filtrar libros
    function filterLibros(query) {
        return libros.filter(libro => libro.titulo.toLowerCase().includes(query.toLowerCase()));
    }

    // Evento de búsqueda
    document.getElementById('searchInput').addEventListener('input', (e) => {
        const query = e.target.value;
        filteredLibros = filterLibros(query);
        currentPage = 1;
        renderTable(filteredLibros, currentPage);
    });

    // Inicialización
    let filteredLibros = libros;
    renderTable(filteredLibros, currentPage);
