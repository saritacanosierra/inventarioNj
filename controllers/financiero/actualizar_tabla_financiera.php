<?php
require_once '../../conexion.php';

try {
    // Modificar el tipo de dato de la columna tipoCompra
    $sql_modify = "ALTER TABLE financiera MODIFY COLUMN tipoCompra VARCHAR(50) NOT NULL DEFAULT 'gasto'";
    if (!$conexion->query($sql_modify)) {
        throw new Exception("Error al modificar la columna tipoCompra: " . $conexion->error);
    }
    echo "Columna tipoCompra modificada correctamente a VARCHAR(50).";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conexion->close();
?> 