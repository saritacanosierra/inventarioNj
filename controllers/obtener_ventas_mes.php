<?php
require_once '../conexion.php';

// Obtener el mes y año de la petición
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : date('n');
$año = isset($_GET['año']) ? intval($_GET['año']) : date('Y');

// Validar el mes
if ($mes < 1 || $mes > 12) {
    echo json_encode([
        'success' => false,
        'message' => 'Mes inválido'
    ]);
    exit;
}

try {
    // Obtener las ventas del mes
    $sql = "SELECT v.*, p.nombre as nombre_producto, p.codigo as codigo_producto,
            DATE_FORMAT(v.fecha_venta, '%Y-%m-%d %H:%i:%s') as fecha_venta
            FROM ventas v
            JOIN productos p ON v.id_producto = p.id
            WHERE MONTH(v.fecha_venta) = ? AND YEAR(v.fecha_venta) = ?
            ORDER BY v.fecha_venta DESC";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $mes, $año);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Obtener el total de ventas y la cantidad
    $sql_totales = "SELECT 
                    COUNT(*) as cantidad_ventas,
                    COALESCE(SUM(total), 0) as total_ventas
                    FROM ventas
                    WHERE MONTH(fecha_venta) = ? AND YEAR(fecha_venta) = ?";

    $stmt_totales = $conexion->prepare($sql_totales);
    $stmt_totales->bind_param("ii", $mes, $año);
    $stmt_totales->execute();
    $totales = $stmt_totales->get_result()->fetch_assoc();

    // Preparar la respuesta
    $ventas = [];
    while ($venta = $resultado->fetch_assoc()) {
        $ventas[] = $venta;
    }

    echo json_encode([
        'success' => true,
        'ventas' => $ventas,
        'total_ventas' => $totales['total_ventas'],
        'cantidad_ventas' => $totales['cantidad_ventas']
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener las ventas: ' . $e->getMessage()
    ]);
}

$conexion->close();
?> 