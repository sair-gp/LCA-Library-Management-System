<?php
require_once "app/config/database.php";
require_once "app/controller/prestamos/cargar_multas.php";



?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multas - Biblioteca</title>
    <link rel="stylesheet" href="public/css/multas.css">
    <!-- Bootstrap CSS ya incluido -->
</head>
<body>
    <div class="container fade-in">
        <h1>Multas</h1>
       
        <!-- Filtros y búsqueda -->
        <div class="filtros">
            <input type="text" id="busqueda" style="width: 35%;" placeholder="Buscar por nombre, motivo, monto, fecha o estado..." oninput="filtrarMultas()">
            <select id="filtroEstado" onchange="filtrarMultas()">
                <option value="">Todos</option>
                <option value="pendiente">Pendiente</option>
                <option value="pagada">Pagada</option>
            </select>
        </div>
        <div class="multas-grid">
            <!-- Tarjetas de multas se generarán dinámicamente -->
        </div>
        <!-- Mensaje cuando no hay resultados -->
        <div id="mensajeNoResultados" style="display: none; text-align: center; margin-top: 20px; font-size: 18px; color: #888;">
            No se encontraron resultados.
        </div>
        <!-- Paginación -->
        <div class="paginacion">
            <button onclick="cambiarPagina(-1)">Anterior</button>
            <span id="paginaActual">1</span>
            <button onclick="cambiarPagina(1)">Siguiente</button>
        </div>
    </div>

    <?php include_once "modal/modalMultas.php"; ?>

    <!-- Bootstrap JS ya incluido -->
    <script>
        // Datos estáticos de multas
        const multas = <?php echo json_encode($multas); ?>;

        let paginaActual = 1;
        const multasPorPagina = 6; // Mostramos 6 multas por página

        // Función para mostrar multas
        function mostrarMultas(multasMostradas = multas) {
            const multasGrid = document.querySelector('.multas-grid');
            const mensajeNoResultados = document.getElementById('mensajeNoResultados');

            multasGrid.innerHTML = '';

            const inicio = (paginaActual - 1) * multasPorPagina;
            const fin = inicio + multasPorPagina;
            const multasPagina = multasMostradas.slice(inicio, fin);

            if (multasPagina.length === 0) {
                mensajeNoResultados.style.display = 'block'; // Mostrar mensaje si no hay resultados
            } else {
                mensajeNoResultados.style.display = 'none'; // Ocultar mensaje si hay resultados
                multasPagina.forEach((multa, index) => {
                    const multaCard = document.createElement('div');
                    var montoPago = multa.montoPagado > 0 && multa.estado === "pagada" ? `<p><strong>Monto:</strong> Bs.${multa.montoPagado}</p>` : `<p><strong>Monto:</strong> Bs.${multa.monto.toFixed(2)}</p>`;
                    console.log(multa.montoPagado);
                    multaCard.className = 'multa-card';
                    multaCard.innerHTML = `
                        <h3>${multa.nombre}</h3>
                        <p><strong></strong>#${multa.idMulta}</p>
                        <p><strong>Motivo:</strong> ${multa.motivo}</p>
                        ${montoPago}
                        <p><strong>Fecha de Multa:</strong> ${multa.fechaMulta}</p>
                        <!--p><strong>Fecha de Fin:</strong> ${multa.fechaFin}</p-->
                        <p><strong>Días de Retraso:</strong> ${multa.diasRetraso}</p>
                        <div class="estado ${multa.estado}" id="estadoMulta${multa.idMulta}">${multa.estado.charAt(0).toUpperCase() + multa.estado.slice(1)}</div>
                        <div class="acciones">
                            ${multa.estado === 'pendiente' ? `<button class="btn btn-pagar" id="pagar${multa.idMulta}" data-bs-toggle="modal" data-bs-target="#modalPagar" onclick="document.getElementById('modalUsuario').textContent = '${multa.nombre}';
                            document.getElementById('montoInput').value = '${multa.monto.toFixed(2)}';
                            document.getElementById('idMulta').value = '${multa.idMulta}';
                            document.getElementById('modalMonto').textContent = 'Bs.${multa.monto.toFixed(2)}';">Pagar</button>` : ''}
                        </div>
                    `;
                    multasGrid.appendChild(multaCard);
                });
            }

            document.getElementById('paginaActual').textContent = paginaActual;
        }

        // Función para cambiar de página
        function cambiarPagina(direccion) {
            paginaActual += direccion;
            if (paginaActual < 1) paginaActual = 1;
            if (paginaActual > Math.ceil(multas.length / multasPorPagina)) paginaActual = Math.ceil(multas.length / multasPorPagina);
            mostrarMultas();
        }

        // Función para filtrar multas en tiempo real
        function filtrarMultas() {
            const busqueda = document.getElementById('busqueda').value.toLowerCase();
            const filtroEstado = document.getElementById('filtroEstado').value;

            const multasFiltradas = multas.filter(multa => {
                const coincideNombre = multa.nombre.toLowerCase().includes(busqueda);
                const coincideMotivo = multa.motivo.toLowerCase().includes(busqueda);
                const coincideMonto = multa.monto.toString().includes(busqueda); // Buscar por monto
                const coincideFechaMulta = multa.fechaMulta.includes(busqueda);
                const coincideFechaFin = multa.fechaFin.includes(busqueda);
                const coincideDiasRetraso = multa.diasRetraso.toString().includes(busqueda); // Buscar por días de retraso
                const coincideEstado = multa.estado.toLowerCase().includes(busqueda); // Buscar por estado
                const coincideFiltroEstado = filtroEstado ? multa.estado === filtroEstado : true;

                return (coincideNombre || coincideMotivo || coincideMonto || coincideFechaMulta || coincideFechaFin || coincideDiasRetraso || coincideEstado) && coincideFiltroEstado;
            });

            mostrarMultas(multasFiltradas);
        }

        // Mostrar multas al cargar la página
        mostrarMultas();
    </script>
</body>
</html>