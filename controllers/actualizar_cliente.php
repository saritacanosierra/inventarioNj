<?php
require_once '../conexion.php';

// Verificar si se proporcionó un ID
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo "<script>
        alert('ID de cliente no válido');
        window.location.href = '../pages/envios.php';
    </script>";
    exit;
}

$id_cliente = intval($_POST['id']);
$nombre = trim($_POST['nombre'] ?? '');
$cedula = trim($_POST['cedula'] ?? '');
$celular = trim($_POST['celular'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');

// Validar que todos los campos estén completos
if (empty($nombre) || empty($cedula) || empty($celular) || empty($direccion)) {
    echo "<script>
        alert('Todos los campos son obligatorios');
        window.location.href = '../pages/envios.php';
    </script>";
    exit;
}

try {
    // Iniciar transacción
    $conexion->begin_transaction();

    // Verificar si la cédula ya existe en otro cliente
    $sql_check = "SELECT id FROM clientes WHERE cedula = ? AND id != ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("si", $cedula, $id_cliente);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        throw new Exception("Ya existe otro cliente con esta cédula");
    }

    // Actualizar el cliente
    $sql = "UPDATE clientes SET nombre = ?, cedula = ?, celular = ?, direccion = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssi", $nombre, $cedula, $celular, $direccion, $id_cliente);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al actualizar el cliente: " . $stmt->error);
    }

    // Confirmar la transacción
    $conexion->commit();

    echo "<script>
        alert('Cliente actualizado exitosamente');
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