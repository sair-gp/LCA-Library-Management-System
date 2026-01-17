var cedulaVisitante = document.getElementById("validarCedulaBD"); 

cedulaVisitante.addEventListener("input", () => {
    fetch("public/js/ajax/validarCamposUnicos.php", {
    method: "POST",
    headers: {"Content-type" : "application/json"},
    body: JSON.stringify({validarCedulaVisitante: cedulaVisitante.value})
}).then(response => {
    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }
    return response.json(); // Procesar la respuesta JSON
}).then(data => {
    console.log(data.message);
    crearSpan(cedulaVisitante, cedulaVisitante.nextElementSibling, data.message, data.class, "");
    if (cedulaVisitante.value == ""){
    crearSpan(cedulaVisitante, cedulaVisitante.nextElementSibling, "", "", true);
    }

    var botonEF = document.getElementById("botonEF")

    data.class == "red" ? botonEF.disabled = true : botonEF.disabled = false;

}).catch(error => {
    console.error("Error al validar la cédula:", error); // Manejo de errores
    // Puedes mostrar un mensaje de error al usuario aquí
});;

});

