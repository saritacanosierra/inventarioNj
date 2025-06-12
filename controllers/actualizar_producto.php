<?php
require '../conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $id_categoria = !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : null;
    $foto_actual = $_POST['foto_actual'];
    
    // Manejar la subida de la nueva imagen
    $foto = $foto_actual; // Por defecto, mantener la foto actual
    
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $directorio = "../uploads/productos/";
        
        // Crear el directorio si no existe
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        
        // Generar nombre único para la nueva imagen
        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nuevo_nombre = uniqid() . '.' . $extension;
        $ruta_completa = $directorio . $nuevo_nombre;
        
        // Mover la nueva imagen
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_completa)) {
            // Eliminar la imagen anterior si existe
            if (!empty($foto_actual) && file_exists($directorio . $foto_actual)) {
                unlink($directorio . $foto_actual);
            }
            $foto = $nuevo_nombre;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al subir la nueva imagen'
            ]);
            exit;
        }
    }

    // Verificar si el código ya existe para otro producto
    $sql_check = "SELECT id FROM productos WHERE codigo = ? AND id != ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("si", $codigo, $id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'El código ya existe para otro producto'
        ]);
        exit;
    }

    // Actualizar el producto
    $stmt = $conexion->prepare("UPDATE productos SET codigo = ?, nombre = ?, precio = ?, stock = ?, foto = ?, id_categoria = ? WHERE id = ?");
    $stmt->bind_param("sssssii", $codigo, $nombre, $precio, $stock, $foto, $id_categoria, $id);

    if ($stmt->execute()) {
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
            'message' => 'Producto actualizado correctamente',
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
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar el producto: ' . $conexion->error
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