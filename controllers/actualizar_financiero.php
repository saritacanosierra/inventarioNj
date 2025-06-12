<?php
require '../conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigoProveedor = $_POST['codigoProveedor'];
    $nombreProveedor = $_POST['nombreProveedor'];
    $fechaCompra = $_POST['fechaCompra'];
    $valorCompra = $_POST['valorCompra'];
    $numeroTelefono = $_POST['numeroTelefono'];
    $cantidadComprada = $_POST['cantidadComprada'];

    // Actualizar el registro
    $sql = "UPDATE financiera 
            SET nombreProveedor = ?, 
                fechaCompra = ?, 
                valorCompra = ?, 
                numeroTelefono = ?, 
                cantidadComprada = ? 
            WHERE codigoProveedor = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssdsii", $nombreProveedor, $fechaCompra, $valorCompra, $numeroTelefono, $cantidadComprada, $codigoProveedor);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'codigoProveedor' => $codigoProveedor,
            'nombreProveedor' => $nombreProveedor,
            'fechaCompra' => $fechaCompra,
            'valorCompra' => $valorCompra,
            'numeroTelefono' => $numeroTelefono,
            'cantidadComprada' => $cantidadComprada
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar el registro: ' . $conexion->error
        ]);
    }

    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}

$conexion->close();
?> 