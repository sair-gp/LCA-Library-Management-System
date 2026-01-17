// Script para manejar las pestañas
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remover clase active de todos los botones y paneles
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
            
            // Agregar clase active al botón clickeado
            this.classList.add('active');
            
            // Mostrar el panel correspondiente
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });
});


// script para colocar los datos del lector en el modal de deshabilitar
/*
$(document).on('click', "#btnDeshabilitarVisitante", () => {
    $("#nombreAqui").text($('.perfil-nombre').text());
    $("#cedulaAqui").text($('.perfil-cedula').text());
});*/