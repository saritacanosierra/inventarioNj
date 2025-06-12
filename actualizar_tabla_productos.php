<?php
require 'conexion.php';

// Leer el archivo SQL
$sql = file_get_contents('sql/actualizar_tabla_productos.sql');

// Ejecutar las consultas
if ($conexion->multi_query($sql)) {
    do {
        // Guardar el resultado
        if ($result = $conexion->store_result()) {
            $result->free();
        }
    } while ($conexion->more_results() && $conexion->next_result());
    
    echo "La tabla productos ha sido actualizada exitosamente.";
} else {
    echo "Error al actualizar la tabla: " . $conexion->error;
}

$conexion->close();
?> 