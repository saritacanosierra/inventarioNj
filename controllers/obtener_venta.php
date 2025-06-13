<?php
require_once '../conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id_venta <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'ID de venta inválido'
        ]);
        exit;
    }

    try {
        // Obtener información de la venta principal
        $sql_venta = "SELECT v.*, 
                       DATE_FORMAT(v.fecha_venta, '%Y-%m-%dT%H:%i') as fecha_venta_formateada,
                       c.nombre as nombre_cliente, c.cedula as cedula_cliente
                       FROM ventas v
                       LEFT JOIN clientes c ON v.id_cliente = c.id
                       WHERE v.id = ?";
        
        $stmt_venta = $conexion->prepare($sql_venta);
        if (!$stmt_venta) {
            throw new Exception("Error al preparar consulta de venta: " . $conexion->error);
        }
        $stmt_venta->bind_param("i", $id_venta);
        $stmt_venta->execute();
        $venta = $stmt_venta->get_result()->fetch_assoc();

        if (!$venta) {
            echo json_encode([
                'success' => false,
                'message' => 'Venta no encontrada'
            ]);
            exit;
        }

        // Obtener los productos de la venta desde detalle_ventas
        $sql_productos = "SELECT dv.*, p.nombre as nombre_producto, p.codigo as codigo_producto, p.precio as precio_actual
                          FROM detalle_ventas dv
                          JOIN productos p ON dv.producto_id = p.id
                          WHERE dv.venta_id = ?";
        
        $stmt_productos = $conexion->prepare($sql_productos);
        if (!$stmt_productos) {
            throw new Exception("Error al preparar consulta de productos: " . $conexion->error);
        }
        $stmt_productos->bind_param("i", $id_venta);
        $stmt_productos->execute();
        $productos = $stmt_productos->get_result()->fetch_all(MYSQLI_ASSOC);

        // Preparar la respuesta
        $respuesta = [
            'success' => true,
            'venta' => [
                'id' => $venta['id'],
                'fecha_venta' => $venta['fecha_venta_formateada'],
                'total' => $venta['total'],
                'tipo_pago' => $venta['tipo_pago'],
                'estado' => $venta['estado'],
                'nombre_cliente' => $venta['nombre_cliente'],
                'cedula_cliente' => $venta['cedula_cliente']
            ],
            'productos' => $productos
        ];

        echo json_encode($respuesta);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener la venta: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}

// Cerrar las conexiones
if (isset($stmt_venta)) $stmt_venta->close();
if (isset($stmt_productos)) $stmt_productos->close();
$conexion->close();
?> 