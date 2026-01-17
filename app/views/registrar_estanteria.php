<?php
require_once "app/config/database.php";
$conexion = conexion();
?>

<br><br>

<div class="estanteria-registro-container">
    <div class="estanteria-header">
        <h1><i class="fas fa-bookshelf"></i> Registrar Nueva Estantería</h1>
        <p class="subtitle">Complete todos los campos para configurar una nueva estantería en el sistema</p>
    </div>

    <form action="app/controller/estanterias/c_registrar_estanteria.php" method="POST">

    <div class="form-columns-container">
        <div class="form-column">
            <div class="form-section">
                <h2 class="section-title"><span>1</span> Información Básica</h2>
                <div class="form-group">
                    <label for="nombre-estanteria">Nombre descriptivo*</label>
                    <input type="text" name="nombre-estanteria" id="nombre-estanteria" required placeholder="Ej: Estantería Principal de Ciencias">
                    <small class="error-message" id="nombre-error">Un nombre claro que identifique esta estantería</small>
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title"><span>2</span> Configuración de Ubicación</h2>
                <div class="form-group">
                    <label for="codigo-estanteria">Código de ubicación* 
                        <button type="button" class="info-btn" id="btn-explicacion-codigo">
                            <i class="fas fa-question-circle"></i> ¿Cómo crear el código?
                        </button>
                    </label>
                    <input type="text" id="codigo-estanteria" name="codigo-estanteria" required 
                           pattern="^[A-Z0-9]{1,5}-[A-Z0-9]{1,5}-[A-Z0-9]{1,5}$"
                           placeholder="Ej: E1-A-03">
                    <small class="error-message" id="codigo-error">Formato requerido: AAA-111-222 (letras/números)</small>
                    
                    <div id="explicacion-codigo" class="help-acordeon" style="display:none;">
                        <div class="acordeon-content">
                            <h4><i class="fas fa-map-marked-alt"></i> Sistema de Codificación de Ubicaciones</h4>
                            <p>El código identifica exactamente dónde se ubicará físicamente la estantería:</p>
                            
                            <div class="codigo-visual">
                                <div class="codigo-part" data-part="mueble">
                                    <span>MUEBLE</span>
                                    <div class="tooltip">Ej: "E1"=Estantería 1, "A3"=Archivador 3</div>
                                </div>
                                <div class="codigo-separator">-</div>
                                <div class="codigo-part" data-part="seccion">
                                    <span>SECCIÓN</span>
                                    <div class="tooltip">Letra (A, B, C...) o número</div>
                                </div>
                                <div class="codigo-separator">-</div>
                                <div class="codigo-part" data-part="posicion">
                                    <span>POSICIÓN</span>
                                    <div class="tooltip">Número correlativo</div>
                                </div>
                            </div>
                            
                            <div class="codigo-examples">
                                <div class="example-card">
                                    <h5>Ejemplo básico:</h5>
                                    <code>E1-A-03</code>
                                    <p>Estantería 1, Sección A, Posición 3</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title"><span>3</span> Configuración de Filas</h2>
                <div class="form-group">
                    <label for="num-filas">Número de filas/niveles*</label>
                    <div class="range-slider-container">
                        <input type="range" name="num-filas" id="num-filas" min="1" max="10" value="4" class="slider">
                        <span id="num-filas-value">4</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="capacidad-fila">Capacidad por fila (libros)*</label>
                    <input type="number" name="capacidad-fila" id="capacidad-fila" min="10" max="200" value="30" required>
                    <small class="error-message" id="capacidad-error">Debe ser entre 10 y 200 libros</small>
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title"><span>4</span> Clasificación Dewey</h2>
                
                <div class="clasificacion-options">
                    <div class="option-card" id="option-misma-clasificacion">
                        <input type="radio" name="tipo-clasificacion" id="misma-clasificacion" value="misma" checked>
                        <label for="misma-clasificacion">
                            <i class="fas fa-layer-group"></i>
                            <h3>Todas las filas con la misma clasificación</h3>
                            <p>Seleccione una categoría Dewey que aplique a todos los libros</p>
                        </label>
                    </div>
                    
                    <div class="option-card" id="option-diferente-clasificacion">
                        <input type="radio" name="tipo-clasificacion" id="diferente-clasificacion" value="diferente">
                        <label for="diferente-clasificacion">
                            <i class="fas fa-sliders-h"></i>
                            <h3>Diferente clasificación por fila</h3>
                            <p>Configure categorías Dewey específicas para cada fila/nivel</p>
                        </label>
                    </div>
                </div>
                
                <div id="clasificacion-misma-container" class="clasificacion-container">
                    <div class="form-group">
                        <label for="clasificacion-general">Categoría Dewey*</label>
                        <div class="dewey-select-container">
                            <select id="clasificacion-general" name="clasificacion-general" class="dewey-select" required>
                                <option value="">-- Seleccione categoría --</option>
                               <?php
                               
                               $sql = "SELECT * FROM dewey";

                               $result = $conexion->query($sql);
    


                               while($row = $result->fetch_assoc()) {
                                echo "<option value='".$row["DeweyID"]."'>".$row["Codigo"]." - ". $row["Descripcion"] ."</option>";
                               }

                               ?>
                            </select>
                            <div class="dewey-preview">
                                <span id="dewey-preview-text">Ninguna categoría seleccionada</span>
                            </div>
                        </div>
                        <small class="error-message" id="dewey-error">Seleccione una categoría Dewey</small>
                    </div>
                </div>
                
                <div id="clasificacion-diferente-container" class="clasificacion-container" style="display: none;">
                    <div class="filas-config-container">
                        <div class="filas-grid" id="filas-grid">
                            <!-- Filas se generarán dinámicamente aquí -->
                        </div>
                        <small class="error-message" id="filas-error" style="display:none;">Asigne clasificación a todas las filas</small>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-secondary" id="btn-cancelar">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn-primary" id="btn-guardar" disabled>
                    <i class="fas fa-save"></i> Guardar Estantería
                </button>
            </div>
        </div>

        <div class="preview-column">
            <div class="preview-section">
                <h2 class="section-title"><span>5</span> Vista Previa</h2>
                <div class="preview-container">
                    <div class="estanteria-preview" id="estanteria-preview">
                        <div class="preview-header">
                            <h3 id="preview-nombre">Estantería Principal de Ciencias</h3>
                            <div class="preview-codigo" id="preview-codigo">E1-A-01</div>
                        </div>
                        <div class="preview-filas" id="preview-filas">
                            <!-- Filas de preview se generarán aquí -->
                        </div>
                        <div class="preview-footer">
                            <div class="preview-info">
                                <span><i class="fas fa-archive"></i> <span id="preview-total-libros">120</span> libros</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </form>

</div>

<link rel="stylesheet" href="public/css/registrarEstanteria.css">

<script src="public/js/registrarEstanteria.js"></script>




<!--select id="clasificacion-general" class="dewey-select" required>
                                <option value="">-- Seleccione categoría --</option>
                                <optgroup label="Generalidades">
                                    <option value="000">000 - Obras generales</option>
                                    <option value="001">001 - Conocimiento</option>
                                    <option value="002">002 - El libro</option>
                                    <option value="003">003 - Sistemas</option>
                                    <option value="004">004 - Procesamiento de datos</option>
                                    <option value="005">005 - Programación</option>
                                    <option value="006">006 - Métodos especiales</option>
                                    <option value="007">007 - [Sin asignar]</option>
                                    <option value="008">008 - [Sin asignar]</option>
                                    <option value="009">009 - [Sin asignar]</option>
                                </optgroup>
                                <optgroup label="Filosofía y Psicología">
                                    <option value="100">100 - Filosofía</option>
                                    <option value="110">110 - Metafísica</option>
                                    <option value="120">120 - Epistemología</option>
                                    <option value="130">130 - Parapsicología</option>
                                    <option value="140">140 - Escuelas filosóficas</option>
                                    <option value="150">150 - Psicología</option>
                                    <option value="160">160 - Lógica</option>
                                    <option value="170">170 - Ética</option>
                                    <option value="180">180 - Filosofía antigua</option>
                                    <option value="190">190 - Filosofía moderna</option>
                                </optgroup>
                                <optgroup label="Religión">
                                    <option value="200">200 - Religión</option>
                                    <option value="210">210 - Filosofía religiosa</option>
                                    <option value="220">220 - Biblia</option>
                                    <option value="230">230 - Teología cristiana</option>
                                    <option value="240">240 - Moral cristiana</option>
                                    <option value="250">250 - Iglesia cristiana</option>
                                    <option value="260">260 - Teología social cristiana</option>
                                    <option value="270">270 - Historia del cristianismo</option>
                                    <option value="280">280 - Denominaciones cristianas</option>
                                    <option value="290">290 - Otras religiones</option>
                                </optgroup>
                                <optgroup label="Ciencias Sociales">
                                    <option value="300">300 - Ciencias sociales</option>
                                    <option value="310">310 - Estadística</option>
                                    <option value="320">320 - Ciencia política</option>
                                    <option value="330">330 - Economía</option>
                                    <option value="340">340 - Derecho</option>
                                    <option value="350">350 - Administración pública</option>
                                    <option value="360">360 - Servicios sociales</option>
                                    <option value="370">370 - Educación</option>
                                    <option value="380">380 - Comercio</option>
                                    <option value="390">390 - Costumbres y folklore</option>
                                </optgroup>
                                <optgroup label="Lenguaje">
                                    <option value="400">400 - Lenguaje</option>
                                    <option value="410">410 - Lingüística</option>
                                    <option value="420">420 - Inglés</option>
                                    <option value="430">430 - Alemán</option>
                                    <option value="440">440 - Francés</option>
                                    <option value="450">450 - Italiano</option>
                                    <option value="460">460 - Español</option>
                                    <option value="470">470 - Latín</option>
                                    <option value="480">480 - Griego</option>
                                    <option value="490">490 - Otros idiomas</option>
                                </optgroup>
                                <optgroup label="Ciencias Naturales y Matemáticas">
                                    <option value="500">500 - Ciencias naturales</option>
                                    <option value="510">510 - Matemáticas</option>
                                    <option value="520">520 - Astronomía</option>
                                    <option value="530">530 - Física</option>
                                    <option value="540">540 - Química</option>
                                    <option value="550">550 - Ciencias de la Tierra</option>
                                    <option value="560">560 - Paleontología</option>
                                    <option value="570">570 - Ciencias de la vida</option>
                                    <option value="580">580 - Plantas</option>
                                    <option value="590">590 - Animales</option>
                                </optgroup>
                                <optgroup label="Tecnología (Ciencias Aplicadas)">
                                    <option value="600">600 - Tecnología</option>
                                    <option value="610">610 - Medicina</option>
                                    <option value="620">620 - Ingeniería</option>
                                    <option value="630">630 - Agricultura</option>
                                    <option value="640">640 - Hogar y familia</option>
                                    <option value="650">650 - Administración</option>
                                    <option value="660">660 - Ingeniería química</option>
                                    <option value="670">670 - Manufacturas</option>
                                    <option value="680">680 - Manufactura para usos específicos</option>
                                    <option value="690">690 - Construcción</option>
                                </optgroup>
                                <optgroup label="Arte y Recreación">
                                    <option value="700">700 - Artes</option>
                                    <option value="710">710 - Urbanismo</option>
                                    <option value="720">720 - Arquitectura</option>
                                    <option value="730">730 - Escultura</option>
                                    <option value="740">740 - Dibujo</option>
                                    <option value="750">750 - Pintura</option>
                                    <option value="760">760 - Grabados</option>
                                    <option value="770">770 - Fotografía</option>
                                    <option value="780">780 - Música</option>
                                    <option value="790">790 - Entretenimiento</option>
                                </optgroup>
                                <optgroup label="Literatura">
                                    <option value="800">800 - Literatura</option>
                                    <option value="810">810 - Literatura americana</option>
                                    <option value="820">820 - Literatura inglesa</option>
                                    <option value="830">830 - Literatura alemana</option>
                                    <option value="840">840 - Literatura francesa</option>
                                    <option value="850">850 - Literatura italiana</option>
                                    <option value="860">860 - Literatura española</option>
                                    <option value="870">870 - Literatura latina</option>
                                    <option value="880">880 - Literatura griega</option>
                                    <option value="890">890 - Otras literaturas</option>
                                </optgroup>
                                <optgroup label="Historia y Geografía">
                                    <option value="900">900 - Historia</option>
                                    <option value="910">910 - Geografía</option>
                                    <option value="920">920 - Biografía</option>
                                    <option value="930">930 - Historia antigua</option>
                                    <option value="940">940 - Historia de Europa</option>
                                    <option value="950">950 - Historia de Asia</option>
                                    <option value="960">960 - Historia de África</option>
                                    <option value="970">970 - Historia de América del Norte</option>
                                    <option value="980">980 - Historia de América del Sur</option>
                                    <option value="990">990 - Historia de otras áreas</option>
                                </optgroup>
                            </select-->