<?php
require_once '../conexion.php';

try {
    // Crear la tabla detalle_ventas
    $sql = "CREATE TABLE IF NOT EXISTS detalle_ventas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_venta INT NOT NULL,
        id_producto INT NOT NULL,
        cantidad INT NOT NULL,
        precio_unitario DECIMAL(10,2) NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (id_venta) REFERENCES ventas(id) ON DELETE CASCADE,
        FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE RESTRICT,
        INDEX idx_venta (id_venta),
        INDEX idx_producto (id_producto)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conexion->query($sql)) {
        echo "Tabla detalle_ventas creada exitosamente o ya existía.<br>";
    } else {
        throw new Exception("Error al crear la tabla detalle_ventas: " . $conexion->error);
    }

    // Verificar si la tabla ventas tiene las columnas necesarias
    $sql_check_ventas = "SHOW COLUMNS FROM ventas LIKE 'id_cliente'";
    $result = $conexion->query($sql_check_ventas);
    
    if ($result->num_rows == 0) {
        // Agregar columna id_cliente si no existe
        $sql_add_cliente = "ALTER TABLE ventas ADD COLUMN id_cliente INT NULL AFTER id";
        if ($conexion->query($sql_add_cliente)) {
            echo "Columna id_cliente agregada a la tabla ventas.<br>";
        }
    }

    // Agregar columnas adicionales si no existen
    $sql_check_tipo_pago = "SHOW COLUMNS FROM ventas LIKE 'tipo_pago'";
    $result_tipo_pago = $conexion->query($sql_check_tipo_pago);
    
    if ($result_tipo_pago->num_rows == 0) {
        $sql_add_tipo_pago = "ALTER TABLE ventas ADD COLUMN tipo_pago VARCHAR(50) NOT NULL DEFAULT 'efectivo' AFTER total";
        if ($conexion->query($sql_add_tipo_pago)) {
            echo "Columna tipo_pago agregada a la tabla ventas.<br>";
        }
    }

    $sql_check_estado = "SHOW COLUMNS FROM ventas LIKE 'estado'";
    $result_estado = $conexion->query($sql_check_estado);
    
    if ($result_estado->num_rows == 0) {
        $sql_add_estado = "ALTER TABLE ventas ADD COLUMN estado VARCHAR(50) NOT NULL DEFAULT 'completada' AFTER tipo_pago";
        if ($conexion->query($sql_add_estado)) {
            echo "Columna estado agregada a la tabla ventas.<br>";
        }
    }

    echo "Estructura de base de datos actualizada correctamente.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conexion->close();
?> 