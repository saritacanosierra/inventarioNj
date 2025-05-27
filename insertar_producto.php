<?php
header('Content-Type: application/json');
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $precio = $_POST['precio'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $foto = $_POST['foto'] ?? '';

    // Validar que todos los campos requeridos estén presentes
    if (empty($codigo) || empty($nombre) || empty($precio) || empty($stock) || empty($foto)) {
        echo json_encode(['error' => 'Todos los campos son requeridos']);
        exit;
    }

    // Verificar si ya existe un producto con el mismo código
    $sql_check = "SELECT id FROM productos WHERE codigo = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("s", $codigo);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo json_encode(['error' => 'Ya existe un producto con ese código']);
        exit;
    }

    // Insertar el nuevo producto
    $sql = "INSERT INTO productos (codigo, nombre, precio, stock, foto) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssss", $codigo, $nombre, $precio, $stock, $foto);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Producto agregado correctamente',
            'producto' => [
                'id' => $stmt->insert_id,
                'codigo' => $codigo,
                'nombre' => $nombre,
                'precio' => $precio,
                'stock' => $stock,
                'foto' => $foto
            ]
        ]);
    } else {
        echo json_encode(['error' => 'Error al agregar el producto: ' . $conexion->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Método no permitido']);
}

$conexion->close();
?>