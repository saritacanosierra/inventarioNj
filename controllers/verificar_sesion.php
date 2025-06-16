<?php
session_start();
header('Content-Type: application/json');

$response = array(
    'logged_in' => isset($_SESSION['usuario']),
    'usuario' => isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null
);

echo json_encode($response);
?> 