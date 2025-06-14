<?php
require '../../conexion.php';

header('Content-Type: application/json');

// Log para depuración
error_log("Actualizar financiero - Método: " . $_SERVER['REQUEST_METHOD']);
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
    error_log("Todos los valores recibidos: " . print_r([
        'codigoProveedor' => $codigoProveedor,
        'nombreProveedor' => $nombreProveedor,
        'fechaCompra' => $fechaCompra,
        'valorCompra' => $valorCompra,
        'numeroTelefono' => $numeroTelefono,
        'cantidadComprada' => $cantidadComprada,
        'tipoCompra' => $tipoCompra
    ], true));

    // Actualizar el registro
    $sql = "UPDATE financiera 
            SET nombreProveedor = ?, 
                fechaCompra = ?, 
                valorCompra = ?, 
                numeroTelefono = ?, 
                cantidadComprada = ?,
                tipoCompra = ?
            WHERE codigoProveedor = ?";
    
    error_log("SQL a ejecutar: " . $sql);
    
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        error_log("Error en prepare: " . $conexion->error);
        echo json_encode([
            'success' => false,
            'message' => 'Error en prepare: ' . $conexion->error
        ]);
        exit;
    }
    
    $stmt->bind_param("ssdssss", $nombreProveedor, $fechaCompra, $valorCompra, $numeroTelefono, $cantidadComprada, $tipoCompra, $codigoProveedor);
    
    if ($stmt->execute()) {
        error_log("Actualización exitosa - tipoCompra actualizado a: $tipoCompra");
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
        error_log("Detalles del error: " . print_r([
            'error' => $stmt->error,
            'errno' => $stmt->errno,
            'sqlstate' => $stmt->sqlstate
        ], true));
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar el registro: ' . $stmt->error
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