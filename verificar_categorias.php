<?php
require 'conexion.php';

// Verificar si la tabla categorias existe
$sql = "SHOW TABLES LIKE 'categorias'";
$result = $conexion->query($sql);

if ($result->num_rows == 0) {
    echo "La tabla 'categorias' no existe. Creando la tabla...<br>";
    
    // Crear la tabla categorias
    $sql = "CREATE TABLE categorias (
        id INT AUTO_INCREMENT PRIMARY KEY,
        codigo VARCHAR(50) NOT NULL,
        nombre VARCHAR(100) NOT NULL,
        ubicacion VARCHAR(100)
    )";
    
    if ($conexion->query($sql)) {
        echo "Tabla 'categorias' creada exitosamente.<br>";
    } else {
        echo "Error al crear la tabla: " . $conexion->error . "<br>";
    }
} else {
    echo "La tabla 'categorias' existe.<br>";
    
    // Verificar si hay datos en la tabla
    $sql = "SELECT COUNT(*) as total FROM categorias";
    $result = $conexion->query($sql);
    $row = $result->fetch_assoc();
    
    if ($row['total'] == 0) {
        echo "La tabla 'categorias' está vacía. Agregando algunas categorías de ejemplo...<br>";
        
        // Insertar algunas categorías de ejemplo
        $sql = "INSERT INTO categorias (codigo, nombre, ubicacion) VALUES 
            ('CAT001', 'Ropa', 'Estante A'),
            ('CAT002', 'Calzado', 'Estante B'),
            ('CAT003', 'Accesorios', 'Estante C')";
        
        if ($conexion->query($sql)) {
            echo "Categorías de ejemplo agregadas exitosamente.<br>";
        } else {
            echo "Error al agregar categorías: " . $conexion->error . "<br>";
        }
    } else {
        echo "La tabla 'categorias' tiene " . $row['total'] . " registros.<br>";
    }
}

// Mostrar las categorías existentes
$sql = "SELECT * FROM categorias";
$result = $conexion->query($sql);

if ($result->num_rows > 0) {
    echo "<br>Lista de categorías:<br>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Código</th><th>Nombre</th><th>Ubicación</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['codigo'] . "</td>";
        echo "<td>" . $row['nombre'] . "</td>";
        echo "<td>" . $row['ubicacion'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

$conexion->close();
?> 