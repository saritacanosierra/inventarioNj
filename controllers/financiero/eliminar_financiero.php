<?php
require_once __DIR__ . '/../../conexion.php';

// Log para depuración
error_log('eliminar_financiero.php - Iniciando proceso de eliminación');

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    error_log('eliminar_financiero.php - ID no válido: ' . ($_GET['id'] ?? 'no definido'));
    echo "<script>
        alert('ID de registro no válido');
        window.location.href = '/inventarioNj/pages/financiero.php';
    </script>";
    exit;
}

$codigoProveedor = intval($_GET['id']);
error_log('eliminar_financiero.php - ID del registro a eliminar: ' . $codigoProveedor);

try {
    // Iniciar transacción
    $conexion->begin_transaction();
    error_log('eliminar_financiero.php - Transacción iniciada');

    // Verificar si el registro existe antes de eliminar
    $sql_check_exists = "SELECT codigoProveedor FROM financiera WHERE codigoProveedor = ?";
    $stmt_check_exists = $conexion->prepare($sql_check_exists);
    $stmt_check_exists->bind_param("i", $codigoProveedor);
    $stmt_check_exists->execute();
    $result_exists = $stmt_check_exists->get_result();
    
    if ($result_exists->num_rows == 0) {
        throw new Exception("El registro con ID $codigoProveedor no existe");
    }

    // Eliminar el registro
    $sql = "DELETE FROM financiera WHERE codigoProveedor = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $codigoProveedor);
    
    error_log('eliminar_financiero.php - Ejecutando DELETE para registro ID: ' . $codigoProveedor);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al eliminar el registro: " . $stmt->error);
    }
    
    $filas_afectadas = $stmt->affected_rows;
    error_log('eliminar_financiero.php - Filas afectadas: ' . $filas_afectadas);

    if ($filas_afectadas == 0) {
        throw new Exception("No se pudo eliminar el registro. Posiblemente ya no existe.");
    }

    // Confirmar la transacción
    $conexion->commit();
    error_log('eliminar_financiero.php - Transacción confirmada');

    echo "<script>
        alert('Registro eliminado exitosamente');
        window.location.href = '/inventarioNj/pages/financiero.php';
    </script>";

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conexion->rollback();
    error_log('eliminar_financiero.php - Error: ' . $e->getMessage());
    
    echo "<script>
        alert('" . $e->getMessage() . "');
        window.location.href = '/inventarioNj/pages/financiero.php';
    </script>";
}

// Cerrar las conexiones
if (isset($stmt_check_exists)) $stmt_check_exists->close();
if (isset($stmt)) $stmt->close();
$conexion->close();
error_log('eliminar_financiero.php - Proceso finalizado');
?> 