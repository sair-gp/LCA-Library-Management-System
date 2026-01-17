<?php
// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Guardar el modo de vista preferido
if (isset($_POST['modo'])) {
    $_SESSION['modo_vista_libros'] = $_POST['modo'] === 'tarjetas' ? 'tarjetas' : 'tabla';
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Modo no especificado']);
}
?>