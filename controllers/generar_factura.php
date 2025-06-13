<?php
require_once '../conexion.php';

// Obtener el ID de la venta
$id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_venta <= 0) {
    die('ID de venta inválido');
}

try {
    // Obtener información de la venta
    $sql = "SELECT v.*, p.nombre as nombre_producto, p.codigo as codigo_producto,
            DATE_FORMAT(v.fecha_venta, '%d/%m/%Y %H:%i') as fecha_venta_formateada,
            c.nombre as nombre_cliente, c.cedula as cedula_cliente, c.direccion as direccion_cliente
            FROM ventas v
            JOIN productos p ON v.id_producto = p.id
            LEFT JOIN clientes c ON v.id_cliente = c.id
            WHERE v.id = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_venta);
    $stmt->execute();
    $venta = $stmt->get_result()->fetch_assoc();

    if (!$venta) {
        die('Venta no encontrada');
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #<?php echo $venta['id']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .factura {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #E1B8E2;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo-container {
            flex: 0 0 200px;
            text-align: left;
        }
        .logo {
            max-width: 150px;
            height: auto;
        }
        .header-info {
            flex: 1;
            text-align: right;
        }
        .header h1 {
            color: #333;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .detalles {
            margin-bottom: 30px;
        }
        .detalles h2 {
            color: #333;
            font-size: 18px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
        }
        .tipo-pago {
            margin: 20px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .tipo-pago strong {
            color: #333;
        }
        .btn-imprimir {
            background-color: #17a2b8;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 20px;
        }
        .btn-imprimir:hover {
            background-color: #138496;
        }
        .material-icons {
            font-size: 18px;
        }
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            .factura {
                box-shadow: none;
                padding: 20px;
            }
            .btn-imprimir {
                display: none;
            }
        }
        .cliente-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid #E1B8E2;
        }

        .cliente-info h3 {
            color: #333;
            margin: 0 0 10px 0;
            font-size: 16px;
        }

        .cliente-info p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="factura">
        <div class="header">
            <div class="logo-container">
                <img src="/inventarioNj/img/logo (40).png" alt="Logo" class="logo">
            </div>
            <div class="header-info">
                <h1>FACTURA</h1>
                <p>Sistema de Inventario</p>
                <p>Fecha: <?php echo $venta['fecha_venta_formateada']; ?></p>
                <p>Factura #: <?php echo $venta['id']; ?></p>
            </div>
        </div>

        <?php if ($venta['nombre_cliente']): ?>
        <div class="cliente-info">
            <h3>Datos del Cliente</h3>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($venta['nombre_cliente']); ?></p>
            <p><strong>Cédula:</strong> <?php echo htmlspecialchars($venta['cedula_cliente']); ?></p>
            <p><strong>Dirección:</strong> <?php echo htmlspecialchars($venta['direccion_cliente']); ?></p>
        </div>
        <?php endif; ?>

        <div class="detalles">
            <h2>Detalles de la Venta</h2>
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $venta['codigo_producto']; ?></td>
                        <td><?php echo $venta['nombre_producto']; ?></td>
                        <td><?php echo $venta['cantidad']; ?></td>
                        <td>$<?php echo number_format($venta['total'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="tipo-pago">
            <strong>Tipo de Pago:</strong> <?php echo ucfirst($venta['tipo_pago']); ?>
        </div>

        <div class="footer">
            <p>Gracias por su compra</p>
            <p>Este documento es una factura válida</p>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <button onclick="window.print()" class="btn-imprimir">
                <span class="material-icons">print</span> Imprimir Factura
            </button>
        </div>
    </div>

    <script>
        // Función para imprimir la factura
        function imprimirFactura() {
            window.print();
        }
    </script>
</body>
</html>
<?php
} catch (Exception $e) {
    die('Error al generar la factura: ' . $e->getMessage());
}

$conexion->close();
?> 