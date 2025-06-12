<?php
require 'conexion.php';

// Verificar si la tabla categorias existe
$sql_check = "SHOW TABLES LIKE 'categorias'";
$result_check = $conexion->query($sql_check);

if ($result_check->num_rows === 0) {
    // Crear la tabla si no existe
    $sql_create = "CREATE TABLE categorias (
        id INT AUTO_INCREMENT PRIMARY KEY,
        codigo VARCHAR(50) NOT NULL UNIQUE,
        nombre VARCHAR(100) NOT NULL,
        ubicacion VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($conexion->query($sql_create)) {
        echo "Tabla categorias creada exitosamente.<br>";
    } else {
        echo "Error al crear la tabla categorias: " . $conexion->error . "<br>";
    }
}

// Mostrar la estructura actual de la tabla
echo "<br>Estructura actual de la tabla categorias:<br>";
$sql_structure = "DESCRIBE categorias";
$result_structure = $conexion->query($sql_structure);
echo "<pre>";
while ($row = $result_structure->fetch_assoc()) {
    print_r($row);
}
echo "</pre>";

// Mostrar todas las categorías
echo "<br>Categorías en la base de datos:<br>";
$sql_categorias = "SELECT * FROM categorias ORDER BY nombre";
$result_categorias = $conexion->query($sql_categorias);
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Código</th><th>Nombre</th><th>Ubicación</th></tr>";
while ($row = $result_categorias->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['codigo'] . "</td>";
    echo "<td>" . $row['nombre'] . "</td>";
    echo "<td>" . $row['ubicacion'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Verificar códigos duplicados
echo "<br>Verificando códigos duplicados:<br>";
$sql_duplicates = "SELECT codigo, COUNT(*) as count FROM categorias GROUP BY codigo HAVING count > 1";
$result_duplicates = $conexion->query($sql_duplicates);
if ($result_duplicates->num_rows > 0) {
    echo "Se encontraron códigos duplicados:<br>";
    while ($row = $result_duplicates->fetch_assoc()) {
        echo "Código: " . $row['codigo'] . " aparece " . $row['count'] . " veces<br>";
    }
} else {
    echo "No se encontraron códigos duplicados.<br>";
}

$conexion->close();
?> 