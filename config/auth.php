<?php
/**
 * Manejo de sesion y autenticacion
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirige a login.php si el usuario no ha iniciado sesion.
 * Debe llamarse al inicio de cada pagina protegida.
 */
function requerirLogin() {
    if (empty($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Igual que requerirLogin() pero para endpoints de la API (responde JSON 401 en vez de redirigir).
 */
function requerirLoginApi() {
    if (empty($_SESSION['usuario_id'])) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => true, 'mensaje' => 'Sesion no iniciada. Vuelva a iniciar sesion.']);
        exit;
    }
}

function usuarioActual() {
    return $_SESSION['usuario_nombre'] ?? null;
}
