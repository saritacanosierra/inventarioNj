<?php
require '../conexion.php';

$sql = "SELECT v.*, p.nombre as nombre_producto, p.codigo as codigo_producto, 
        DATE_FORMAT(v.fecha_venta, '%Y-%m-%d %H:%i:%s') as fecha_venta,
        p.stock as stock_actual
        FROM ventas v 
        JOIN productos p ON v.id_producto = p.id 
        ORDER BY v.fecha_venta DESC";
$resultado = $conexion->query($sql);

if (!$resultado) {
    die('Error al obtener la lista de ventas: ' . $conexion->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas - Sistema de Inventario</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/tablas.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: modalFadeIn 0.3s ease-out;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #000;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #E1B8E2;
            box-shadow: 0 0 0 2px rgba(225, 184, 226, 0.25);
        }

        .form-group input[readonly] {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }

        .form-buttons {
            margin-top: 25px;
            text-align: right;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-guardar,
        .btn-cancelar {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-guardar {
            background-color: #E1B8E2;
            color: #000;
        }

        .btn-guardar:hover {
            background-color: #d4a7d5;
            transform: translateY(-1px);
        }

        .btn-cancelar {
            background-color: #f44336;
            color: white;
        }

        .btn-cancelar:hover {
            background-color: #d32f2f;
            transform: translateY(-1px);
        }

        .mensaje {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .acciones {
            display: flex;
            gap: 10px;
        }

        .btn-editar,
        .btn-eliminar {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            transition: transform 0.2s ease;
        }

        .btn-editar:hover,
        .btn-eliminar:hover {
            transform: scale(1.1);
        }

        .btn-editar .material-icons {
            color: #2196F3;
        }

        .btn-eliminar .material-icons {
            color: #f44336;
        }

        /* Estilos para el select de productos */
        select#id_producto,
        select#edit_id_producto {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            font-size: 14px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        select#id_producto:focus,
        select#edit_id_producto:focus {
            outline: none;
            border-color: #E1B8E2;
            box-shadow: 0 0 0 2px rgba(225, 184, 226, 0.25);
        }

        /* Estilos para los campos de fecha y hora */
        input[type="datetime-local"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="datetime-local"]:focus {
            outline: none;
            border-color: #E1B8E2;
            box-shadow: 0 0 0 2px rgba(225, 184, 226, 0.25);
        }

        /* Estilos para los campos numéricos */
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="number"]:focus {
            outline: none;
            border-color: #E1B8E2;
            box-shadow: 0 0 0 2px rgba(225, 184, 226, 0.25);
        }

        /* Estilos para el contenedor del modal */
        .modal-content h2 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #E1B8E2;
        }

        /* Estilos para el botón de ventas mensuales */
        .btn-ventas-mensuales {
            background-color: #E1B8E2;
            color: #000;
            border: none;
            border-radius: 4px;
            padding: 8px 15px;
            margin-right: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .btn-ventas-mensuales:hover {
            background-color: #d4a7d5;
            transform: translateY(-1px);
        }

        .btn-ventas-mensuales .material-icons {
            font-size: 20px;
        }

        /* Estilos para el modal de ventas mensuales */
        .modal-ventas-mensuales {
            max-width: 800px !important;
        }

        .meses-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn-mes {
            padding: 10px;
            border: 1px solid #E1B8E2;
            background-color: white;
            color: #333;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-mes:hover {
            background-color: #E1B8E2;
            color: #000;
        }

        .btn-mes.active {
            background-color: #E1B8E2;
            color: #000;
            font-weight: bold;
        }

        .resumen-ventas {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .total-ventas, .cantidad-ventas {
            text-align: center;
        }

        .total-ventas h3, .cantidad-ventas h3 {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        .total-ventas p, .cantidad-ventas p {
            margin: 5px 0 0;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .tabla-ventas-mensuales {
            max-height: 400px;
            overflow-y: auto;
        }

        .tabla-ventas-mensuales table {
            width: 100%;
            border-collapse: collapse;
        }

        .tabla-ventas-mensuales th,
        .tabla-ventas-mensuales td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .tabla-ventas-mensuales th {
            background-color: #f8f9fa;
            position: sticky;
            top: 0;
        }

        .tabla-ventas-mensuales tbody tr:hover {
            background-color: #f5f5f5;
        }

        /* Estilos para productos con bajo stock */
        .stock-bajo {
            color: #ffd700;
            font-weight: bold;
            animation: parpadeo 2s infinite;
            background-color: #fff8dc;
            padding: 2px 5px;
            border-radius: 3px;
        }

        @keyframes parpadeo {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        .stock-bajo::after {
            content: "⚠️";
            margin-left: 5px;
        }

        .btn-factura {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-factura .material-icons {
            color: #4CAF50;
        }

        .btn-factura:hover .material-icons {
            color: #388E3C;
        }
    </style>
</head>
<body>
    <div class="contenedor-principal">
        <?php include '../components/header.php'; ?>
        
        <div class="contenido">
            <h2>Registro de Ventas</h2>
            
            <div class="filtro-agregar-contenedor">
                <div class="filtro-contenedor">
                    <input type="text" id="filtro-venta" placeholder="Buscar venta..." class="filtro-input">
                </div>
                <div class="btn-agregar-contenedor">
                    <button onclick="abrirModalVentasMensuales()" class="btn-ventas-mensuales">
                        <span class="material-icons">calendar_month</span>
                        Ventas Mensuales
                    </button>
                    <button onclick="abrirModalInsertar()" class="btn-agregar">+</button>
                </div>
            </div>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Código Producto</th>
                            <th>Producto</th>
                            <th>Stock</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
                            <th>Tipo de Pago</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-ventas">
                        <?php while ($venta = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $venta['id']; ?></td>
                                <td><?php 
                                    $fecha = new DateTime($venta['fecha_venta']);
                                    echo $fecha->format('d/n/Y H:i');
                                ?></td>
                                <td><?php echo $venta['codigo_producto']; ?></td>
                                <td>
                                    <?php 
                                    echo $venta['nombre_producto'];
                                    if ($venta['stock_actual'] == 1) {
                                        echo ' <span class="stock-bajo">(Última unidad disponible)</span>';
                                    }
                                    ?>
                                </td>
                                <td><?php echo $venta['cantidad']; ?></td>
                                <td>$<?php echo number_format($venta['precio_unitario'], 2); ?></td>
                                <td>$<?php echo number_format($venta['total'], 2); ?></td>
                                <td>
                                    <?php 
                                    $tipo_pago = $venta['tipo_pago'] ?? 'No especificado';
                                    echo ucfirst($tipo_pago);
                                    ?>
                                </td>
                                <td class="acciones">
                                    <button onclick="abrirModalEditar(<?php echo htmlspecialchars(json_encode($venta)); ?>)" class="btn-editar">
                                        <span class="material-icons">edit</span>
                                    </button>
                                    <a href="../controllers/eliminar_venta.php?id=<?php echo $venta['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar esta venta?')">
                                        <span class="material-icons">delete</span>
                                    </a>
                                    <a href="../controllers/generar_factura.php?id=<?php echo $venta['id']; ?>" class="btn-factura" target="_blank">
                                        <span class="material-icons">receipt</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de Insertar Venta -->
    <div id="modalInsertar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalInsertar()">&times;</span>
            <h2>Registrar Nueva Venta</h2>
            <div id="mensaje-error-insertar" class="mensaje error" style="display: none;"></div>
            <form id="formInsertar" class="form-insertar">
                <div class="form-group">
                    <label for="fecha_venta">Fecha de Venta:</label>
                    <input type="datetime-local" id="fecha_venta" name="fecha_venta" required>
                </div>
                <div class="form-group">
                    <label for="id_categoria">Categoría:</label>
                    <select id="id_categoria" name="id_categoria" required onchange="filtrarProductos()">
                        <option value="">Seleccione una categoría</option>
                        <?php
                        $sql_categorias = "SELECT id, codigo, nombre FROM categoria ORDER BY nombre";
                        $categorias = $conexion->query($sql_categorias);
                        while ($categoria = $categorias->fetch_assoc()) {
                            echo "<option value='{$categoria['id']}'>{$categoria['nombre']} ({$categoria['codigo']})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_producto">Producto:</label>
                    <select id="id_producto" name="id_producto" required onchange="actualizarPrecio()">
                        <option value="">Seleccione un producto</option>
                        <?php
                        $sql_productos = "SELECT p.id, p.codigo, p.nombre, p.precio, p.stock, p.id_categoria 
                                        FROM productos p 
                                        WHERE p.stock > 0 
                                        ORDER BY p.nombre";
                        $productos = $conexion->query($sql_productos);
                        while ($producto = $productos->fetch_assoc()) {
                            echo "<option value='{$producto['id']}' 
                                    data-precio='{$producto['precio']}' 
                                    data-stock='{$producto['stock']}'
                                    data-categoria='{$producto['id_categoria']}'
                                    style='display: none;'>
                                    {$producto['codigo']} - {$producto['nombre']} (Stock: {$producto['stock']})
                                </option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" id="cantidad" name="cantidad" min="1" required onchange="calcularTotal()">
                </div>
                <div class="form-group">
                    <label for="precio_unitario">Precio Unitario:</label>
                    <input type="number" id="precio_unitario" name="precio_unitario" step="0.01" readonly>
                </div>
                <div class="form-group">
                    <label for="total">Total:</label>
                    <input type="number" id="total" name="total" step="0.01" readonly>
                </div>
                <div class="form-group">
                    <label for="tipo_pago">Tipo de Pago:</label>
                    <select id="tipo_pago" name="tipo_pago" required>
                        <option value="">Seleccione el tipo de pago</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-guardar">Guardar</button>
                    <button type="button" class="btn-cancelar" onclick="cerrarModalInsertar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Editar Venta -->
    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalEditar()">&times;</span>
            <h2>Editar Venta</h2>
            <div id="mensaje-error-editar" class="mensaje error" style="display: none;"></div>
            <form id="formEditar" class="form-editar">
                <input type="hidden" id="edit_id" name="id">
                <div class="form-group">
                    <label for="edit_fecha_venta">Fecha de Venta:</label>
                    <input type="datetime-local" id="edit_fecha_venta" name="fecha_venta" required>
                </div>
                <div class="form-group">
                    <label for="edit_id_producto">Producto:</label>
                    <select id="edit_id_producto" name="id_producto" required onchange="actualizarPrecioEditar()">
                        <?php
                        $productos->data_seek(0);
                        while ($producto = $productos->fetch_assoc()) {
                            echo "<option value='{$producto['id']}' data-precio='{$producto['precio']}' data-stock='{$producto['stock']}'>{$producto['codigo']} - {$producto['nombre']} (Stock: {$producto['stock']})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_cantidad">Cantidad:</label>
                    <input type="number" id="edit_cantidad" name="cantidad" min="1" required onchange="calcularTotalEditar()">
                </div>
                <div class="form-group">
                    <label for="edit_precio_unitario">Precio Unitario:</label>
                    <input type="number" id="edit_precio_unitario" name="precio_unitario" step="0.01" readonly>
                </div>
                <div class="form-group">
                    <label for="edit_total">Total:</label>
                    <input type="number" id="edit_total" name="total" step="0.01" readonly>
                </div>
                <div class="form-group">
                    <label for="edit_tipo_pago">Tipo de Pago:</label>
                    <select id="edit_tipo_pago" name="tipo_pago" required>
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-guardar">Guardar Cambios</button>
                    <button type="button" class="btn-cancelar" onclick="cerrarModalEditar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Ventas Mensuales -->
    <div id="modalVentasMensuales" class="modal">
        <div class="modal-content modal-ventas-mensuales">
            <span class="close" onclick="cerrarModalVentasMensuales()">&times;</span>
            <h2>Ventas Mensuales</h2>
            
            <div class="meses-container">
                <button onclick="cargarVentasMes(1)" class="btn-mes">Enero</button>
                <button onclick="cargarVentasMes(2)" class="btn-mes">Febrero</button>
                <button onclick="cargarVentasMes(3)" class="btn-mes">Marzo</button>
                <button onclick="cargarVentasMes(4)" class="btn-mes">Abril</button>
                <button onclick="cargarVentasMes(5)" class="btn-mes">Mayo</button>
                <button onclick="cargarVentasMes(6)" class="btn-mes">Junio</button>
                <button onclick="cargarVentasMes(7)" class="btn-mes">Julio</button>
                <button onclick="cargarVentasMes(8)" class="btn-mes">Agosto</button>
                <button onclick="cargarVentasMes(9)" class="btn-mes">Septiembre</button>
                <button onclick="cargarVentasMes(10)" class="btn-mes">Octubre</button>
                <button onclick="cargarVentasMes(11)" class="btn-mes">Noviembre</button>
                <button onclick="cargarVentasMes(12)" class="btn-mes">Diciembre</button>
            </div>

            <div class="resumen-ventas">
                <div class="total-ventas">
                    <h3>Total Ventas del Mes</h3>
                    <p id="total-ventas-mes">$0.00</p>
                </div>
                <div class="cantidad-ventas">
                    <h3>Cantidad de Ventas</h3>
                    <p id="cantidad-ventas-mes">0</p>
                </div>
            </div>

            <div class="tabla-ventas-mensuales">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-ventas-mensuales">
                        <!-- Las ventas se cargarán aquí dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Funciones para el modal de insertar
        function abrirModalInsertar() {
            document.getElementById('modalInsertar').style.display = 'block';
            document.getElementById('mensaje-error-insertar').style.display = 'none';
            document.getElementById('id_categoria').value = '';
            filtrarProductos(); // Resetear el filtro de productos
        }

        function cerrarModalInsertar() {
            document.getElementById('modalInsertar').style.display = 'none';
            document.getElementById('formInsertar').reset();
            document.getElementById('mensaje-error-insertar').style.display = 'none';
            document.getElementById('precio_unitario').value = '';
            document.getElementById('total').value = '';
        }

        // Funciones para el modal de editar
        function abrirModalEditar(venta) {
            document.getElementById('modalEditar').style.display = 'block';
            document.getElementById('edit_id').value = venta.id;
            // Convertir la fecha al formato correcto para el input datetime-local
            const fecha = new Date(venta.fecha_venta);
            const fechaFormateada = fecha.toISOString().slice(0, 16);
            document.getElementById('edit_fecha_venta').value = fechaFormateada;
            document.getElementById('edit_id_producto').value = venta.id_producto;
            document.getElementById('edit_cantidad').value = venta.cantidad;
            document.getElementById('edit_precio_unitario').value = venta.precio_unitario;
            document.getElementById('edit_total').value = venta.total;
            document.getElementById('edit_tipo_pago').value = venta.tipo_pago || 'efectivo';
        }

        function cerrarModalEditar() {
            document.getElementById('modalEditar').style.display = 'none';
            document.getElementById('formEditar').reset();
        }

        // Funciones para cálculos
        function calcularTotal() {
            const cantidad = document.getElementById('cantidad').value;
            const precio = document.getElementById('precio_unitario').value;
            document.getElementById('total').value = (cantidad * precio).toFixed(2);
        }

        function calcularTotalEditar() {
            const cantidad = document.getElementById('edit_cantidad').value;
            const precio = document.getElementById('edit_precio_unitario').value;
            document.getElementById('edit_total').value = (cantidad * precio).toFixed(2);
        }

        // Función para filtrar productos por categoría
        function filtrarProductos() {
            const categoriaId = document.getElementById('id_categoria').value;
            const selectProducto = document.getElementById('id_producto');
            const options = selectProducto.getElementsByTagName('option');
            
            // Resetear el select de productos
            selectProducto.value = '';
            document.getElementById('precio_unitario').value = '';
            document.getElementById('cantidad').value = '';
            document.getElementById('total').value = '';
            
            // Mostrar/ocultar productos según la categoría seleccionada
            for (let option of options) {
                if (option.value === '') {
                    option.style.display = ''; // Siempre mostrar la opción por defecto
                } else {
                    const categoriaProducto = option.getAttribute('data-categoria');
                    option.style.display = categoriaId === '' || categoriaId === categoriaProducto ? '' : 'none';
                }
            }
        }

        // Modificar la función actualizarPrecio para incluir la validación de categoría
        function actualizarPrecio() {
            const select = document.getElementById('id_producto');
            const option = select.options[select.selectedIndex];
            const precio = option.getAttribute('data-precio');
            const stock = option.getAttribute('data-stock');
            
            if (select.value !== '') {
                document.getElementById('precio_unitario').value = precio;
                document.getElementById('cantidad').max = stock;
                calcularTotal();
            }
        }

        // Función para actualizar la tabla después de una venta
        function actualizarTablaVentas(nuevaVenta) {
            const tbody = document.getElementById('tabla-ventas');
            const tr = document.createElement('tr');
            
            // Crear el contenido de la celda del producto con el indicador de stock bajo si es necesario
            let productoCell = `${nuevaVenta.nombre_producto}`;
            if (nuevaVenta.stock_actual == 1) {
                productoCell += ` <span class="stock-bajo">(Última unidad disponible)</span>`;
            }

            tr.innerHTML = `
                <td>${nuevaVenta.id}</td>
                <td>${new Date(nuevaVenta.fecha_venta).toLocaleString('es-ES', {
                    day: 'numeric',
                    month: 'numeric',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                })}</td>
                <td>${nuevaVenta.codigo_producto}</td>
                <td>${productoCell}</td>
                <td>${nuevaVenta.cantidad}</td>
                <td>$${parseFloat(nuevaVenta.precio_unitario).toFixed(2)}</td>
                <td>$${parseFloat(nuevaVenta.total).toFixed(2)}</td>
                <td>${nuevaVenta.tipo_pago ? nuevaVenta.tipo_pago.charAt(0).toUpperCase() + nuevaVenta.tipo_pago.slice(1) : 'No especificado'}</td>
                <td class="acciones">
                    <button onclick="abrirModalEditar(${JSON.stringify(nuevaVenta)})" class="btn-editar">
                        <span class="material-icons">edit</span>
                    </button>
                    <a href="../controllers/eliminar_venta.php?id=${nuevaVenta.id}" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar esta venta?')">
                        <span class="material-icons">delete</span>
                    </a>
                    <a href="../controllers/generar_factura.php?id=${nuevaVenta.id}" class="btn-factura" target="_blank">
                        <span class="material-icons">receipt</span>
                    </a>
                </td>
            `;
            
            tbody.insertBefore(tr, tbody.firstChild);
        }

        // Modificar el manejador del formulario de insertar para usar la nueva función
        document.getElementById('formInsertar').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const mensajeError = document.getElementById('mensaje-error-insertar');
            mensajeError.style.display = 'none';
            
            fetch('../controllers/insertar_venta.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    actualizarTablaVentas(data);
                    cerrarModalInsertar();
                    alert('Venta registrada exitosamente');
                    location.reload(); // Recargar para actualizar el stock en los selectores
                } else {
                    mensajeError.textContent = data.message;
                    mensajeError.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mensajeError.textContent = 'Error al procesar la solicitud. Por favor, intente nuevamente.';
                mensajeError.style.display = 'block';
            });
        });

        // Manejar el envío del formulario de editar
        document.getElementById('formEditar').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            // Asegurarse de que la fecha esté en el formato correcto
            const fechaVenta = formData.get('fecha_venta');
            if (!fechaVenta) {
                alert('Por favor seleccione una fecha');
                return;
            }
            formData.set('fecha_venta', fechaVenta.replace('T', ' '));
            
            const mensajeError = document.getElementById('mensaje-error-editar');
            mensajeError.style.display = 'none';
            
            fetch('../controllers/actualizar_venta.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar la fila en la tabla
                    const rows = document.querySelectorAll('#tabla-ventas tr');
                    let targetRow = null;
                    
                    for (let row of rows) {
                        if (row.cells[0].textContent === data.id.toString()) {
                            targetRow = row;
                            break;
                        }
                    }
                    
                    if (targetRow) {
                        targetRow.innerHTML = `
                            <td>${data.id}</td>
                            <td>${new Date(data.fecha_venta).toLocaleString('es-ES', {
                                day: 'numeric',
                                month: 'numeric',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false
                            })}</td>
                            <td>${data.codigo_producto}</td>
                            <td>${data.nombre_producto}</td>
                            <td>${data.cantidad}</td>
                            <td>$${parseFloat(data.precio_unitario).toFixed(2)}</td>
                            <td>$${parseFloat(data.total).toFixed(2)}</td>
                            <td>${data.tipo_pago ? data.tipo_pago.charAt(0).toUpperCase() + data.tipo_pago.slice(1) : 'No especificado'}</td>
                            <td class="acciones">
                                <button onclick="abrirModalEditar(${JSON.stringify(data)})" class="btn-editar">
                                    <span class="material-icons">edit</span>
                                </button>
                                <a href="../controllers/eliminar_venta.php?id=${data.id}" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar esta venta?')">
                                    <span class="material-icons">delete</span>
                                </a>
                                <a href="../controllers/generar_factura.php?id=${data.id}" class="btn-factura" target="_blank">
                                    <span class="material-icons">receipt</span>
                                </a>
                            </td>
                        `;
                    }
                    
                    cerrarModalEditar();
                    alert('Venta actualizada exitosamente');
                    location.reload(); // Recargar para actualizar el stock en los selectores
                } else {
                    mensajeError.textContent = data.message;
                    mensajeError.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mensajeError.textContent = 'Error al procesar la solicitud. Por favor, intente nuevamente.';
                mensajeError.style.display = 'block';
            });
        });

        // Filtro de búsqueda
        document.getElementById('filtro-venta').addEventListener('keyup', function() {
            const filtro = this.value.toLowerCase();
            const tabla = document.getElementById('tabla-ventas');
            const filas = tabla.getElementsByTagName('tr');

            for (let i = 0; i < filas.length; i++) {
                const celdas = filas[i].getElementsByTagName('td');
                let mostrar = false;

                for (let j = 0; j < celdas.length; j++) {
                    if (celdas[j].textContent.toLowerCase().indexOf(filtro) > -1) {
                        mostrar = true;
                        break;
                    }
                }

                filas[i].style.display = mostrar ? '' : 'none';
            }
        });

        // Funciones para el modal de ventas mensuales
        function abrirModalVentasMensuales() {
            document.getElementById('modalVentasMensuales').style.display = 'block';
            // Cargar el mes actual por defecto
            const mesActual = new Date().getMonth() + 1;
            cargarVentasMes(mesActual);
        }

        function cerrarModalVentasMensuales() {
            document.getElementById('modalVentasMensuales').style.display = 'none';
        }

        function cargarVentasMes(mes) {
            // Actualizar botón activo
            const botones = document.querySelectorAll('.btn-mes');
            botones.forEach(btn => btn.classList.remove('active'));
            botones[mes - 1].classList.add('active');

            // Obtener el año actual
            const año = new Date().getFullYear();

            // Realizar la petición al servidor
            fetch(`../controllers/obtener_ventas_mes.php?mes=${mes}&año=${año}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar resumen
                        document.getElementById('total-ventas-mes').textContent = 
                            `$${parseFloat(data.total_ventas).toFixed(2)}`;
                        document.getElementById('cantidad-ventas-mes').textContent = 
                            data.cantidad_ventas;

                        // Actualizar tabla
                        const tbody = document.getElementById('tabla-ventas-mensuales');
                        tbody.innerHTML = '';

                        data.ventas.forEach(venta => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${new Date(venta.fecha_venta).toLocaleString('es-ES', {
                                    day: 'numeric',
                                    month: 'numeric',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: false
                                })}</td>
                                <td>${venta.nombre_producto} (${venta.codigo_producto})</td>
                                <td>${venta.cantidad}</td>
                                <td>$${parseFloat(venta.precio_unitario).toFixed(2)}</td>
                                <td>$${parseFloat(venta.total).toFixed(2)}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    } else {
                        console.error('Error al cargar las ventas:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Cerrar modal de ventas mensuales al hacer clic fuera
        window.onclick = function(event) {
            var modalVentasMensuales = document.getElementById('modalVentasMensuales');
            if (event.target == modalVentasMensuales) {
                cerrarModalVentasMensuales();
            }
            // ... existing modal close code ...
        }
    </script>
</body>
</html>