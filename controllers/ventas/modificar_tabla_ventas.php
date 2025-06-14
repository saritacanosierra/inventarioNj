<?php
require_once '../conexion.php';

try {
    // Modificar la columna id_cliente para permitir NULL
    $sql = "ALTER TABLE ventas MODIFY COLUMN id_cliente INT(11) NULL";
    
    if ($conexion->query($sql)) {
        echo "Tabla ventas modificada exitosamente";
    } else {
        throw new Exception("Error al modificar la tabla: " . $conexion->error);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conexion->close();
?> 