/**
 * SISTEMA DE NOTIFICACIONES TOAST
 * 
 * Este código crea notificaciones emergentes (toasts) que se pueden usar:
 * 1. Desde JavaScript directamente: Toast.success("Mensaje")
 * 2. Desde la URL: index.php?toast=success&mensaje=Texto
 * 
 * Tipos disponibles: success, error, warning, info
 */

// Creamos un módulo auto-ejecutable para evitar variables globales
const Toast = (() => {
    // =============================================
    // CONFIGURACIÓN DEL SISTEMA
    // =============================================
    const settings = {
        defaultDuration: 5000, // 5 segundos por defecto
        icons: {
            // Iconos usando Bootstrap Icons con colores
            success: '<i class="ds-toast-icon bi bi-check-circle-fill" style="color:#4CAF50;"></i>',
            error: '<i class="ds-toast-icon bi bi-x-circle-fill" style="color:#F44336;"></i>',
            warning: '<i class="ds-toast-icon bi bi-exclamation-triangle-fill" style="color:#FF9800;"></i>',
            info: '<i class="ds-toast-icon bi bi-info-circle-fill" style="color:#2196F3;"></i>'
        }
    };

    // =============================================
    // FUNCIÓN PARA CREAR EL CONTENEDOR DE NOTIFICACIONES
    // =============================================
    function initContainer() {
        // Buscamos si ya existe un contenedor
        let container = document.querySelector('.ds-toast-container');
        
        // Si no existe, lo creamos
        if (!container) {
            container = document.createElement('div');
            container.className = 'ds-toast-container';
            document.body.appendChild(container); // Lo añadimos al final del body
        }
        
        return container;
    }

    // =============================================
    // FUNCIÓN PRINCIPAL PARA MOSTRAR NOTIFICACIONES
    // =============================================
    function show(message, type = 'success', duration = settings.defaultDuration) {
        // 1. Obtenemos el contenedor (lo crea si no existe)
        const container = initContainer();
        
        // 2. Creamos el elemento de la notificación
        const toast = document.createElement('div');
        toast.className = `ds-toast ds-toast--${type}`;
        
        // 3. Definimos el contenido HTML del toast
        toast.innerHTML = `
            ${settings.icons[type] || settings.icons.success} <!-- Icono -->
            <div class="ds-toast-content">${message}</div> <!-- Mensaje -->
            <div class="ds-toast-progress" style="animation-duration: ${duration/1000}s"></div> <!-- Barra de progreso -->
        `;

        // 4. Añadimos el toast al contenedor
        container.appendChild(toast);

        // 5. Programamos la eliminación automática después de la duración especificada
        setTimeout(() => {
            // Aplicamos animación de salida
            toast.style.animation = 'ds-toast-slide-out 0.5s forwards';
            
            // Eliminamos el toast cuando termine la animación
            toast.addEventListener('animationend', () => toast.remove());
        }, duration);
    }

    // =============================================
    // INTERFAZ PÚBLICA - MÉTODOS DISPONIBLES
    // =============================================
    return {
        // Métodos específicos para cada tipo
        success: (msg, duration) => show(msg, 'success', duration),
        error: (msg, duration) => show(msg, 'error', duration),
        warning: (msg, duration) => show(msg, 'warning', duration),
        info: (msg, duration) => show(msg, 'info', duration),
        
        // Método genérico para tipos personalizados
        show: show
    };
})();

// =============================================
// MANEJADOR DE NOTIFICACIONES DESDE LA URL
// =============================================
document.addEventListener('DOMContentLoaded', function() {
    // 1. Obtenemos los parámetros de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const toastType = urlParams.get('toast'); // Ej: 'success'
    const toastMessage = urlParams.get('mensaje'); // Ej: 'Operación exitosa'
    
    // 2. Verificamos si hay parámetros de toast en la URL
    if (toastType && toastMessage) {
        // 3. Decodificamos el mensaje (para manejar espacios y caracteres especiales)
        const decodedMessage = decodeURIComponent(toastMessage);
        
        // 4. Verificamos que el tipo sea válido y mostramos el toast
        if (Toast[toastType]) {
            Toast[toastType](decodedMessage);
            
            // 5. Limpiamos los parámetros de la URL sin recargar la página
            urlParams.delete('toast');
            urlParams.delete('mensaje');
            const newUrl = window.location.pathname + 
                         (urlParams.toString() ? '?' + urlParams.toString() : '');
            window.history.replaceState({}, '', newUrl);
        }
    }
});
/*
Toast.success("Operación exitosa");
Toast.error("Error encontrado", 3000); // Duración en ms
Toast.warning("Advertencia");
Toast.info("Información", 2000);
/*Toast.success('Operación completada con éxito');
Toast.error('Ocurrió un error inesperado');
Toast.warning('Esta acción requiere confirmación');*/