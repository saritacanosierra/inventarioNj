<?php
require '../../conexion.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Usuario eliminado correctamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al eliminar el usuario: ' . $conexion->error
        ]);
    }

    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID de usuario no proporcionado.'
    ]);
}

$conexion->close();
?>