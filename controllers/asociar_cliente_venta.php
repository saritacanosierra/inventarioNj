<?php
require_once '../conexion.php';

// Obtener los datos del POST
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id_cliente']) || !isset($data['id_venta'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
    exit;
}

$id_cliente = intval($data['id_cliente']);
$id_venta = intval($data['id_venta']);

try {
    // Actualizar la venta con el ID del cliente
    $sql = "UPDATE ventas SET id_cliente = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $id_cliente, $id_venta);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la venta']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conexion->close();
?> 