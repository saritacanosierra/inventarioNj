<?php
require '../../conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    
    $stmt = $conexion->prepare("SELECT COUNT(*) as count FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    echo json_encode(['exists' => $row['count'] > 0]);
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$conexion->close();
?> 