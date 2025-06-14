<?php
require_once __DIR__ . '/../../conexion.php';
require_once __DIR__ . '/../envios/envios.php';
session_start();

// Habilitar reporte de errores para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log para debugging
error_log('insertar_cliente_venta.php - Iniciando proceso - ' . date('Y-m-d H:i:s'));

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    error_log('insertar_cliente_venta.php - Usuario no autorizado');
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
    // Crear instancia del manager de envíos
    $enviosManager = new EnviosManager($conexion);
    
    // Procesar el cliente usando el manager (sin asociar a venta, devolver JSON)
    $resultado = $enviosManager->procesarCliente($_POST, null, true);
    
    if ($resultado['success']) {
        // Obtener el cliente por cédula para devolver sus datos
        $cliente = $enviosManager->obtenerClientePorCedula($cedula);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => $resultado['message'],
            'cliente_id' => $cliente['id'],
            'nombre' => $cliente['nombre'],
            'cedula' => $cliente['cedula']
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $resultado['message']]);
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conexion->close();
?> 