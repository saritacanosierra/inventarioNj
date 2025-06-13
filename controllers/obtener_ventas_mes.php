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
    // Obtener las ventas del mes con detalles de productos
    $sql = "SELECT v.id, v.fecha_venta, v.total, v.tipo_pago, v.estado,
                   c.nombre as nombre_cliente, c.cedula as cedula_cliente,
                   GROUP_CONCAT(CONCAT(p.codigo, ' - ', p.nombre, ' (', dv.cantidad, ')') SEPARATOR ', ') as productos,
                   SUM(dv.cantidad) as cantidad_total
            FROM ventas v
            LEFT JOIN clientes c ON v.id_cliente = c.id
            LEFT JOIN detalle_ventas dv ON v.id = dv.venta_id
            LEFT JOIN productos p ON dv.producto_id = p.id
            WHERE MONTH(v.fecha_venta) = ? AND YEAR(v.fecha_venta) = ?
            GROUP BY v.id
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
        $ventas[] = [
            'id' => $venta['id'],
            'fecha_venta' => $venta['fecha_venta'],
            'total' => $venta['total'],
            'tipo_pago' => $venta['tipo_pago'],
            'estado' => $venta['estado'],
            'nombre_cliente' => $venta['nombre_cliente'],
            'cedula_cliente' => $venta['cedula_cliente'],
            'productos' => $venta['productos'] ?: 'Sin productos',
            'cantidad' => $venta['cantidad_total'] ?: 0
        ];
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