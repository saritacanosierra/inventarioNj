<?php
require_once __DIR__ . '/../../conexion.php';
session_start();

// Habilitar reporte de errores para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log para debugging
error_log('insertar_cliente_venta.php - Iniciando proceso - ' . date('Y-m-d H:i:s'));
error_log('Session data: ' . print_r($_SESSION, true));

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    error_log('insertar_cliente_venta.php - Usuario no autorizado - Session data: ' . print_r($_SESSION, true));
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

// Verificar que la conexión esté activa
if (!$conexion || $conexion->connect_error) {
    error_log('insertar_cliente_venta.php - Error de conexión a la base de datos');
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit();
}

// Log para debugging
error_log('insertar_cliente_venta.php - POST recibido: ' . json_encode($_POST));

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
    // Verificar si el cliente ya existe
    $stmt = $conexion->prepare("SELECT id FROM clientes WHERE cedula = ?");
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Ya existe un cliente con esta cédula']);
        exit();
    }

    // Insertar nuevo cliente
    $sql = "INSERT INTO clientes (nombre, cedula, celular, direccion, fecha_registro, total_compras) 
            VALUES (?, ?, ?, ?, NOW(), 1)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $cedula, $celular, $direccion);
    
    if ($stmt->execute()) {
        $id_cliente = $conexion->insert_id;
        
        // Obtener el cliente recién creado
        $stmt = $conexion->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $cliente = $stmt->get_result()->fetch_assoc();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Cliente agregado exitosamente',
            'cliente_id' => $cliente['id'],
            'nombre' => $cliente['nombre'],
            'cedula' => $cliente['cedula'],
            'celular' => $cliente['celular']
        ]);
    } else {
        throw new Exception("Error al registrar el cliente");
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conexion->close();
?> 