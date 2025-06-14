<?php
require '../../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Iniciar transacción
    $conexion->begin_transaction();

    try {
        // Verificar que la venta existe y obtener el ID del cliente
        $sql_check_venta = "SELECT id, id_cliente FROM ventas WHERE id = ?";
        $stmt_check = $conexion->prepare($sql_check_venta);
        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows == 0) {
            throw new Exception("Venta no encontrada.");
        }

        $venta = $result_check->fetch_assoc();
        $id_cliente = $venta['id_cliente'];

        // Obtener todos los productos de la venta desde detalle_ventas
        $sql_detalles = "SELECT producto_id, cantidad FROM detalle_ventas WHERE venta_id = ?";
        $stmt_detalles = $conexion->prepare($sql_detalles);
        $stmt_detalles->bind_param("i", $id);
        $stmt_detalles->execute();
        $result_detalles = $stmt_detalles->get_result();
        
        // Restaurar el stock de todos los productos de la venta
        while ($detalle = $result_detalles->fetch_assoc()) {
            $sql_update_stock = "UPDATE productos SET stock = stock + ? WHERE id = ?";
            $stmt_update = $conexion->prepare($sql_update_stock);
            $stmt_update->bind_param("ii", $detalle['cantidad'], $detalle['producto_id']);
            $stmt_update->execute();
        }

        // Restar 1 del total_compras del cliente si la venta tenía un cliente asociado
        if ($id_cliente && $id_cliente !== null) {
            $sql_update_cliente = "UPDATE clientes SET total_compras = GREATEST(total_compras - 1, 0) WHERE id = ?";
            $stmt_update_cliente = $conexion->prepare($sql_update_cliente);
            $stmt_update_cliente->bind_param("i", $id_cliente);
            $stmt_update_cliente->execute();
        }

        // Eliminar los detalles de la venta (se eliminarán automáticamente por CASCADE)
        // o eliminar manualmente si no hay CASCADE
        $sql_delete_detalles = "DELETE FROM detalle_ventas WHERE venta_id = ?";
        $stmt_delete_detalles = $conexion->prepare($sql_delete_detalles);
        $stmt_delete_detalles->bind_param("i", $id);
        $stmt_delete_detalles->execute();

        // Eliminar la venta principal
        $sql_delete = "DELETE FROM ventas WHERE id = ?";
        $stmt_delete = $conexion->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id);
        $stmt_delete->execute();

        // Confirmar transacción
        $conexion->commit();

        header("Location: ../../pages/ventas.php?success=venta_eliminada");
        exit();
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conexion->rollback();
        header("Location: ../../pages/ventas.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: ../../pages/ventas.php");
    exit();
} 