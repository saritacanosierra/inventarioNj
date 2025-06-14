<?php
require_once __DIR__ . '/../../conexion.php';
session_start();

// Habilitar reporte de errores para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurar headers para JSON
header('Content-Type: application/json');

// Log para debugging
error_log('insertar_cliente_venta.php - Iniciando proceso - ' . date('Y-m-d H:i:s'));
error_log('insertar_cliente_venta.php - SESSION: ' . json_encode($_SESSION));
error_log('insertar_cliente_venta.php - POST: ' . json_encode($_POST));
error_log('insertar_cliente_venta.php - SERVER: ' . json_encode($_SERVER));

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    error_log('insertar_cliente_venta.php - Usuario no autorizado. SESSION: ' . json_encode($_SESSION));
    echo json_encode(['success' => false, 'message' => 'No autorizado - Sesión no iniciada']);
    exit();
}

// Verificar que la conexión esté activa
if (!$conexion || $conexion->connect_error) {
    error_log('insertar_cliente_venta.php - Error de conexión a la base de datos');
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit();
}

// Obtener y validar datos
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$cedula = isset($_POST['cedula']) ? trim($_POST['cedula']) : '';
$celular = isset($_POST['celular']) ? trim($_POST['celular']) : '';
$direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';

// Validar campos requeridos
if (empty($nombre) || empty($cedula) || empty($celular) || empty($direccion)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
    exit();
}

try {
    // Verificar si el cliente ya existe
    $stmt = $conexion->prepare("SELECT id FROM clientes WHERE cedula = ?");
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conexion->error);
    }
    
    $stmt->bind_param("s", $cedula);
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Ya existe un cliente con esta cédula']);
        exit();
    }

    // Insertar nuevo cliente
    $sql = "INSERT INTO clientes (nombre, cedula, celular, direccion, fecha_registro, total_compras) 
            VALUES (?, ?, ?, ?, NOW(), 1)";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error al preparar la inserción: " . $conexion->error);
    }
    
    $stmt->bind_param("ssss", $nombre, $cedula, $celular, $direccion);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al registrar el cliente: " . $stmt->error);
    }
    
    $id_cliente = $conexion->insert_id;
    
    // Obtener el cliente recién creado
    $stmt = $conexion->prepare("SELECT * FROM clientes WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta del cliente: " . $conexion->error);
    }
    
    $stmt->bind_param("i", $id_cliente);
    if (!$stmt->execute()) {
        throw new Exception("Error al obtener el cliente: " . $stmt->error);
    }
    
    $cliente = $stmt->get_result()->fetch_assoc();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Cliente agregado exitosamente',
        'cliente' => [
            'id' => $cliente['id'],
            'nombre' => $cliente['nombre'],
            'cedula' => $cliente['cedula'],
            'celular' => $cliente['celular'],
            'direccion' => $cliente['direccion']
        ]
    ]);
    
} catch (Exception $e) {
    error_log('insertar_cliente_venta.php - Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conexion->close();
?> 