<?php
require '../conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = $_POST['id_producto'];
    $cantidad = $_POST['cantidad'];
    $precio_unitario = $_POST['precio_unitario'];
    $total = $_POST['total'];
    $fecha_venta = $_POST['fecha_venta'];

    // Iniciar transacción
    $conexion->begin_transaction();

    try {
        // Verificar stock disponible
        $sql_stock = "SELECT stock FROM productos WHERE id = ?";
        $stmt_stock = $conexion->prepare($sql_stock);
        $stmt_stock->bind_param("i", $id_producto);
        $stmt_stock->execute();
        $result_stock = $stmt_stock->get_result();
        $producto = $result_stock->fetch_assoc();

        if ($producto['stock'] < $cantidad) {
            throw new Exception("No hay suficiente stock disponible.");
        }

        // Insertar la venta
        $sql_venta = "INSERT INTO ventas (id_producto, cantidad, precio_unitario, total, fecha_venta) VALUES (?, ?, ?, ?, ?)";
        $stmt_venta = $conexion->prepare($sql_venta);
        $stmt_venta->bind_param("iidds", $id_producto, $cantidad, $precio_unitario, $total, $fecha_venta);
        $stmt_venta->execute();

        // Actualizar el stock del producto
        $nuevo_stock = $producto['stock'] - $cantidad;
        $sql_update_stock = "UPDATE productos SET stock = ? WHERE id = ?";
        $stmt_update = $conexion->prepare($sql_update_stock);
        $stmt_update->bind_param("ii", $nuevo_stock, $id_producto);
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
            'id' => $conexion->insert_id,
            'fecha_venta' => $fecha_venta,
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