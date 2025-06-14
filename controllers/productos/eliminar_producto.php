<?php
require '../../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Iniciar transacción
    $conexion->begin_transaction();
    
    try {
        // Verificar que el producto existe
        $sql_check = "SELECT id, nombre FROM productos WHERE id = ?";
        $stmt_check = $conexion->prepare($sql_check);
        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows == 0) {
            throw new Exception("Producto no encontrado.");
        }
        
        $producto = $result_check->fetch_assoc();
        
        // Eliminar registros relacionados en detalle_ventas
        $sql_delete_detalle = "DELETE FROM detalle_ventas WHERE producto_id = ?";
        $stmt_detalle = $conexion->prepare($sql_delete_detalle);
        $stmt_detalle->bind_param("i", $id);
        $stmt_detalle->execute();
        
        // Eliminar el producto
        $sql_delete_producto = "DELETE FROM productos WHERE id = ?";
        $stmt_producto = $conexion->prepare($sql_delete_producto);
        $stmt_producto->bind_param("i", $id);
        $stmt_producto->execute();
        
        // Confirmar transacción
        $conexion->commit();
        
        // Redireccionar con mensaje de éxito
        header('Location: ../../pages/listar_producto.php?success=1&message=' . urlencode("Producto '{$producto['nombre']}' eliminado exitosamente"));
        exit;
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conexion->rollback();
        
        // Redireccionar con mensaje de error
        header('Location: ../../pages/listar_producto.php?error=1&message=' . urlencode("Error al eliminar el producto: " . $e->getMessage()));
        exit;
    }
} else {
    // Redireccionar si no se proporciona ID
    header('Location: ../../pages/listar_producto.php?error=1&message=' . urlencode("ID de producto no proporcionado"));
    exit;
}

$conexion->close();
?>