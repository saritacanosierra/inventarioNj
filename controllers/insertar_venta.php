<?php
require_once '../conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha_venta = $_POST['fecha_venta'];
    $id_producto = $_POST['id_producto'];
    $cantidad = $_POST['cantidad'];
    $precio_unitario = $_POST['precio_unitario'];
    $total = $_POST['total'];
    $tipo_pago = $_POST['tipo_pago'];

    try {
        // Iniciar transacción
        $conexion->begin_transaction();

        // Verificar el stock actual
        $sql_stock = "SELECT stock, precio FROM productos WHERE id = ?";
        $stmt_stock = $conexion->prepare($sql_stock);
        $stmt_stock->bind_param("i", $id_producto);
        $stmt_stock->execute();
        $resultado_stock = $stmt_stock->get_result();
        $producto = $resultado_stock->fetch_assoc();

        if ($producto['stock'] < $cantidad) {
            throw new Exception("No hay suficiente stock disponible");
        }

        // Insertar la venta
        $sql = "INSERT INTO ventas (fecha_venta, id_producto, cantidad, precio_unitario, total, tipo_pago) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("siidds", $fecha_venta, $id_producto, $cantidad, $precio_unitario, $total, $tipo_pago);
        $stmt->execute();

        // Actualizar el stock
        $nuevo_stock = $producto['stock'] - $cantidad;
        $sql_update = "UPDATE productos SET stock = ? WHERE id = ?";
        $stmt_update = $conexion->prepare($sql_update);
        $stmt_update->bind_param("ii", $nuevo_stock, $id_producto);
        $stmt_update->execute();

        // Obtener información del producto para la respuesta
        $sql_producto = "SELECT p.*, c.nombre as categoria 
                        FROM productos p 
                        LEFT JOIN categoria c ON p.id_categoria = c.id 
                        WHERE p.id = ?";
        $stmt_producto = $conexion->prepare($sql_producto);
        $stmt_producto->bind_param("i", $id_producto);
        $stmt_producto->execute();
        $producto_info = $stmt_producto->get_result()->fetch_assoc();

        // Confirmar transacción
        $conexion->commit();

        // Preparar la respuesta
        $respuesta = [
            'success' => true,
            'id' => $conexion->insert_id,
            'fecha_venta' => $fecha_venta,
            'id_producto' => $id_producto,
            'codigo_producto' => $producto_info['codigo'],
            'nombre_producto' => $producto_info['nombre'],
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_unitario,
            'total' => $total,
            'stock_actual' => $nuevo_stock,
            'tipo_pago' => $tipo_pago
        ];

        echo json_encode($respuesta);

    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conexion->rollback();
        
        echo json_encode([
            'success' => false,
            'message' => 'Error al registrar la venta: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}

$conexion->close();
?> 