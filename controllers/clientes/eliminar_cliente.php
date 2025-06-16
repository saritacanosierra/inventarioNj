<?php
require_once __DIR__ . '/../../conexion.php';

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    error_log('eliminar_cliente.php - ID no válido: ' . ($_GET['id'] ?? 'no definido'));
    echo "<script>
        alert('ID de cliente no válido');
        window.location.href = '/inventarioNj/pages/envios.php';
    </script>";
    exit;
}

$id_cliente = intval($_GET['id']);

try {
    // Iniciar transacción
    $conexion->begin_transaction();

    // Verificar si el cliente existe antes de eliminar
    $sql_check_exists = "SELECT id, nombre FROM clientes WHERE id = ?";
    $stmt_check_exists = $conexion->prepare($sql_check_exists);
    $stmt_check_exists->bind_param("i", $id_cliente);
    $stmt_check_exists->execute();
    $result_exists = $stmt_check_exists->get_result();
    
    if ($result_exists->num_rows == 0) {
        throw new Exception("El cliente con ID $id_cliente no existe");
    }
    
    $cliente = $result_exists->fetch_assoc();

    // Verificar si el cliente tiene ventas asociadas
    $sql_check_ventas = "SELECT COUNT(*) as total FROM ventas WHERE id_cliente = ?";
    $stmt_check_ventas = $conexion->prepare($sql_check_ventas);
    $stmt_check_ventas->bind_param("i", $id_cliente);
    $stmt_check_ventas->execute();
    $result_ventas = $stmt_check_ventas->get_result();
    $row_ventas = $result_ventas->fetch_assoc();

    if ($row_ventas['total'] > 0) {
        throw new Exception("No se puede eliminar el cliente porque tiene ventas asociadas");
    }

    // Verificar si el cliente tiene envíos asociados
    $sql_check_envios = "SELECT COUNT(*) as total FROM envios WHERE cliente_id = ?";
    $stmt_check_envios = $conexion->prepare($sql_check_envios);
    $stmt_check_envios->bind_param("i", $id_cliente);
    $stmt_check_envios->execute();
    $result_envios = $stmt_check_envios->get_result();
    $row_envios = $result_envios->fetch_assoc();

    if ($row_envios['total'] > 0) {
        throw new Exception("No se puede eliminar el cliente porque tiene envíos asociados");
    }

    // Eliminar el cliente
    $sql = "DELETE FROM clientes WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_cliente);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al eliminar el cliente: " . $stmt->error);
    }
    
    $filas_afectadas = $stmt->affected_rows;

    if ($filas_afectadas == 0) {
        throw new Exception("No se pudo eliminar el cliente. Posiblemente ya no existe.");
    }

    // Confirmar la transacción
    $conexion->commit();

    echo "<script>
        alert('Cliente eliminado exitosamente');
        window.location.href = '/inventarioNj/pages/envios.php';
    </script>";

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conexion->rollback();
    error_log('eliminar_cliente.php - Error: ' . $e->getMessage());
    
    echo "<script>
        alert('" . $e->getMessage() . "');
        window.location.href = '/inventarioNj/pages/envios.php';
    </script>";
}

// Cerrar las conexiones
if (isset($stmt_check_exists)) $stmt_check_exists->close();
if (isset($stmt_check_ventas)) $stmt_check_ventas->close();
if (isset($stmt_check_envios)) $stmt_check_envios->close();
if (isset($stmt)) $stmt->close();
$conexion->close();
?> 