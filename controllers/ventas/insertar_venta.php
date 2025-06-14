<?php
require_once '../conexion.php';

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    // Obtener y validar los datos
    $fecha_venta = $_POST['fecha_venta'] ?? null;
    $id_producto = $_POST['id_producto'] ?? null;
    $cantidad = $_POST['cantidad'] ?? null;
    $precio_unitario = $_POST['precio_unitario'] ?? null;
    $total = $_POST['total'] ?? null;
    $tipo_pago = $_POST['tipo_pago'] ?? null;
    $id_cliente = !empty($_POST['id_cliente']) ? $_POST['id_cliente'] : null;

    // Log para debugging
    error_log('Insertar venta - POST recibido: ' . json_encode($_POST));
    error_log('Insertar venta - Datos procesados: fecha=' . $fecha_venta . ', producto=' . $id_producto . ', cantidad=' . $cantidad . ', precio=' . $precio_unitario . ', total=' . $total . ', tipo_pago=' . $tipo_pago . ', cliente=' . $id_cliente);

    if (!$fecha_venta || !$id_producto || !$cantidad || !$precio_unitario || !$total || !$tipo_pago) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
        exit;
    }

    // Iniciar transacción
    $conexion->begin_transaction();

    // Insertar la venta
    $sql = "INSERT INTO ventas (fecha_venta, id_producto, cantidad, precio_unitario, total, tipo_pago, id_cliente) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("siiddsi", $fecha_venta, $id_producto, $cantidad, $precio_unitario, $total, $tipo_pago, $id_cliente);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al insertar la venta: " . $stmt->error);
    }

    $id_venta = $conexion->insert_id;

    // Actualizar el stock del producto
    $sql_stock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
    $stmt_stock = $conexion->prepare($sql_stock);
    $stmt_stock->bind_param("ii", $cantidad, $id_producto);
    
    if (!$stmt_stock->execute()) {
        throw new Exception("Error al actualizar el stock: " . $stmt_stock->error);
    }

    // Obtener los datos de la venta insertada para la respuesta
    $sql_venta = "SELECT v.*, p.nombre as nombre_producto, p.codigo as codigo_producto, p.stock as stock_actual,
                         c.nombre as nombre_cliente
                  FROM ventas v
                  JOIN productos p ON v.id_producto = p.id
                  LEFT JOIN clientes c ON v.id_cliente = c.id
                  WHERE v.id = ?";
    $stmt_venta = $conexion->prepare($sql_venta);
    $stmt_venta->bind_param("i", $id_venta);
    $stmt_venta->execute();
    $resultado = $stmt_venta->get_result();
    $venta = $resultado->fetch_assoc();

    // Confirmar la transacción
    $conexion->commit();

    // Preparar la respuesta
    $respuesta = [
        'success' => true,
        'message' => 'Venta registrada exitosamente',
        'venta' => [
            'id' => $venta['id'],
            'fecha_venta' => $venta['fecha_venta'],
            'codigo_producto' => $venta['codigo_producto'],
            'nombre_producto' => $venta['nombre_producto'],
            'cantidad' => $venta['cantidad'],
            'precio_unitario' => $venta['precio_unitario'],
            'total' => $venta['total'],
            'tipo_pago' => $venta['tipo_pago'],
            'stock_actual' => $venta['stock_actual'],
            'nombre_cliente' => $venta['nombre_cliente']
        ]
    ];

    echo json_encode($respuesta);

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conexion->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Cerrar las conexiones
if (isset($stmt)) $stmt->close();
if (isset($stmt_stock)) $stmt_stock->close();
if (isset($stmt_venta)) $stmt_venta->close();
$conexion->close();
?> 