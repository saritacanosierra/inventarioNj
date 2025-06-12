<?php
require '../conexion.php';

header('Content-Type: application/json');

try {
    // Verificar que la conexión esté activa
    if (!$conexion) {
        throw new Exception('Error de conexión a la base de datos');
    }

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
        $uploadDir = '../uploads/productos/';
        
        // Crear el directorio si no existe
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new Exception('No se pudo crear el directorio para las imágenes');
            }
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

    // Verificar si el código ya existe
    $sql_check = "SELECT id FROM productos WHERE codigo = ?";
    $stmt_check = $conexion->prepare($sql_check);
    if (!$stmt_check) {
        throw new Exception('Error al preparar la consulta de verificación: ' . $conexion->error);
    }
    
    $stmt_check->bind_param("s", $codigo);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        throw new Exception('El código ya existe para otro producto');
    }
    $stmt_check->close();

    // Insertar en la base de datos
    $sql = "INSERT INTO productos (codigo, nombre, precio, stock, foto, id_categoria) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta de inserción: ' . $conexion->error);
    }

    $id_categoria = !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : null;
    $stmt->bind_param("ssdisi", $codigo, $nombre, $precio, $stock, $foto, $id_categoria);

    if (!$stmt->execute()) {
        throw new Exception('Error al insertar el producto en la base de datos: ' . $stmt->error);
    }

    $id = $conexion->insert_id;
    if (!$id) {
        throw new Exception('Error al obtener el ID del producto insertado');
    }

    // Obtener la información de la categoría
    $categoria = null;
    if ($id_categoria) {
        $sql_categoria = "SELECT c.nombre as categoria, c.codigo as codigo_categoria, c.ubicacion 
                         FROM categorias c 
                         WHERE c.id = ?";
        $stmt_categoria = $conexion->prepare($sql_categoria);
        $stmt_categoria->bind_param("i", $id_categoria);
        $stmt_categoria->execute();
        $result_categoria = $stmt_categoria->get_result();
        $categoria = $result_categoria->fetch_assoc();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Producto agregado exitosamente',
        'id' => $id,
        'codigo' => $codigo,
        'nombre' => $nombre,
        'precio' => $precio,
        'stock' => $stock,
        'foto' => $foto,
        'id_categoria' => $id_categoria,
        'categoria' => $categoria ? $categoria['categoria'] : null,
        'codigo_categoria' => $categoria ? $categoria['codigo_categoria'] : null,
        'ubicacion' => $categoria ? $categoria['ubicacion'] : null
    ]);

} catch (Exception $e) {
    error_log('Error en insertar_producto.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conexion->close();
?>