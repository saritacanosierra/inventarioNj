<?php
require '../conexion.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Validar que todos los campos requeridos estén presentes
    if (empty($_POST['codigo']) || empty($_POST['nombre']) || empty($_POST['ubicacion'])) {
        throw new Exception('Todos los campos son requeridos');
    }

    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];
    $ubicacion = $_POST['ubicacion'];

    // Verificar si el código ya existe
    $sql_check = "SELECT id FROM categoria WHERE codigo = ?";
    $stmt_check = $conexion->prepare($sql_check);
    if (!$stmt_check) {
        throw new Exception('Error al preparar la consulta de verificación: ' . $conexion->error);
    }
    
    $stmt_check->bind_param("s", $codigo);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        throw new Exception('El código ya existe para otra categoría');
    }
    $stmt_check->close();

    // Insertar la nueva categoría
    $sql = "INSERT INTO categoria (codigo, nombre, ubicacion) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta de inserción: ' . $conexion->error);
    }

    $stmt->bind_param("sss", $codigo, $nombre, $ubicacion);

    if (!$stmt->execute()) {
        throw new Exception('Error al insertar la categoría: ' . $stmt->error);
    }

    $id = $conexion->insert_id;
    if (!$id) {
        throw new Exception('Error al obtener el ID de la categoría insertada');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Categoría agregada correctamente',
        'id' => $id,
        'codigo' => $codigo,
        'nombre' => $nombre,
        'ubicacion' => $ubicacion
    ]);

} catch (Exception $e) {
    error_log('Error en procesar_categoria.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conexion->close();
?> 