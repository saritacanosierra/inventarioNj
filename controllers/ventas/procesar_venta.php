<?php
require_once '../../conexion.php';
session_start();

// Configurar encabezado para respuesta JSON
header('Content-Type: application/json');

// Función para enviar respuesta JSON y terminar
function sendJsonResponse($success, $message, $data = []) {
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
    exit;
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    sendJsonResponse(false, 'Usuario no autenticado.');
}

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Método no permitido.');
}

try {
    // Verificar que las tablas necesarias existan
    $sql_check_ventas = "SHOW TABLES LIKE 'ventas'";
    $result_ventas = $conexion->query($sql_check_ventas);
    if ($result_ventas->num_rows == 0) {
        throw new Exception('La tabla ventas no existe. Ejecute el script de configuración.');
    }

    $sql_check_detalle = "SHOW TABLES LIKE 'detalle_ventas'";
    $result_detalle = $conexion->query($sql_check_detalle);
    if ($result_detalle->num_rows == 0) {
        throw new Exception('La tabla detalle_ventas no existe. Ejecute el script de configuración.');
    }

    // Obtener y validar los datos básicos de la venta
    $id_cliente = $_POST['id_cliente'] ?? null;
    $fecha_venta = $_POST['fecha_venta'] ?? null;
    $tipo_pago = $_POST['tipo_pago'] ?? null;
    $estado = $_POST['estado'] ?? 'completada';
    
    // Decodificar el JSON de productos
    $productos_json = $_POST['productos_venta_json'] ?? '[]';
    $productos = json_decode($productos_json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar los datos de los productos.');
    }

    if (!$fecha_venta || !$tipo_pago || empty($productos)) {
        throw new Exception('Faltan datos requeridos para la venta o no se han agregado productos.');
    }

    // Validar que haya al menos un producto
    if (count($productos) === 0) {
        throw new Exception('Debe agregar al menos un producto a la venta.');
    }

    // Iniciar transacción
    $conexion->begin_transaction();

    // Calcular el total de la venta
    $total_venta = 0;
    foreach ($productos as $producto) {
        if (!isset($producto['id']) || !isset($producto['cantidad']) || !isset($producto['precio_unitario'])) {
            throw new Exception('Datos de producto incompletos en el JSON.');
        }
        
        $cantidad = (int)$producto['cantidad'];
        $precio_unitario = (float)$producto['precio_unitario'];
        
        if ($cantidad <= 0 || $precio_unitario <= 0) {
            throw new Exception('Cantidad y precio deben ser mayores a 0 para el producto ' . ($producto['nombre'] ?? $producto['id']) . '.');
        }
        
        $total_venta += $cantidad * $precio_unitario;
    }

    // Insertar la venta principal
    if ($id_cliente === '' || $id_cliente === null) {
        $sql_venta = "INSERT INTO ventas (total, tipo_pago, estado, fecha_venta) 
                      VALUES (?, ?, ?, ?)";
        $stmt_venta = $conexion->prepare($sql_venta);
        if (!$stmt_venta) {
            throw new Exception("Error al preparar consulta de venta: " . $conexion->error);
        }
        $stmt_venta->bind_param("dsss", $total_venta, $tipo_pago, $estado, $fecha_venta);
    } else {
        $sql_venta = "INSERT INTO ventas (id_cliente, total, tipo_pago, estado, fecha_venta) 
                      VALUES (?, ?, ?, ?, ?)";
        $stmt_venta = $conexion->prepare($sql_venta);
        if (!$stmt_venta) {
            throw new Exception("Error al preparar consulta de venta: " . $conexion->error);
        }
        $stmt_venta->bind_param("idsss", $id_cliente, $total_venta, $tipo_pago, $estado, $fecha_venta);
    }
    
    if (!$stmt_venta->execute()) {
        throw new Exception("Error al insertar la venta: " . $stmt_venta->error);
    }

    $id_venta = $conexion->insert_id;

    // Actualizar el total_compras del cliente si se asoció un cliente
    if ($id_cliente && $id_cliente !== '') {
        $sql_update_cliente = "UPDATE clientes SET total_compras = total_compras + 1 WHERE id = ?";
        $stmt_update_cliente = $conexion->prepare($sql_update_cliente);
        if (!$stmt_update_cliente) {
            throw new Exception("Error al preparar actualización de cliente: " . $conexion->error);
        }
        $stmt_update_cliente->bind_param("i", $id_cliente);
        
        if (!$stmt_update_cliente->execute()) {
            throw new Exception("Error al actualizar total_compras del cliente: " . $stmt_update_cliente->error);
        }
    }

    // Insertar los detalles de la venta y actualizar stock
    foreach ($productos as $producto) {
        $id_producto = (int)$producto['id']; // Usar 'id' del JSON
        $cantidad = (int)$producto['cantidad'];
        $precio_unitario = (float)$producto['precio_unitario'];
        $subtotal = $cantidad * $precio_unitario;

        // Verificar stock disponible
        $sql_stock = "SELECT stock FROM productos WHERE id = ?";
        $stmt_stock = $conexion->prepare($sql_stock);
        if (!$stmt_stock) {
            throw new Exception("Error al preparar consulta de stock: " . $conexion->error);
        }
        $stmt_stock->bind_param("i", $id_producto);
        $stmt_stock->execute();
        $result_stock = $stmt_stock->get_result();
        $producto_stock = $result_stock->fetch_assoc();

        if (!$producto_stock) {
            throw new Exception("Producto no encontrado (ID: $id_producto).");
        }

        if ($producto_stock['stock'] < $cantidad) {
            throw new Exception("Stock insuficiente para el producto '" . ($producto['nombre'] ?? 'ID: ' . $id_producto) . "'. Stock disponible: {$producto_stock['stock']}. Solicitado: {$cantidad}.");
        }

        // Insertar detalle de venta
        $sql_detalle = "INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal) 
                        VALUES (?, ?, ?, ?, ?)";
        $stmt_detalle = $conexion->prepare($sql_detalle);
        if (!$stmt_detalle) {
            throw new Exception("Error al preparar consulta de detalle: " . $conexion->error);
        }
        $stmt_detalle->bind_param("iiidd", $id_venta, $id_producto, $cantidad, $precio_unitario, $subtotal);
        
        if (!$stmt_detalle->execute()) {
            throw new Exception("Error al insertar detalle de venta para producto ID: $id_producto: " . $stmt_detalle->error);
        }

        // Actualizar stock del producto
        $sql_update_stock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
        $stmt_update_stock = $conexion->prepare($sql_update_stock);
        if (!$stmt_update_stock) {
            throw new Exception("Error al preparar actualización de stock: " . $conexion->error);
        }
        $stmt_update_stock->bind_param("ii", $cantidad, $id_producto);
        
        if (!$stmt_update_stock->execute()) {
            throw new Exception("Error al actualizar stock para producto ID: $id_producto: " . $stmt_update_stock->error);
        }
    }

    // Confirmar la transacción
    $conexion->commit();

    // Enviar respuesta de éxito JSON
    sendJsonResponse(true, 'Venta registrada exitosamente!');

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    if ($conexion->connect_errno == 0) {
        $conexion->rollback();
    }
    
    // Enviar respuesta de error JSON
    sendJsonResponse(false, 'Error al registrar la venta: ' . $e->getMessage());

} finally {
    // Cerrar las conexiones
    if (isset($stmt_venta)) $stmt_venta->close();
    if (isset($stmt_update_cliente)) $stmt_update_cliente->close();
    if (isset($stmt_detalle)) $stmt_detalle->close();
    if (isset($stmt_stock)) $stmt_stock->close();
    if (isset($stmt_update_stock)) $stmt_update_stock->close();
    $conexion->close();
}
?> 