<?php
require_once 'conexion.php';

// Crear la tabla ventas si no existe
$sql = "CREATE TABLE IF NOT EXISTS ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_venta DATE NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
)";

if ($conexion->query($sql) === TRUE) {
    echo "Tabla ventas creada correctamente o ya existía";
} else {
    echo "Error al crear la tabla: " . $conexion->error;
}

$conexion->close();
?> 