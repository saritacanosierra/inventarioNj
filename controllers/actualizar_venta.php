<?php
require '../conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $id_producto = $_POST['id_producto'];
    $cantidad = $_POST['cantidad'];
    $precio_unitario = $_POST['precio_unitario'];
    $total = $_POST['total'];

    // Iniciar transacción
    $conexion->begin_transaction();

    try {
        // Obtener la venta actual
        $sql_venta_actual = "SELECT id_producto, cantidad FROM ventas WHERE id = ?";
        $stmt_venta_actual = $conexion->prepare($sql_venta_actual);
        $stmt_venta_actual->bind_param("i", $id);
        $stmt_venta_actual->execute();
        $result_venta_actual = $stmt_venta_actual->get_result();
        $venta_actual = $result_venta_actual->fetch_assoc();

        // Obtener stock actual del producto
        $sql_stock = "SELECT stock FROM productos WHERE id = ?";
        $stmt_stock = $conexion->prepare($sql_stock);
        $stmt_stock->bind_param("i", $id_producto);
        $stmt_stock->execute();
        $result_stock = $stmt_stock->get_result();
        $producto = $result_stock->fetch_assoc();

        // Calcular diferencia de stock
        $diferencia_stock = 0;
        if ($venta_actual['id_producto'] == $id_producto) {
            // Mismo producto, solo ajustar la diferencia
            $diferencia_stock = $venta_actual['cantidad'] - $cantidad;
        } else {
            // Producto diferente, devolver stock anterior y restar nuevo
            $diferencia_stock = $venta_actual['cantidad'] - $cantidad;
            
            // Devolver stock al producto anterior
            $sql_update_stock_anterior = "UPDATE productos SET stock = stock + ? WHERE id = ?";
            $stmt_update_anterior = $conexion->prepare($sql_update_stock_anterior);
            $stmt_update_anterior->bind_param("ii", $venta_actual['cantidad'], $venta_actual['id_producto']);
            $stmt_update_anterior->execute();
        }

        // Verificar si hay suficiente stock para la nueva cantidad
        if ($producto['stock'] + $diferencia_stock < 0) {
            throw new Exception("No hay suficiente stock disponible para esta operación.");
        }

        // Actualizar la venta
        $sql_venta = "UPDATE ventas SET id_producto = ?, cantidad = ?, precio_unitario = ?, total = ? WHERE id = ?";
        $stmt_venta = $conexion->prepare($sql_venta);
        $stmt_venta->bind_param("iiddi", $id_producto, $cantidad, $precio_unitario, $total, $id);
        $stmt_venta->execute();

        // Actualizar el stock del producto
        $sql_update_stock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
        $stmt_update = $conexion->prepare($sql_update_stock);
        $stmt_update->bind_param("ii", $cantidad, $id_producto);
        $stmt_update->execute();

        // Obtener información del producto para la respuesta
        $sql_producto = "SELECT codigo, nombre FROM productos WHERE id = ?";
        $stmt_producto = $conexion->prepare($sql_producto);
        $stmt_producto->bind_param("i", $id_producto);
        $stmt_producto->execute();
        $result_producto = $stmt_producto->get_result();
        $info_producto = $result_producto->fetch_assoc();

        // Confirmar transacción
        $conexion->commit();

        echo json_encode([
            'success' => true,
            'id' => $id,
            'fecha_venta' => date('Y-m-d H:i:s'),
            'codigo_producto' => $info_producto['codigo'],
            'nombre_producto' => $info_producto['nombre'],
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_unitario,
            'total' => $total
        ]);
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conexion->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
} 