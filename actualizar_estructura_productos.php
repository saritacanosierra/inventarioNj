<?php
require 'conexion.php';

// Verificar y actualizar la estructura de la tabla productos
$sql = "ALTER TABLE productos 
        MODIFY COLUMN id_categoria INT,
        ADD CONSTRAINT fk_producto_categoria 
        FOREIGN KEY (id_categoria) REFERENCES categorias(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE";

if ($conexion->query($sql)) {
    echo "Estructura de la tabla productos actualizada correctamente.<br>";
} else {
    echo "Error al actualizar la estructura: " . $conexion->error . "<br>";
}

// Mostrar la estructura actual
echo "<br>Estructura actual de la tabla productos:<br>";
$sql = "DESCRIBE productos";
$result = $conexion->query($sql);
echo "<pre>";
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
echo "</pre>";

// Mostrar los productos y sus categorías
echo "<br>Productos y sus categorías:<br>";
$sql = "SELECT p.id, p.codigo, p.nombre, p.id_categoria, c.nombre as categoria_nombre 
        FROM productos p 
        LEFT JOIN categorias c ON p.id_categoria = c.id 
        ORDER BY p.id";
$result = $conexion->query($sql);
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Código</th><th>Nombre</th><th>ID Categoría</th><th>Nombre Categoría</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['codigo'] . "</td>";
    echo "<td>" . $row['nombre'] . "</td>";
    echo "<td>" . ($row['id_categoria'] ?? 'NULL') . "</td>";
    echo "<td>" . ($row['categoria_nombre'] ?? 'Sin categoría') . "</td>";
    echo "</tr>";
}
echo "</table>";

$conexion->close();
?> 