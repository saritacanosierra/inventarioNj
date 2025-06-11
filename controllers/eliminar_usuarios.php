<?php
require '../conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: ../pages/listar_usuarios.php?mensaje=eliminado");
        exit();
    } else {
        die('Error al eliminar el usuario: ' . $conexion->error);
    }

    $stmt->close();
} else {
    die("ID de usuario no proporcionado.");
}

$conexion->close();
?>