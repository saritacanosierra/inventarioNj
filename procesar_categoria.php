<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];
    $ubicacion = $_POST['ubicacion'];

    $sql = "INSERT INTO categoria (codigo, nombre, ubicacion) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sss", $codigo, $nombre, $ubicacion);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Categoría agregada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar la categoría: ' . $conexion->error]);
    }

    $stmt->close();
    $conexion->close();
    exit;
}
?> 