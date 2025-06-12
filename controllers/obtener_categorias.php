<?php
require '../conexion.php';

header('Content-Type: application/json');

try {
    // Obtener todas las categorías ordenadas por nombre
    $sql = "SELECT id, codigo, nombre FROM categoria ORDER BY nombre";
    $resultado = $conexion->query($sql);

    if (!$resultado) {
        throw new Exception('Error al obtener las categorías: ' . $conexion->error);
    }

    $categorias = [];
    while ($categoria = $resultado->fetch_assoc()) {
        $categorias[] = [
            'id' => (int)$categoria['id'],
            'codigo' => $categoria['codigo'],
            'nombre' => $categoria['nombre']
        ];
    }

    // Registrar en el log para depuración
    error_log('Categorías encontradas: ' . count($categorias));
    error_log('SQL ejecutado: ' . $sql);
    error_log('Categorías: ' . json_encode($categorias));

    // Verificar si hay categorías
    if (empty($categorias)) {
        error_log('No se encontraron categorías en la base de datos');
        
        // Verificar si la tabla existe
        $sql_check = "SHOW TABLES LIKE 'categoria'";
        $result_check = $conexion->query($sql_check);
        if ($result_check->num_rows === 0) {
            error_log('La tabla categoria no existe');
        } else {
            // Si la tabla existe pero no hay datos, mostrar la estructura
            $sql_structure = "DESCRIBE categoria";
            $result_structure = $conexion->query($sql_structure);
            error_log('Estructura de la tabla categoria:');
            while ($row = $result_structure->fetch_assoc()) {
                error_log(json_encode($row));
            }
        }
    }

    // Devolver las categorías
    echo json_encode([
        'success' => true,
        'categorias' => $categorias,
        'total' => count($categorias)
    ]);

} catch (Exception $e) {
    error_log('Error en obtener_categorias.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => true,
        'message' => $e->getMessage()
    ]);
}

$conexion->close();
?> 