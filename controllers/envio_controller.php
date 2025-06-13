<?php
require_once __DIR__ . '/../conexion.php';
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

// Obtener la acción solicitada
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

switch ($action) {
    case 'crear':
        crearCliente();
        break;
    case 'editar':
        editarCliente();
        break;
    case 'eliminar':
        eliminarCliente();
        break;
    default:
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}

function crearCliente() {
    global $conexion;
    
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
        $stmt = $conexion->prepare("INSERT INTO clientes (nombre, cedula, celular, direccion) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $cedula, $celular, $direccion);
        
        if ($stmt->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Cliente registrado correctamente']);
        } else {
            throw new Exception("Error al registrar el cliente");
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function editarCliente() {
    global $conexion;
    
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $cedula = isset($_POST['cedula']) ? trim($_POST['cedula']) : '';
    $celular = isset($_POST['celular']) ? trim($_POST['celular']) : '';
    $direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';

    if ($id <= 0 || empty($nombre) || empty($cedula) || empty($celular) || empty($direccion)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        exit();
    }

    try {
        // Verificar si la cédula ya existe en otro cliente
        $stmt = $conexion->prepare("SELECT id FROM clientes WHERE cedula = ? AND id != ?");
        $stmt->bind_param("si", $cedula, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Ya existe otro cliente con esta cédula']);
            exit();
        }

        // Actualizar cliente
        $stmt = $conexion->prepare("UPDATE clientes SET nombre = ?, cedula = ?, celular = ?, direccion = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nombre, $cedula, $celular, $direccion, $id);
        
        if ($stmt->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Cliente actualizado correctamente']);
        } else {
            throw new Exception("Error al actualizar el cliente");
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function eliminarCliente() {
    global $conexion;
    
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'ID de cliente no válido']);
        exit();
    }

    try {
        // Verificar si el cliente tiene envíos asociados
        $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM envios WHERE cliente_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['total'] > 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No se puede eliminar el cliente porque tiene envíos asociados']);
            exit();
        }

        // Eliminar cliente
        $stmt = $conexion->prepare("DELETE FROM clientes WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Cliente eliminado correctamente']);
        } else {
            throw new Exception("Error al eliminar el cliente");
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}