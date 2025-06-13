<?php
require '../conexion.php';

// Leer el contenido del archivo SQL
$sql = file_get_contents('nueva_estructura_ventas.sql');

// Ejecutar el script SQL
if ($conexion->multi_query($sql)) {
    echo "Estructura de ventas actualizada exitosamente.<br>";
    echo "Se han creado las nuevas tablas: ventas y detalle_ventas<br>";
    echo "La nueva estructura permite múltiples productos por venta.";
} else {
    echo "Error al actualizar la estructura de ventas: " . $conexion->error;
}

$conexion->close();
?> 