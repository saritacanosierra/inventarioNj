<?php
require 'conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cedula'])) {
    $cedula = $_POST['cedula'];
    
    $sql = "SELECT id FROM usuarios WHERE cedula = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo json_encode([
        'exists' => $result->num_rows > 0
    ]);
    
    $stmt->close();
} else {
    echo json_encode([
        'exists' => false,
        'error' => 'Invalid request'
    ]);
}

$conexion->close();
?> 