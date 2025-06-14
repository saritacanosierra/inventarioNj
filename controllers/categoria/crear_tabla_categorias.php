<?php
require_once '../../conexion.php';

// Mostrar información de depuración
echo "Intentando crear tabla categorias...<br>";

// Crear la tabla categorias si no existe
$sql = "CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
)";

try {
    if ($conexion->query($sql) === TRUE) {
        echo "Tabla categorias creada correctamente o ya existía<br>";
        
        // Verificar si hay categorías, si no, insertar algunas por defecto
        $check = $conexion->query("SELECT COUNT(*) as total FROM categorias");
        if ($check === false) {
            echo "Error al verificar categorías: " . $conexion->error . "<br>";
        } else {
            $row = $check->fetch_assoc();
            
            if ($row['total'] == 0) {
                $categorias = [
                    "Electrónicos",
                    "Ropa",
                    "Hogar",
                    "Alimentos",
                    "Bebidas"
                ];
                
                $stmt = $conexion->prepare("INSERT INTO categorias (nombre) VALUES (?)");
                if ($stmt === false) {
                    echo "Error al preparar la inserción: " . $conexion->error . "<br>";
                } else {
                    foreach ($categorias as $categoria) {
                        $stmt->bind_param("s", $categoria);
                        if (!$stmt->execute()) {
                            echo "Error al insertar categoría '$categoria': " . $stmt->error . "<br>";
                        }
                    }
                    echo "Categorías por defecto insertadas correctamente<br>";
                }
            } else {
                echo "Ya existen " . $row['total'] . " categorías en la tabla<br>";
            }
        }
    } else {
        echo "Error al crear la tabla: " . $conexion->error . "<br>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

$conexion->close();
?> 