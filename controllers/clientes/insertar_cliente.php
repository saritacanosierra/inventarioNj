<?php
require_once __DIR__ . '/../../conexion.php';
session_start();

// Habilitar reporte de errores para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log para debugging
error_log('insertar_cliente.php - Iniciando proceso - ' . date('Y-m-d H:i:s'));

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    error_log('insertar_cliente.php - Usuario no autorizado');
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

// Verificar que la conexión esté activa
if (!$conexion || $conexion->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit();
}

// Log para debugging
error_log('insertar_cliente.php - POST recibido: ' . json_encode($_POST));

// Obtener y validar datos
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$cedula = isset($_POST['cedula']) ? trim($_POST['cedula']) : '';
$celular = isset($_POST['celular']) ? trim($_POST['celular']) : '';
$direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';

// Validar campos requeridos
if (empty($nombre) || empty($cedula) || empty($celular) || empty($direccion)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
    exit();
}

try {
    // Verificar si la cédula ya existe
    $sql_check = "SELECT id FROM clientes WHERE cedula = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("s", $cedula);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows > 0) {
        // Cliente existe, actualizar sus datos
        $cliente = $result_check->fetch_assoc();
        $sql_update = "UPDATE clientes SET nombre = ?, celular = ?, direccion = ? WHERE id = ?";
        $stmt_update = $conexion->prepare($sql_update);
        $stmt_update->bind_param("sssi", $nombre, $celular, $direccion, $cliente['id']);
        
        if ($stmt_update->execute()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'message' => 'Cliente actualizado exitosamente',
                'cliente_id' => $cliente['id'],
                'nombre' => $nombre,
                'cedula' => $cedula
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error al actualizar cliente: ' . $stmt_update->error]);
        }
    } else {
        // Insertar nuevo cliente
        $sql = "INSERT INTO clientes (nombre, cedula, celular, direccion, fecha_registro, total_compras) 
                VALUES (?, ?, ?, ?, NOW(), 1)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $cedula, $celular, $direccion);
        
        if ($stmt->execute()) {
            $id_cliente = $conexion->insert_id;
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'message' => 'Cliente agregado exitosamente',
                'cliente_id' => $id_cliente,
                'nombre' => $nombre,
                'cedula' => $cedula
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error al agregar cliente: ' . $stmt->error]);
        }
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conexion->close();
?> 