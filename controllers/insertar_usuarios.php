<?php
require '../conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    $email = $_POST['email'];

    // Verificar si el usuario ya existe
    $sql_check = "SELECT id FROM usuarios WHERE usuario = ? OR email = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("ss", $usuario, $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'El usuario o email ya existe'
        ]);
        exit;
    }

    // Insertar el nuevo usuario
    $sql = "INSERT INTO usuarios (cedula, nombre, apellido, usuario, contrasena, email) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssss", $cedula, $nombre, $apellido, $usuario, $contrasena, $email);

    if ($stmt->execute()) {
        $id = $conexion->insert_id;
        echo json_encode([
            'success' => true,
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
            'message' => 'Error al insertar el usuario: ' . $conexion->error
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