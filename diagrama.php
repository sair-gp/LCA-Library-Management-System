<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagrama de Clases con Relaciones</title>
    <script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        mermaid.initialize({ startOnLoad: true });
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            overflow: hidden;
        }
        .mermaid-container {
            width: 100%;
            height: 80vh;
            overflow: auto;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
            position: relative;
        }
        .mermaid {
            padding: 20px;
            transform-origin: top left;
            transform: scale(1.5); /* Aumenta el tamaño inicial */
        }
        .mermaid .classTitle {
            font-size: 18px !important; /* Aumenta el tamaño de la fuente del título */
        }
        .mermaid .classText {
            font-size: 14px !important; /* Aumenta el tamaño de la fuente del contenido */
        }
        .mermaid .relation {
            font-size: 14px !important; /* Aumenta el tamaño de la fuente de las relaciones */
        }
        .mermaid .classBox {
            margin: 5px !important; /* Reduce aún más el espacio entre las tablas */
            padding: 5px !important; /* Reduce el padding interno */
        }
        .zoom-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        .zoom-controls button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            cursor: pointer;
        }
        .zoom-controls button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Diagrama de Clases con Relaciones</h1>
    <div class="zoom-controls">
        <button onclick="zoomIn()">+</button>
        <button onclick="zoomOut()">-</button>
        <button onclick="resetZoom()">Reset</button>
        <button onclick="exportToPDF()">Exportar a PDF</button>
    </div>
    <div class="mermaid-container">
        <div class="mermaid">
            classDiagram
                %% Definición de clases
                class actividades {
                    +registrarActividad()
                    +suspenderActividad()
                    +reprogramarActividad()
                    +obtenerActividadesActivas() (recomendación)
                    -actividadesActivas (recomendación)
                }

                class BibliotecaAnalisis {
                    -datosActuales
                    -datosAnteriores
                    +__construct()
                    -calcularVariacion()
                    +analizarPrestamos()
                    +analizarDevoluciones()
                    +analizarDesincorporaciones()
                    +analizarActividades()
                    +analizarAsistencias()
                    +analizarSuministroLibros()
                    +analizarLibrosAgregados()
                    +generarAnalisisCompleto()
                    +exportarReporte() (recomendación)
                }

                class asistencias {
                    +registrarAsistencia()
                    +generarReporteAsistencias() (recomendación)
                }

                class Autores {
                    +agregarAutores()
                    +listarAutores() (recomendación)
                }

                class selects {
                    +select_dinamico()
                }

                class categorias {
                    +agregarCategoria()
                    +listarCategorias() (recomendación)
                }

                class check {
                    +generarCheckboxesPermisos()
                    +generarCheckboxesPermisosUsuario()
                    +actualizarPermisosUsuario()
                    +cBoxPermisoPorDefecto()
                    +validarPermisos() (recomendación)
                }

                class editorial {
                    +agregarEditorial()
                    +listarEditoriales() (recomendación)
                }

                class Ejemplares {
                    +mostrar_ejemplares()
                    +agregar_ejemplares()
                    +eliminarEjemplar() (recomendación)
                }

                class Historial {
                    -conexion
                    +__construct()
                    +MostrarHistorial()
                    +registrar_accion()
                    +filtrarPorFechas()
                    +filtrarPorAccion()
                    +filtrarPorResponsable()
                    +exportarHistorial() (recomendación)
                }

                class imprimirRegistros {
                    +obtenerPaginacion()
                    +generarPaginacion()
                }

                class Libros {
                    +registros_por_pagina
                    +consultaCount
                    +consultaPaginacion
                    +RegistrarLibro()
                    +ejecutarConsulta()
                    -RegistrarLibroVolumenEjemplarOg()
                    +RegistrarLibroVolumenEjemplar()
                    +eliminar_libro()
                    +retornar_libro()
                    +obtenerPaginacion()
                    +generarPaginacion()
                    +buscarLibroPorTitulo() (recomendación)
                }

                class prestamos {
                    +registrarPrestamo()
                    +devolverPrestamo()
                    +renovarPrestamo()
                    +generarReportePrestamos() (recomendación)
                }

                class ReporteGeneral {
                    -prestamos
                    -devoluciones
                    -actividades
                    -librosMasSolicitados
                    -visitasPorHorario
                    -sanciones
                    -conexion
                    -tipoDeReporte
                    +__construct()
                    +obtenerDatosParaReporte()
                    +exportarReporte() (recomendación)
                }

                class UserSession {
                    +constructor()
                    +setCurrentUser()
                    +getCurrentUser()
                    +closeSession()
                    +validarSesion() (recomendación)
                }

                class User {
                    +userExists()
                    +addUser()
                    +registrar_accion()
                    +setUser()
                    +getNombre()
                    +cambiarContrasena() (recomendación)
                }

                class visitantes {
                    +agregarVisitante()
                    +generarReporteVisitantes() (recomendación)
                }

                class Volumenes {
                    +registros_por_pagina
                    +consultaCount
                    +consultaPaginacion
                    +RegistrarLibro()
                    +ejecutarConsulta()
                    -RegistrarLibroVolumenEjemplarOg()
                    +RegistrarLibroVolumenEjemplar()
                    +eliminar_libro()
                    +retornar_libro()
                    +obtenerPaginacion()
                    +generarPaginacion()
                    +buscarVolumenPorTitulo() (recomendación)
                }

                %% Relaciones de herencia
                imprimirRegistros <|-- Libros
                Libros <|-- Volumenes

                %% Relaciones entre clases con cardinalidades personalizadas
                actividades "1" -- "1..N" ReporteGeneral : genera >
                asistencias "1" -- "1..N" ReporteGeneral : genera >
                prestamos "1" -- "1..N" ReporteGeneral : genera >
                Libros "1" -- "N" Ejemplares : contiene >
                Libros "1" -- "N" Autores : tiene >
                Libros "1" -- "1" categorias : pertenece a >
                Libros "1" -- "1" editorial : publicado por >
                User "1" -- "N" prestamos : realiza >
                User "1" -- "N" actividades : participa en >
                User "1" -- "N" asistencias : registra >
                User "1" -- "1" UserSession : inicia >
                visitantes "1" -- "N" asistencias : registra >
                check "1" -- "N" User : gestiona permisos >
                Historial "1" -- "N" User : registra acciones >
                selects "1" -- "N" Libros : filtra >
        </div>
    </div>

    <script>
        let scale = 1.5; // Escala inicial

        function zoomIn() {
            scale += 0.1;
            updateZoom();
        }

        function zoomOut() {
            scale -= 0.1;
            if (scale < 0.5) scale = 0.5; // Límite mínimo de zoom
            updateZoom();
        }

        function resetZoom() {
            scale = 1.5;
            updateZoom();
        }

        function updateZoom() {
            const diagram = document.querySelector('.mermaid');
            diagram.style.transform = `scale(${scale})`;
        }

        async function exportToPDF() {
            const diagramContainer = document.querySelector('.mermaid');
            const pdf = new jspdf.jsPDF('landscape'); // Orientación horizontal

            // Configuración para alta resolución (x4)
            const options = {
                scale: 4, // Aumenta la escala a 4x
                useCORS: true, // Permite el uso de recursos externos
                logging: true, // Habilita logs para depuración
            };

            // Captura el contenido del diagrama
            const canvas = await html2canvas(diagramContainer, options);

            // Ajusta el tamaño de la imagen al PDF
            const imgWidth = pdf.internal.pageSize.getWidth();
            const imgHeight = (canvas.height * imgWidth) / canvas.width;

            // Agrega la imagen al PDF
            pdf.addImage(canvas.toDataURL('image/png'), 'PNG', 0, 0, imgWidth, imgHeight);

            // Descarga el PDF
            pdf.save('diagrama.pdf');
        }
    </script>
</body>
</html>