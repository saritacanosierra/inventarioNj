<?php
require 'conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conexion->prepare("DELETE FROM categoria WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: listar_categorias.php?mensaje=eliminado");
        exit();
    } else {
        die('Error al eliminar la categoria: ' . $conexion->error);
    }

    $stmt->close();
} else {
    die("ID de categoria no proporcionado.");
}

$conexion->close();
?>