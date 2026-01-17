

function buscarLibro(termino){
    let contentTable = document.getElementById('tablaLibros');

    fetch('public/js/ajax/buscarLibro.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ termino: termino })
    }).then(response => {
        if (!response.ok) {
            throw new Error('No respondio la red');
        }
        return response.json();
    }).then(data => {
        console.log('El servidor respondio', data);
        contentTable.innerHTML = data;
    }).catch(error => {
        console.error('error ', error);
    });
}

let inputTermino = document.getElementById("inputTermino");

inputTermino.addEventListener("keypress", function () {
    buscarLibro(inputTermino.value);
})

inputTermino.addEventListener("keyup", function (event) {
    // Prevent default action if the key pressed is Enter
    if (event.key === "Enter") {
        event.preventDefault();
    }

    buscarLibro(inputTermino.value);
});