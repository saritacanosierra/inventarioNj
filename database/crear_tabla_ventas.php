<?php
require '../conexion.php';

// Leer el contenido del archivo SQL
$sql = file_get_contents('ventas.sql');

// Ejecutar el script SQL
if ($conexion->multi_query($sql)) {
    echo "Tabla 'ventas' creada exitosamente.";
} else {
    echo "Error al crear la tabla 'ventas': " . $conexion->error;
}

$conexion->close();
?> 