<?php
require '../conexion.php';

if (isset($_GET['codigoProveedor'])) {
    $codigoProveedor = $_GET['codigoProveedor'];

    // Eliminar el registro
    $sql = "DELETE FROM financiera WHERE codigoProveedor = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $codigoProveedor);

    if ($stmt->execute()) {
        header('Location: ../pages/financiero.php?mensaje=Registro eliminado exitosamente');
    } else {
        header('Location: ../pages/financiero.php?error=Error al eliminar el registro: ' . $conexion->error);
    }

    $stmt->close();
} else {
    header('Location: ../pages/financiero.php?error=Código de proveedor no especificado');
}

$conexion->close();
?> 