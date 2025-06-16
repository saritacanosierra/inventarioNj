<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    // Redirigir al login
    header("Location: /inventarioNj/pages/login.php");
    exit();
}

// Si es una petición AJAX, devolver JSON
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode([
        'logged_in' => true,
        'usuario' => $_SESSION['usuario']
    ]);
    exit();
}

// Asegurarse de que no haya salida antes de la verificación
ob_start();
?> 