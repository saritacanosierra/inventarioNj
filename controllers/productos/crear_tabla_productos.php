<?php
require '../../conexion.php';

// Crear la tabla productos si no existe
$sql = "CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    categoria_id INT,
    FOREIGN KEY (categoria_id) REFERENCES categoria(id)
)";

if ($conexion->query($sql) === TRUE) {
    echo "Tabla productos creada correctamente o ya existía";
} else {
    echo "Error al crear la tabla: " . $conexion->error;
}

$conexion->close();
?> 