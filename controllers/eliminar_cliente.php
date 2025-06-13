<?php
require_once '../conexion.php';

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>
        alert('ID de cliente no válido');
        window.location.href = '../pages/envios.php';
    </script>";
    exit;
}

$id_cliente = intval($_GET['id']);

try {
    // Iniciar transacción
    $conexion->begin_transaction();

    // Verificar si el cliente tiene ventas asociadas
    $sql_check = "SELECT COUNT(*) as total FROM ventas WHERE id_cliente = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("i", $id_cliente);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    $row = $result->fetch_assoc();

    if ($row['total'] > 0) {
        throw new Exception("No se puede eliminar el cliente porque tiene ventas asociadas");
    }

    // Eliminar el cliente
    $sql = "DELETE FROM clientes WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_cliente);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al eliminar el cliente: " . $stmt->error);
    }

    // Confirmar la transacción
    $conexion->commit();

    echo "<script>
        alert('Cliente eliminado exitosamente');
        window.location.href = '../pages/envios.php';
    </script>";

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conexion->rollback();
    
    echo "<script>
        alert('" . $e->getMessage() . "');
        window.location.href = '../pages/envios.php';
    </script>";
}

// Cerrar las conexiones
if (isset($stmt_check)) $stmt_check->close();
if (isset($stmt)) $stmt->close();
$conexion->close();
?> 