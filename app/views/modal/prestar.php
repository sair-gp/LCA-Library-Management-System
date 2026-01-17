<!-- Button trigger modal -->


<!-- Modal -->
<style>
    .formPrestamo {

        padding: 20px;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .formPrestamo label {
        font-weight: bold;
    }

    .form-control {
        width: 100%;
        padding: 8px;
        margin: 5px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: #007bff;
    }

    .formPrestamo select#lector,
    .formPrestamo select#cota {
        width: 425px;
    }

    .formPrestamo .btn {
        padding: 8px 20px;
        margin-top: 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .formPrestamo .btn-success {
        background-color: #28a745;
        color: #fff;
    }

    .formPrestamo .btn-danger {
        background-color: #dc3545;
        color: #fff;
    }







    /* estilo select 2 */

    /* Contenedor del select simple */
    #cota+.select2-container .select2-selection--single,
    #lector+.select2-container .select2-selection--single {
        height: 40px;
        /* Altura consistente */
        display: flex;
        align-items: center;
        /* Centrado vertical */
        padding: 5px 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: none;
        background-color: #f9f9f9;
        transition: border-color 0.3s ease;
    }

    #cota+.select2-container .select2-selection--single:hover,
    #lector+.select2-container .select2-selection--single:hover {
        border-color: #007bff;
        /* Efecto hover */
    }

    #cota+.select2-container .select2-selection__rendered,
    #cota+.select2-container .select2-selection__rendered {
        line-height: 1.5;
        color: #333;
    }
</style>



<div class="modal fade" id="registrarPrestamo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Registrar Prestamo</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form action="app/controller/prestamos/c_prestamos.php" method="POST" id="formulario"
                    class="formPrestamo">

                    <div class="col-12">


                        <div class="form-group">
                        <label for="cota" class="libro-registro-label">Cota</label>
                        <select name="idEjemplar" id="cota" class="prestaInput" required>
                            <option value="">Escriba la cota del libro a prestar.</option>
                        </select>
                        </div>
                        
                        
                        <!--input type="hidden" name="idEjemplar" id="idEjemplar" class="form-control prestaInput"-->
                        <div class="form-group">
                        <label for="lector">Lector</label>
                        <select name="lector" id="lector" class="prestaInput" aria-placeholder="Escriba la cédula del lector.">
                            

                        </select>
                        </div>
                        
                        <div class="form-group" id="divFechaInput" style="display: none;">
                        <label for="fecha_fin">Fecha de devolucion</label>
                        <input type="date" name="fecha_fin" id="fecha_fin"
                            class="form-control fechaForm prestaInput"><br>
                        </div>


                    </div>



                    <br>
                    <div class="modal-footer">
                        <button type="submit" id="botonEF" class="btn btn-success">Agregar</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>

            </div>


            </form>
        </div>
    </div>
</div>
<!--script src="node_modules/select2/dist/js/select2.min.js"></script-->
<script>
/*
console.log(document.getElementById("lector").value)

function calcularNewFecha(fecha, unidades, cantidad) {
  let nuevaFecha = new Date(fecha);
  switch (unidades) {
      case "días":
      case "dias":
          nuevaFecha.setDate(nuevaFecha.getDate() + cantidad);
          break;
      case "semanas":
          nuevaFecha.setDate(nuevaFecha.getDate() + (cantidad * 7));
          break;
      case "meses":
          nuevaFecha.setMonth(nuevaFecha.getMonth() + cantidad);
          break;
      default:
          console.warn("Unidad de tiempo no válida");
  }
  return nuevaFecha;
}
    
    function validarFecha() {

        
  
  const fechaSeleccionada = new Date(inputFecha.value);
  fechaSeleccionada.setDate(fechaSeleccionada.getDate() + 1);

  const fechaActual = new Date();
  //fechaActual.setDate(fechaActual.getDate() - 1);
  console.log(fechaActual);
  //console.log(fechaSeleccionada);
  console.log(fechaSeleccionada);


  fetch("public/js/ajax/obtener_regla_circulacion.php", {
            method: "POST",
            headers: {"Content-type": "application/json"},
            body: JSON.stringify({cedulaPrestamo: document.getElementById("lector").value})
        })
        .then(response => response.json())
        .then(data => {
            let unidades = data.unidades.toLowerCase();
            let periodoPrestamo = parseInt(data.periodo_prestamo, 10);

            const nuevaFecha = calcularNewFecha(fechaActual, unidadades, periodoPrestamo);

            // Formatea la nueva fecha a 'yyyy-MM-dd'
            const anio = nuevaFecha.getFullYear();
            const mes = String(nuevaFecha.getMonth() + 1).padStart(2, "0");
            const dia = String(nuevaFecha.getDate()).padStart(2, "0");
            const fechaFormateada = `${anio}-${mes}-${dia}`;

            crearSpan(
                spanAdvertenciaFecha,
                spanAdvertenciaFecha.nextElementSibling,
                `El usuario puede solicitar un préstamo por un tiempo máximo de ${periodoPrestamo} ${unidades}.`,
                "green",
                false,
                true
            );

            inputFecha.value = fechaFormateada;


        });


  

  if (fechaSeleccionada >= fechaActual && fechaSeleccionada <= fechaFormateada) {
    checkerFecha = true;
    crearSpan(
      inputFecha,
      inputFecha.nextElementSibling,
      "Fecha válida.",
      "green"
    );

    console.log("Fecha válida.");
    // Aquí puedes realizar alguna acción si la fecha es válida, como enviar un formulario
  } else {
    checkerFecha = false;
    crearSpan(
      inputFecha,
      inputFecha.nextElementSibling,
      `Fecha inválida. Debe ser mayor o igual a la fecha actual y no mayor que ${periodoPrestamo} ${unidad}.`,
      "red"
    );
    console.log(
      `Fecha inválida. Debe ser mayor o igual a la fecha actual y no mayor que ${periodoPrestamo} ${unidad}.`
    );
    // Aquí puedes mostrar un mensaje de error al usuario
  }
}
*/






</script>
<script src="public/js/obtenerIdEjemplar.js"></script>