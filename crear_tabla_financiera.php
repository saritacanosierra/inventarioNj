<?php
require 'conexion.php';

// Crear la tabla financiera
$sql = "CREATE TABLE IF NOT EXISTS financiera (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fechaCompra DATE NOT NULL,
    valorCompra DECIMAL(10,2) NOT NULL,
    tipoCompra ENUM('gasto', 'inversion', 'compra') NOT NULL,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conexion->query($sql) === TRUE) {
    echo "Tabla 'financiera' creada exitosamente";
} else {
    echo "Error al crear la tabla: " . $conexion->error;
}

$conexion->close();
?> 