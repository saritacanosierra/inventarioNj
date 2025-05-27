<?php
require 'conexion.php';

header('Content-Type: application/json');

try {
    // Validar que todos los campos requeridos estén presentes
    if (empty($_POST['codigo']) || empty($_POST['nombre']) || empty($_POST['precio']) || empty($_POST['stock'])) {
        throw new Exception('Todos los campos son requeridos');
    }

    // Validar que precio y stock sean números válidos
    if (!is_numeric($_POST['precio']) || floatval($_POST['precio']) <= 0) {
        throw new Exception('El precio debe ser un número válido mayor a 0');
    }

    if (!is_numeric($_POST['stock']) || intval($_POST['stock']) < 0) {
        throw new Exception('El stock debe ser un número válido mayor o igual a 0');
    }

    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $foto = '';

    // Procesar la imagen si se subió una
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/productos/';
        
        // Crear el directorio si no existe
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExtension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception('Solo se permiten archivos de imagen (JPG, JPEG, PNG, GIF)');
        }

        // Generar un nombre único para el archivo
        $foto = uniqid() . '.' . $fileExtension;
        $uploadFile = $uploadDir . $foto;

        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $uploadFile)) {
            throw new Exception('Error al subir la imagen');
        }
    }

    // Insertar en la base de datos
    $sql = "INSERT INTO productos (codigo, nombre, precio, stock, foto) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssdis", $codigo, $nombre, $precio, $stock, $foto);

    if (!$stmt->execute()) {
        throw new Exception('Error al insertar el producto: ' . $stmt->error);
    }

    echo json_encode(['success' => true, 'message' => 'Producto agregado exitosamente']);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$conexion->close();
?>