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

    // Validar que el código de proveedor no exista
    $sql_check = "SELECT codigoProveedor FROM financiera WHERE codigoProveedor = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("i", $codigoProveedor);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'El código de proveedor ya existe'
        ]);
        exit;
    }

    // Insertar el nuevo registro
    $sql = "INSERT INTO financiera (codigoProveedor, nombreProveedor, fechaCompra, valorCompra, numeroTelefono, cantidadComprada) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("issdsi", $codigoProveedor, $nombreProveedor, $fechaCompra, $valorCompra, $numeroTelefono, $cantidadComprada);

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
            'message' => 'Error al insertar el registro: ' . $conexion->error
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