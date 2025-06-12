<?php
require '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Iniciar transacción
    $conexion->begin_transaction();

    try {
        // Obtener información de la venta
        $sql_venta = "SELECT id_producto, cantidad FROM ventas WHERE id = ?";
        $stmt_venta = $conexion->prepare($sql_venta);
        $stmt_venta->bind_param("i", $id);
        $stmt_venta->execute();
        $result_venta = $stmt_venta->get_result();
        $venta = $result_venta->fetch_assoc();

        if (!$venta) {
            throw new Exception("Venta no encontrada.");
        }

        // Restaurar el stock del producto
        $sql_update_stock = "UPDATE productos SET stock = stock + ? WHERE id = ?";
        $stmt_update = $conexion->prepare($sql_update_stock);
        $stmt_update->bind_param("ii", $venta['cantidad'], $venta['id_producto']);
        $stmt_update->execute();

        // Eliminar la venta
        $sql_delete = "DELETE FROM ventas WHERE id = ?";
        $stmt_delete = $conexion->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id);
        $stmt_delete->execute();

        // Confirmar transacción
        $conexion->commit();

        header("Location: ../pages/ventas.php");
        exit();
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conexion->rollback();
        die("Error al eliminar la venta: " . $e->getMessage());
    }
} else {
    header("Location: ../pages/ventas.php");
    exit();
} 