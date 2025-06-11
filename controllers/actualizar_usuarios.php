<?php
require '../conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $cedula = $_POST['cedula'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    $email = $_POST['email'];

    // Verificar si el usuario o email ya existe para otro usuario
    $sql_check = "SELECT id FROM usuarios WHERE (usuario = ? OR email = ?) AND id != ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("ssi", $usuario, $email, $id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'El usuario o email ya existe para otro usuario'
        ]);
        exit;
    }

    // Actualizar el usuario
    $stmt = $conexion->prepare("UPDATE usuarios SET cedula = ?, nombre = ?, apellido = ?, usuario = ?, contrasena = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $cedula, $nombre, $apellido, $usuario, $contrasena, $email, $id);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Usuario actualizado correctamente',
            'id' => $id,
            'cedula' => $cedula,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'usuario' => $usuario,
            'contrasena' => $contrasena,
            'email' => $email
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar el usuario: ' . $conexion->error
        ]);
    }

    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}

$conexion->close();
?> 