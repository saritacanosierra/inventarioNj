<?php
require_once '../conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $fecha_venta = $_POST['fecha_venta'];
    $id_producto = $_POST['id_producto'];
    $cantidad = $_POST['cantidad'];
    $precio_unitario = $_POST['precio_unitario'];
    $total = $_POST['total'];
    $tipo_pago = $_POST['tipo_pago'];

    try {
        // Iniciar transacción
        $conexion->begin_transaction();

        // Obtener la venta actual para comparar cantidades
        $sql_venta_actual = "SELECT cantidad, id_producto FROM ventas WHERE id = ?";
        $stmt_venta_actual = $conexion->prepare($sql_venta_actual);
        $stmt_venta_actual->bind_param("i", $id);
        $stmt_venta_actual->execute();
        $venta_actual = $stmt_venta_actual->get_result()->fetch_assoc();

        // Si el producto cambió, devolver el stock del producto anterior
        if ($venta_actual['id_producto'] != $id_producto) {
            $sql_devolver_stock = "UPDATE productos SET stock = stock + ? WHERE id = ?";
            $stmt_devolver = $conexion->prepare($sql_devolver_stock);
            $stmt_devolver->bind_param("ii", $venta_actual['cantidad'], $venta_actual['id_producto']);
            $stmt_devolver->execute();
        }

        // Verificar el stock del nuevo producto
        $sql_stock = "SELECT stock FROM productos WHERE id = ?";
        $stmt_stock = $conexion->prepare($sql_stock);
        $stmt_stock->bind_param("i", $id_producto);
        $stmt_stock->execute();
        $resultado_stock = $stmt_stock->get_result();
        $producto = $resultado_stock->fetch_assoc();

        // Calcular la diferencia de stock necesaria
        $diferencia_stock = $cantidad - $venta_actual['cantidad'];
        if ($producto['stock'] < $diferencia_stock) {
            throw new Exception("No hay suficiente stock disponible");
        }

        // Actualizar la venta
        $sql = "UPDATE ventas SET 
                fecha_venta = ?, 
                id_producto = ?, 
                cantidad = ?, 
                precio_unitario = ?, 
                total = ?,
                tipo_pago = ?
                WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("siiddsi", $fecha_venta, $id_producto, $cantidad, $precio_unitario, $total, $tipo_pago, $id);
        $stmt->execute();

        // Actualizar el stock
        $sql_update = "UPDATE productos SET stock = stock - ? WHERE id = ?";
        $stmt_update = $conexion->prepare($sql_update);
        $stmt_update->bind_param("ii", $diferencia_stock, $id_producto);
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
            'id' => $id,
            'fecha_venta' => $fecha_venta,
            'id_producto' => $id_producto,
            'codigo_producto' => $producto_info['codigo'],
            'nombre_producto' => $producto_info['nombre'],
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_unitario,
            'total' => $total,
            'tipo_pago' => $tipo_pago
        ];

        echo json_encode($respuesta);

    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conexion->rollback();
        
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar la venta: ' . $e->getMessage()
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