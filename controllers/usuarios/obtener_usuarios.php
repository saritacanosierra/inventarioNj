<?php
require '../../conexion.php';

header('Content-Type: application/json');

try {
    // Obtener todos los usuarios ordenados por nombre
    $sql = "SELECT id, cedula, nombre, apellido, usuario, email FROM usuarios ORDER BY nombre";
    $resultado = $conexion->query($sql);

    if (!$resultado) {
        throw new Exception('Error al obtener los usuarios: ' . $conexion->error);
    }

    $usuarios = [];
    while ($usuario = $resultado->fetch_assoc()) {
        $usuarios[] = [
            'id' => (int)$usuario['id'],
            'cedula' => $usuario['cedula'],
            'nombre' => $usuario['nombre'],
            'apellido' => $usuario['apellido'],
            'usuario' => $usuario['usuario'],
            'email' => $usuario['email']
        ];
    }

    // Registrar en el log para depuración
    error_log('Usuarios encontrados: ' . count($usuarios));
    error_log('SQL ejecutado: ' . $sql);
    error_log('Usuarios: ' . json_encode($usuarios));

    // Verificar si hay usuarios
    if (empty($usuarios)) {
        error_log('No se encontraron usuarios en la base de datos');
        
        // Verificar si la tabla existe
        $sql_check = "SHOW TABLES LIKE 'usuarios'";
        $result_check = $conexion->query($sql_check);
        if ($result_check->num_rows === 0) {
            error_log('La tabla usuarios no existe');
        } else {
            // Si la tabla existe pero no hay datos, mostrar la estructura
            $sql_structure = "DESCRIBE usuarios";
            $result_structure = $conexion->query($sql_structure);
            error_log('Estructura de la tabla usuarios:');
            while ($row = $result_structure->fetch_assoc()) {
                error_log(json_encode($row));
            }
        }
    }

    // Devolver los usuarios
    echo json_encode([
        'success' => true,
        'usuarios' => $usuarios,
        'total' => count($usuarios)
    ]);

} catch (Exception $e) {
    error_log('Error en obtener_usuarios.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => true,
        'message' => $e->getMessage()
    ]);
}

$conexion->close();
?> 