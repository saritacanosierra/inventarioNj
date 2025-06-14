<?php
require_once __DIR__ . '/../../conexion.php';

class EnviosManager {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    /**
     * Modifica la tabla ventas para agregar la relación con clientes
     */
    public function modificarTablaVentas() {
        $sql_modificar_ventas = "ALTER TABLE ventas 
            ADD COLUMN id_cliente INT(11) NULL AFTER id,
            ADD FOREIGN KEY (id_cliente) REFERENCES clientes(id)";
        
        try {
            $this->conexion->query($sql_modificar_ventas);
            return ['success' => true, 'message' => 'Tabla ventas modificada exitosamente'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al modificar la tabla ventas: ' . $e->getMessage()];
        }
    }
    
    /**
     * Procesa el formulario de cliente
     */
    public function procesarCliente($datos, $id_venta = null, $returnJson = false) {
        $nombre = trim($datos['nombre']);
        $cedula = trim($datos['cedula']);
        $celular = trim($datos['celular']);
        $direccion = trim($datos['direccion']);
        
        // Validar que los campos no estén vacíos
        if (empty($nombre) || empty($cedula) || empty($celular) || empty($direccion)) {
            if ($returnJson) {
                return ['success' => false, 'message' => 'Todos los campos son obligatorios'];
            }
            return ['success' => false, 'message' => 'Todos los campos son obligatorios'];
        }
        
        // Verificar si la cédula ya existe
        $sql_check = "SELECT id, total_compras FROM clientes WHERE cedula = ?";
        $stmt_check = $this->conexion->prepare($sql_check);
        $stmt_check->bind_param("s", $cedula);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            // Cliente existe, actualizar sus datos y total_compras
            return $this->actualizarClienteExistente($nombre, $cedula, $celular, $direccion, $result_check->fetch_assoc(), $id_venta, $returnJson);
        } else {
            // Insertar el nuevo cliente
            return $this->insertarNuevoCliente($nombre, $cedula, $celular, $direccion, $id_venta, $returnJson);
        }
    }
    
    /**
     * Actualiza un cliente existente
     */
    private function actualizarClienteExistente($nombre, $cedula, $celular, $direccion, $cliente, $id_venta = null, $returnJson = false) {
        $nuevo_total = $cliente['total_compras'] + 1;
        
        $sql_update = "UPDATE clientes SET 
                      nombre = ?, 
                      celular = ?, 
                      direccion = ?, 
                      total_compras = ? 
                      WHERE id = ?";
        $stmt_update = $this->conexion->prepare($sql_update);
        $stmt_update->bind_param("sssii", $nombre, $celular, $direccion, $nuevo_total, $cliente['id']);
        
        if ($stmt_update->execute()) {
            // Si hay un ID de venta, actualizar la venta con el ID del cliente
            if ($id_venta) {
                $this->asociarClienteAVenta($cliente['id'], $id_venta);
                if ($returnJson) {
                    return ['success' => true, 'message' => 'Cliente actualizado y asociado a la venta exitosamente', 'redirect' => 'ventas.php'];
                } else {
                    return ['success' => true, 'message' => 'Cliente actualizado y asociado a la venta exitosamente', 'redirect' => 'ventas.php'];
                }
            } else {
                if ($returnJson) {
                    return ['success' => true, 'message' => 'Cliente actualizado exitosamente', 'redirect' => 'envios.php'];
                } else {
                    return ['success' => true, 'message' => 'Cliente actualizado exitosamente', 'redirect' => 'envios.php'];
                }
            }
        } else {
            return ['success' => false, 'message' => 'Error al actualizar cliente: ' . $stmt_update->error];
        }
    }
    
    /**
     * Inserta un nuevo cliente
     */
    private function insertarNuevoCliente($nombre, $cedula, $celular, $direccion, $id_venta = null, $returnJson = false) {
        $sql = "INSERT INTO clientes (nombre, cedula, celular, direccion, fecha_registro, total_compras) 
                VALUES (?, ?, ?, ?, NOW(), 1)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $cedula, $celular, $direccion);
        
        if ($stmt->execute()) {
            $id_cliente = $this->conexion->insert_id;
            
            // Si hay un ID de venta, actualizar la venta con el ID del cliente
            if ($id_venta) {
                $this->asociarClienteAVenta($id_cliente, $id_venta);
                if ($returnJson) {
                    return ['success' => true, 'message' => 'Cliente agregado y asociado a la venta exitosamente', 'redirect' => 'ventas.php'];
                } else {
                    return ['success' => true, 'message' => 'Cliente agregado y asociado a la venta exitosamente', 'redirect' => 'ventas.php'];
                }
            } else {
                if ($returnJson) {
                    return ['success' => true, 'message' => 'Cliente agregado exitosamente', 'redirect' => 'envios.php'];
                } else {
                    return ['success' => true, 'message' => 'Cliente agregado exitosamente', 'redirect' => 'envios.php'];
                }
            }
        } else {
            return ['success' => false, 'message' => 'Error al agregar cliente: ' . $stmt->error];
        }
    }
    
    /**
     * Asocia un cliente a una venta
     */
    private function asociarClienteAVenta($id_cliente, $id_venta) {
        $sql_actualizar = "UPDATE ventas SET id_cliente = ? WHERE id = ?";
        $stmt_actualizar = $this->conexion->prepare($sql_actualizar);
        $stmt_actualizar->bind_param("ii", $id_cliente, $id_venta);
        $stmt_actualizar->execute();
        $stmt_actualizar->close();
    }
    
    /**
     * Obtiene todos los clientes
     */
    public function obtenerClientes() {
        $sql = "SELECT * FROM clientes ORDER BY nombre";
        $resultado = $this->conexion->query($sql);
        return $resultado;
    }
    
    /**
     * Obtiene un cliente por ID
     */
    public function obtenerClientePorId($id) {
        $sql = "SELECT * FROM clientes WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
    
    /**
     * Obtiene un cliente por cédula
     */
    public function obtenerClientePorCedula($cedula) {
        $sql = "SELECT * FROM clientes WHERE cedula = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $cedula);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
    
    /**
     * Actualiza un cliente existente
     */
    public function actualizarCliente($id, $nombre, $cedula, $celular, $direccion) {
        // Verificar si la cédula ya existe en otro cliente
        $sql_check = "SELECT id FROM clientes WHERE cedula = ? AND id != ?";
        $stmt_check = $this->conexion->prepare($sql_check);
        $stmt_check->bind_param("si", $cedula, $id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            return ['success' => false, 'message' => 'Ya existe un cliente con esa cédula'];
        }
        
        $sql_update = "UPDATE clientes SET nombre = ?, cedula = ?, celular = ?, direccion = ? WHERE id = ?";
        $stmt_update = $this->conexion->prepare($sql_update);
        $stmt_update->bind_param("ssssi", $nombre, $cedula, $celular, $direccion, $id);
        
        if ($stmt_update->execute()) {
            return ['success' => true, 'message' => 'Cliente actualizado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar cliente: ' . $stmt_update->error];
        }
    }
}

// Si se llama directamente este archivo, procesar la lógica
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    $enviosManager = new EnviosManager($conexion);
    
    // Obtener el ID de la venta si existe
    $id_venta = isset($_GET['id_venta']) ? intval($_GET['id_venta']) : null;
    
    // Procesar el formulario
    $resultado = $enviosManager->procesarCliente($_POST, $id_venta);
    
    // Redireccionar con el resultado
    if ($resultado['success']) {
        header("Location: ../pages/" . $resultado['redirect'] . "?mensaje=" . urlencode($resultado['message']));
    } else {
        header("Location: ../pages/envios.php?error=" . urlencode($resultado['message']));
    }
    exit;
}

// Si se accede desde envios.php, procesar el formulario
if (isset($_POST['nombre']) && isset($_POST['cedula']) && isset($_POST['celular']) && isset($_POST['direccion']) && !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    $enviosManager = new EnviosManager($conexion);
    
    // Obtener el ID de la venta si existe
    $id_venta = isset($_GET['id_venta']) ? intval($_GET['id_venta']) : null;
    
    // Procesar el formulario
    $resultado = $enviosManager->procesarCliente($_POST, $id_venta);
    
    // Redireccionar con el resultado
    if ($resultado['success']) {
        header("Location: " . $resultado['redirect'] . "?mensaje=" . urlencode($resultado['message']));
    } else {
        header("Location: envios.php?error=" . urlencode($resultado['message']));
    }
    exit;
}
?> 