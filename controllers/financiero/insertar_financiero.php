<?php
require '../../conexion.php';

header('Content-Type: application/json');

// Log para depuración
error_log("Insertar financiero - Método: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigoProveedor = $_POST['codigoProveedor'];
    $nombreProveedor = $_POST['nombreProveedor'];
    $fechaCompra = $_POST['fechaCompra'];
    $valorCompra = $_POST['valorCompra'];
    $numeroTelefono = $_POST['numeroTelefono'];
    $cantidadComprada = $_POST['cantidadComprada'];
    $tipoCompra = $_POST['tipoCompra'];

    // Log de los valores recibidos
    error_log("Valores recibidos - codigoProveedor: $codigoProveedor, tipoCompra: $tipoCompra");

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
    $sql = "INSERT INTO financiera (codigoProveedor, nombreProveedor, fechaCompra, valorCompra, numeroTelefono, cantidadComprada, tipoCompra) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("issdsss", $codigoProveedor, $nombreProveedor, $fechaCompra, $valorCompra, $numeroTelefono, $cantidadComprada, $tipoCompra);

    if ($stmt->execute()) {
        error_log("Inserción exitosa - tipoCompra: $tipoCompra");
        echo json_encode([
            'success' => true,
            'codigoProveedor' => $codigoProveedor,
            'nombreProveedor' => $nombreProveedor,
            'fechaCompra' => $fechaCompra,
            'valorCompra' => $valorCompra,
            'numeroTelefono' => $numeroTelefono,
            'cantidadComprada' => $cantidadComprada,
            'tipoCompra' => $tipoCompra
        ]);
    } else {
        error_log("Error en execute: " . $stmt->error);
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