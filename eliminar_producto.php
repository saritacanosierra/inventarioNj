<?php
require 'conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conexion->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: listar_producto.php?mensaje=eliminado");
        exit();
    } else {
        die('Error al eliminar el producto: ' . $conexion->error);
    }

    $stmt->close();
} else {
    die("ID de producto no proporcionado.");
}

$conexion->close();
?>