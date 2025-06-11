<?php
require '../conexion.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Validar que todos los campos requeridos estén presentes
    if (empty($_POST['id']) || empty($_POST['codigo']) || empty($_POST['nombre']) || empty($_POST['ubicacion'])) {
        throw new Exception('Todos los campos son requeridos');
    }

    $id = intval($_POST['id']);
    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];
    $ubicacion = $_POST['ubicacion'];

    // Verificar si el código ya existe para otra categoría
    $sql_check = "SELECT id FROM categoria WHERE codigo = ? AND id != ?";
    $stmt_check = $conexion->prepare($sql_check);
    if (!$stmt_check) {
        throw new Exception('Error al preparar la consulta de verificación: ' . $conexion->error);
    }
    
    $stmt_check->bind_param("si", $codigo, $id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        throw new Exception('El código ya existe para otra categoría');
    }
    $stmt_check->close();

    // Actualizar la categoría
    $sql = "UPDATE categoria SET codigo = ?, nombre = ?, ubicacion = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta de actualización: ' . $conexion->error);
    }

    $stmt->bind_param("sssi", $codigo, $nombre, $ubicacion, $id);

    if (!$stmt->execute()) {
        throw new Exception('Error al actualizar la categoría: ' . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception('No se encontró la categoría a actualizar');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Categoría actualizada correctamente',
        'id' => $id,
        'codigo' => $codigo,
        'nombre' => $nombre,
        'ubicacion' => $ubicacion
    ]);

} catch (Exception $e) {
    error_log('Error en actualizar_categoria.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conexion->close();
?> 