<?php
require_once __DIR__ . '/../../conexion.php';
require_once __DIR__ . '/../envios/envios.php';

// Log para depuración
error_log('actualizar_cliente.php - Iniciando proceso de actualización');

// Verificar si se proporcionó un ID
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    error_log('actualizar_cliente.php - ID no válido: ' . ($_POST['id'] ?? 'no definido'));
    echo "<script>
        alert('ID de cliente no válido');
        window.location.href = '../../pages/envios.php';
    </script>";
    exit;
}

$id_cliente = intval($_POST['id']);
$nombre = trim($_POST['nombre'] ?? '');
$cedula = trim($_POST['cedula'] ?? '');
$celular = trim($_POST['celular'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');

error_log('actualizar_cliente.php - Datos recibidos - ID: ' . $id_cliente . ', Nombre: ' . $nombre . ', Cédula: ' . $cedula);

// Validar que todos los campos estén completos
if (empty($nombre) || empty($cedula) || empty($celular) || empty($direccion)) {
    error_log('actualizar_cliente.php - Campos incompletos');
    echo "<script>
        alert('Todos los campos son obligatorios');
        window.location.href = '../../pages/envios.php';
    </script>";
    exit;
}

try {
    // Crear instancia del manager de envíos
    $enviosManager = new EnviosManager($conexion);
    error_log('actualizar_cliente.php - Manager de envíos creado');
    
    // Actualizar el cliente usando el manager
    $resultado = $enviosManager->actualizarCliente($id_cliente, $nombre, $cedula, $celular, $direccion);
    error_log('actualizar_cliente.php - Resultado de actualización: ' . print_r($resultado, true));
    
    if ($resultado['success']) {
        echo "<script>
            alert('" . $resultado['message'] . "');
            window.location.href = '../../pages/envios.php';
        </script>";
    } else {
        echo "<script>
            alert('" . $resultado['message'] . "');
            window.location.href = '../../pages/envios.php';
        </script>";
    }

} catch (Exception $e) {
    error_log('actualizar_cliente.php - Error: ' . $e->getMessage());
    echo "<script>
        alert('Error inesperado: " . $e->getMessage() . "');
        window.location.href = '../../pages/envios.php';
    </script>";
}

$conexion->close();
error_log('actualizar_cliente.php - Proceso finalizado');
?> 