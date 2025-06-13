<?php
require_once __DIR__ . '/../conexion.php';
session_start();

// Habilitar reporte de errores para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
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
    if (!$stmt) {
        throw new Exception("Error al preparar consulta: " . $conexion->error);
    }
    
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Ya existe un cliente con esta cédula']);
        exit();
    }

    // Insertar nuevo cliente
    $stmt = $conexion->prepare("INSERT INTO clientes (nombre, cedula, celular, direccion, fecha_registro, total_compras) VALUES (?, ?, ?, ?, NOW(), 1)");
    if (!$stmt) {
        throw new Exception("Error al preparar inserción: " . $conexion->error);
    }
    
    $stmt->bind_param("ssss", $nombre, $cedula, $celular, $direccion);
    
    if ($stmt->execute()) {
        $cliente_id = $conexion->insert_id;
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Cliente registrado correctamente',
            'cliente_id' => $cliente_id,
            'nombre' => $nombre,
            'cedula' => $cedula
        ]);
    } else {
        throw new Exception("Error al ejecutar inserción: " . $stmt->error);
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conexion->close();
?> 