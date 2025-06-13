<?php
require '../conexion.php';

// Obtener categorías
$sql_categorias = "SELECT id, codigo, nombre FROM categoria ORDER BY nombre";
$categorias = $conexion->query($sql_categorias);

// Obtener productos
$sql_productos = "SELECT p.id, p.codigo, p.nombre, p.precio, p.stock, p.id_categoria, c.nombre as categoria_nombre 
                  FROM productos p 
                  LEFT JOIN categoria c ON p.id_categoria = c.id
                  WHERE p.stock > 0 
                  ORDER BY p.nombre";
$productos = $conexion->query($sql_productos);

// Obtener clientes
$sql_clientes = "SELECT id, nombre, cedula, celular FROM clientes ORDER BY nombre";
$clientes = $conexion->query($sql_clientes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Venta - Sistema de Inventario</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        /* Estilos consistentes con las demás vistas */
        .contenedor-principal {
            min-height: 100vh;
            background-color: #f5f5f5;
        }

        .contenido {
            width: 100%;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 1600px;
            margin-left: auto;
            margin-right: auto;
        }

        .contenido h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 600;
        }

        /* Layout de 2 columnas */
        .layout-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: start;
            justify-content: center;
            width: 100%;
        }

        .columna-izquierda {
            display: flex;
            flex-direction: column;
            gap: 25px;
            width: 100%;
        }

        .columna-derecha {
            display: flex;
            flex-direction: column;
            gap: 25px;
            width: 100%;
        }

        .form-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            width: 100%;
            box-sizing: border-box;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
            box-sizing: border-box;
            height: 45px;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #E1B8E2;
            box-shadow: 0 0 0 3px rgba(225, 184, 226, 0.2);
        }

        .form-group input[readonly] {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-color: #ced4da;
        }

        .form-group input[type="number"] {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        .form-group input[readonly][type="number"] {
            background-color: #e9ecef;
            color: #495057;
            font-weight: bold;
        }

        .productos-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            width: 100%;
            box-sizing: border-box;
        }

        .producto-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            border: 1px solid #e9ecef;
        }

        .producto-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .producto-title {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .btn-remove-producto {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-remove-producto:hover {
            background: #c82333;
        }

        .producto-fields {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
            gap: 10px;
            align-items: end;
        }

        .producto-fields .form-group:first-child {
            grid-column: 1 / -1;
            margin-bottom: 10px;
        }

        .producto-fields .form-group:first-child select {
            width: 100%;
        }

        .producto-fields .form-group:nth-child(2) {
            grid-column: 1 / -1;
            margin-bottom: 10px;
        }

        .producto-fields .form-group:nth-child(2) select {
            width: 100%;
        }

        .producto-fields .form-group:nth-child(3),
        .producto-fields .form-group:nth-child(4),
        .producto-fields .form-group:nth-child(5) {
            grid-column: span 1;
        }

        .producto-fields .form-group:nth-child(6) {
            display: none;
        }

        .btn-add-producto {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn-add-producto:hover {
            background: #218838;
            transform: translateY(-1px);
        }

        .resumen-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            width: 100%;
            box-sizing: border-box;
        }

        .resumen-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .resumen-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .resumen-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .resumen-value {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .total-general {
            text-align: center;
            padding: 15px;
            background: linear-gradient(135deg, #E1B8E2, #d4a7d5);
            border-radius: 8px;
            color: #333;
        }

        .total-general .resumen-value {
            font-size: 28px;
            color: #333;
        }

        .actions-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        .btn-primary {
            background: #E1B8E2;
            color: #333;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #d4a7d5;
            transform: translateY(-1px);
        }

        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .mensaje {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: 600;
        }

        .mensaje.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .mensaje.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .producto-info {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }

        .stock-disponible {
            color: #28a745;
            font-weight: 600;
        }

        .stock-bajo {
            color: #ffc107;
            font-weight: 600;
        }

        .sin-stock {
            color: #dc3545;
            font-weight: 600;
        }

        /* Estilos del modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .form-insertar {
            margin-top: 20px;
        }

        .form-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn-guardar {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-guardar:hover {
            background: #218838;
        }

        .btn-cancelar {
            background: #6c757d;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-cancelar:hover {
            background: #5a6268;
        }

        /* Optimizaciones adicionales para pantallas más pequeñas */
        @media (max-width: 1600px) {
            .contenido {
                max-width: 1400px;
            }
            
            .layout-container {
                grid-template-columns: 1fr 1fr;
                gap: 30px;
            }
        }

        @media (max-width: 1400px) {
            .contenido {
                max-width: 1200px;
            }
            
            .layout-container {
                grid-template-columns: 1fr 1fr;
                gap: 25px;
            }
        }

        @media (max-width: 1200px) {
            .contenido {
                max-width: 100%;
                margin: 15px;
            }
            
            .layout-container {
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }
            
            .producto-fields {
                grid-template-columns: 1fr 1fr 1fr;
                gap: 8px;
            }
            
            .producto-fields .form-group:first-child,
            .producto-fields .form-group:nth-child(2) {
                grid-column: 1 / -1;
            }
            
            .producto-fields .form-group:nth-child(3),
            .producto-fields .form-group:nth-child(4),
            .producto-fields .form-group:nth-child(5) {
                grid-column: span 1;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 10px;
            }
        }

        @media (max-width: 768px) {
            .contenido {
                margin: 10px;
                padding: 15px;
            }
            
            .layout-container {
                width: 100%;
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .form-section, .productos-section, .resumen-section {
                padding: 15px;
            }
            
            .resumen-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .producto-fields {
                grid-template-columns: 1fr;
                gap: 8px;
            }
            
            .producto-fields .form-group:first-child,
            .producto-fields .form-group:nth-child(2) {
                grid-column: 1;
            }
            
            .producto-fields .form-group:nth-child(3),
            .producto-fields .form-group:nth-child(4),
            .producto-fields .form-group:nth-child(5) {
                grid-column: 1;
            }
        }
    </style>
</head>
<body>
    
    <div class="contenedor-principal">
        <?php include '../components/header.php'; ?>
        
        <div class="contenido">
            <h2>Registrar Nueva Venta</h2>
            
            <div id="mensaje" class="mensaje" style="display: none;"></div>

            <form id="formVenta" method="POST" action="../controllers/procesar_venta.php">
                <!-- Layout de 2 columnas -->
                <div class="layout-container">
                    <!-- Columna izquierda -->
                    <div class="columna-izquierda">
                        <!-- Información del Cliente -->
                        <div class="form-section">
                            <h2><span class="material-icons" style="margin-right: 8px;">person</span>Información del Cliente</h2>
                            
                            <div class="form-group">
                                <label for="id_cliente">Cliente:</label>
                                <div style="display: flex; gap: 10px; align-items: end;">
                                    <select id="id_cliente" name="id_cliente" required style="flex: 1;">
                                        <option value="">Seleccione un cliente</option>
                                        <?php while ($cliente = $clientes->fetch_assoc()): ?>
                                            <option value="<?= $cliente['id'] ?>">
                                                <?= htmlspecialchars($cliente['nombre']) ?> - <?= htmlspecialchars($cliente['cedula']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <button type="button" class="btn-agregar-cliente" onclick="abrirModalInsertarCliente()" style="height: 45px; padding: 0 15px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; transition: all 0.3s ease;">
                                        <span class="material-icons" style="font-size: 20px;">person_add</span>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="fecha_venta">Fecha de Venta:</label>
                                <input type="datetime-local" id="fecha_venta" name="fecha_venta" required>
                            </div>

                            <div class="form-group">
                                <label for="tipo_pago">Tipo de Pago:</label>
                                <select id="tipo_pago" name="tipo_pago" required>
                                    <option value="">Seleccione el tipo de pago</option>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="tarjeta">Tarjeta</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="estado">Estado:</label>
                                <select id="estado" name="estado" required>
                                    <option value="completada">Completada</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="cancelada">Cancelada</option>
                                </select>
                            </div>
                        </div>

                        <!-- Resumen -->
                        <div class="resumen-section">
                            <h2><span class="material-icons" style="margin-right: 8px;">receipt</span>Resumen de la Venta</h2>
                            
                            <div class="resumen-grid">
                                <div class="resumen-item">
                                    <div class="resumen-label">Cantidad de Productos</div>
                                    <div class="resumen-value" id="cantidad-productos">0</div>
                                </div>
                                <div class="resumen-item">
                                    <div class="resumen-label">Total de Unidades</div>
                                    <div class="resumen-value" id="total-unidades">0</div>
                                </div>
                                <div class="resumen-item">
                                    <div class="resumen-label">Subtotal</div>
                                    <div class="resumen-value" id="subtotal">$0.00</div>
                                </div>
                            </div>

                            <div class="total-general">
                                <div class="resumen-label">TOTAL GENERAL</div>
                                <div class="resumen-value" id="total-general">$0.00</div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div class="columna-derecha">
                        <!-- Productos -->
                        <div class="productos-section">
                            <h2><span class="material-icons" style="margin-right: 8px;">inventory</span>Productos de la Venta</h2>
                            
                            <div id="productos-container">
                                <!-- Los productos se agregarán dinámicamente aquí -->
                            </div>

                            <button type="button" class="btn-add-producto" onclick="agregarProducto()">
                                <span class="material-icons">add</span>
                                Agregar Producto
                            </button>
                        </div>

                        <!-- Acciones -->
                        <div class="actions-section">
                            <a href="ventas.php" class="btn-secondary">
                                <span class="material-icons" style="margin-right: 5px;">arrow_back</span>
                                Volver a Ventas
                            </a>
                            
                            <button type="submit" class="btn-primary" id="btn-guardar" disabled>
                                <span class="material-icons" style="margin-right: 5px;">save</span>
                                Guardar Venta
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para insertar cliente -->
    <div id="modalInsertarCliente" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalInsertarCliente()">&times;</span>
            <h2>Insertar Nuevo Cliente</h2>
            <div id="mensaje-error-insertar-cliente" class="mensaje error" style="display: none;"></div>
            <form id="formInsertarCliente" class="form-insertar" method="POST" action="../controllers/insertar_cliente.php">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="cedula">Cédula:</label>
                    <input type="text" id="cedula" name="cedula" required>
                </div>
                <div class="form-group">
                    <label for="celular">Celular:</label>
                    <input type="text" id="celular" name="celular" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" required>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-guardar">Guardar</button>
                    <button type="button" class="btn-cancelar" onclick="cerrarModalInsertarCliente()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Datos de categorías disponibles
        const categorias = [
            <?php 
            $categorias->data_seek(0);
            $categoriasArray = [];
            while ($categoria = $categorias->fetch_assoc()) {
                $categoriasArray[] = "{
                    id: {$categoria['id']},
                    codigo: '" . addslashes($categoria['codigo']) . "',
                    nombre: '" . addslashes($categoria['nombre']) . "'
                }";
            }
            echo implode(",\n", $categoriasArray);
            ?>
        ];

        // Datos de productos disponibles
        const productos = [
            <?php 
            $productos->data_seek(0);
            $productosArray = [];
            while ($producto = $productos->fetch_assoc()) {
                $productosArray[] = "{
                    id: {$producto['id']},
                    codigo: '" . addslashes($producto['codigo']) . "',
                    nombre: '" . addslashes($producto['nombre']) . "',
                    precio: {$producto['precio']},
                    stock: {$producto['stock']},
                    categoria_id: " . ($producto['id_categoria'] ?: 'null') . ",
                    categoria: '" . addslashes($producto['categoria_nombre'] ?? 'Sin categoría') . "'
                }";
            }
            echo implode(",\n", $productosArray);
            ?>
        ];

        let productosAgregados = [];
        let contadorProductos = 0;

        // Establecer fecha actual
        document.getElementById('fecha_venta').value = new Date().toISOString().slice(0, 16);

        function agregarProducto() {
            contadorProductos++;
            const productoId = `producto_${contadorProductos}`;
            
            const productoHTML = `
                <div class="producto-item" id="${productoId}">
                    <div class="producto-header">
                        <div class="producto-title">Producto ${contadorProductos}</div>
                        <button type="button" class="btn-remove-producto" onclick="removerProducto('${productoId}')">
                            <span class="material-icons">delete</span>
                        </button>
                    </div>
                    <div class="producto-fields">
                        <div class="form-group">
                            <label for="filtro_categoria_${contadorProductos}">Categoría:</label>
                            <select id="filtro_categoria_${contadorProductos}" onchange="filtrarProductosPorCategoria(${contadorProductos})" style="margin-bottom: 10px;">
                                <option value="">Todas las categorías</option>
                                ${categorias.map(c => `
                                    <option value="${c.id}">
                                        ${c.nombre} (${c.codigo})
                                    </option>
                                `).join('')}
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="producto_${contadorProductos}">Producto:</label>
                            <select id="select_${contadorProductos}" name="productos[${contadorProductos}][id_producto]" required onchange="actualizarPrecio(${contadorProductos})">
                                <option value="">Seleccione un producto</option>
                                ${productos.map(p => `
                                    <option value="${p.id}" data-precio="${p.precio}" data-stock="${p.stock}" data-categoria="${p.categoria_id || ''}">
                                        ${p.codigo} - ${p.nombre} (Stock: ${p.stock})
                                    </option>
                                `).join('')}
                            </select>
                            <div class="producto-info" id="info_${contadorProductos}"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="cantidad_${contadorProductos}">Cantidad:</label>
                            <input type="number" id="cantidad_${contadorProductos}" name="productos[${contadorProductos}][cantidad]" min="1" required onchange="calcularSubtotal(${contadorProductos})" oninput="calcularSubtotal(${contadorProductos})">
                        </div>
                        
                        <div class="form-group">
                            <label for="precio_${contadorProductos}">Precio Unitario:</label>
                            <input type="number" id="precio_${contadorProductos}" name="productos[${contadorProductos}][precio_unitario]" step="0.01" readonly style="background-color: #e9ecef; font-weight: bold;">
                        </div>
                        
                        <div class="form-group">
                            <label for="subtotal_${contadorProductos}">Subtotal:</label>
                            <input type="number" id="subtotal_${contadorProductos}" name="productos[${contadorProductos}][subtotal]" step="0.01" readonly style="background-color: #e9ecef; font-weight: bold;">
                        </div>
                        
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <input type="hidden" id="stock_disponible_${contadorProductos}" value="0">
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('productos-container').insertAdjacentHTML('beforeend', productoHTML);
            productosAgregados.push(productoId);
            actualizarResumen();
        }

        function removerProducto(productoId) {
            const elemento = document.getElementById(productoId);
            elemento.remove();
            productosAgregados = productosAgregados.filter(id => id !== productoId);
            actualizarResumen();
        }

        function actualizarPrecio(numero) {
            const select = document.getElementById(`select_${numero}`);
            const precioInput = document.getElementById(`precio_${numero}`);
            const infoDiv = document.getElementById(`info_${numero}`);
            const stockInput = document.getElementById(`stock_disponible_${numero}`);
            
            console.log(`Actualizando precio para producto ${numero}`);
            
            if (select.value) {
                const producto = productos.find(p => p.id == select.value);
                if (producto) {
                    console.log(`Producto encontrado:`, producto);
                    precioInput.value = producto.precio.toFixed(2);
                    stockInput.value = producto.stock;
                    
                    let stockClass = 'stock-disponible';
                    if (producto.stock <= 5) {
                        stockClass = 'stock-bajo';
                    }
                    if (producto.stock == 0) {
                        stockClass = 'sin-stock';
                    }
                    
                    infoDiv.innerHTML = `
                        <span class="${stockClass}">Stock disponible: ${producto.stock}</span><br>
                        <span>Categoría: ${producto.categoria}</span>
                    `;
                    
                    console.log(`Precio establecido: ${precioInput.value}`);
                    calcularSubtotal(numero);
                }
            } else {
                precioInput.value = '';
                infoDiv.innerHTML = '';
                stockInput.value = '0';
                console.log(`Producto no seleccionado, limpiando campos`);
            }
        }

        function calcularSubtotal(numero) {
            const cantidad = parseFloat(document.getElementById(`cantidad_${numero}`).value) || 0;
            const precio = parseFloat(document.getElementById(`precio_${numero}`).value) || 0;
            const subtotal = cantidad * precio;
            
            console.log(`Calculando subtotal para producto ${numero}:`, { cantidad, precio, subtotal });
            
            document.getElementById(`subtotal_${numero}`).value = subtotal.toFixed(2);
            console.log(`Subtotal establecido: ${subtotal.toFixed(2)}`);
            
            actualizarResumen();
        }

        function actualizarResumen() {
            let cantidadProductos = productosAgregados.length;
            let totalUnidades = 0;
            let subtotal = 0;
            
            productosAgregados.forEach(productoId => {
                const numero = productoId.split('_')[1];
                const cantidad = parseFloat(document.getElementById(`cantidad_${numero}`).value) || 0;
                const subtotalProducto = parseFloat(document.getElementById(`subtotal_${numero}`).value) || 0;
                
                totalUnidades += cantidad;
                subtotal += subtotalProducto;
            });
            
            document.getElementById('cantidad-productos').textContent = cantidadProductos;
            document.getElementById('total-unidades').textContent = totalUnidades;
            document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
            document.getElementById('total-general').textContent = `$${subtotal.toFixed(2)}`;
            
            // Habilitar/deshabilitar botón de guardar
            const btnGuardar = document.getElementById('btn-guardar');
            btnGuardar.disabled = cantidadProductos === 0 || subtotal === 0;
        }

        // Validación del formulario
        document.getElementById('formVenta').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (productosAgregados.length === 0) {
                mostrarMensaje('Debe agregar al menos un producto', 'error');
                return;
            }
            
            // Validar que todos los productos tengan datos válidos
            let valido = true;
            productosAgregados.forEach(productoId => {
                const numero = productoId.split('_')[1];
                const producto = document.getElementById(`select_${numero}`).value;
                const cantidad = document.getElementById(`cantidad_${numero}`).value;
                const stockDisponible = parseInt(document.getElementById(`stock_disponible_${numero}`).value);
                
                if (!producto || !cantidad || cantidad <= 0) {
                    valido = false;
                }
                
                if (parseInt(cantidad) > stockDisponible) {
                    mostrarMensaje(`La cantidad del producto ${numero} excede el stock disponible`, 'error');
                    valido = false;
                }
            });
            
            if (valido) {
                this.submit();
            }
        });

        function mostrarMensaje(mensaje, tipo) {
            const mensajeDiv = document.getElementById('mensaje');
            mensajeDiv.textContent = mensaje;
            mensajeDiv.className = `mensaje ${tipo}`;
            mensajeDiv.style.display = 'block';
            
            setTimeout(() => {
                mensajeDiv.style.display = 'none';
            }, 5000);
        }

        // Agregar primer producto automáticamente
        window.onload = function() {
            agregarProducto();
        };

        // Función para filtrar productos por categoría (individual por producto)
        function filtrarProductosPorCategoria(numero) {
            const categoriaSeleccionada = document.getElementById(`filtro_categoria_${numero}`).value;
            const select = document.getElementById(`select_${numero}`);
            
            if (select) {
                const options = select.getElementsByTagName('option');
                
                for (let option of options) {
                    if (option.value === '') {
                        option.style.display = ''; // Siempre mostrar la opción por defecto
                    } else {
                        const categoriaProducto = option.getAttribute('data-categoria') || '';
                        option.style.display = categoriaSeleccionada === '' || categoriaSeleccionada === categoriaProducto ? '' : 'none';
                    }
                }
                
                // Si el producto seleccionado no está en la categoría filtrada, limpiar la selección
                if (select.value && select.value !== '') {
                    const productoSeleccionado = productos.find(p => p.id == select.value);
                    if (productoSeleccionado && categoriaSeleccionada !== '' && productoSeleccionado.categoria_id != categoriaSeleccionada) {
                        select.value = '';
                        actualizarPrecio(numero);
                    }
                }
            }
        }

        // Funciones del modal para insertar cliente
        function abrirModalInsertarCliente() {
            document.getElementById('modalInsertarCliente').style.display = 'block';
        }

        function cerrarModalInsertarCliente() {
            document.getElementById('modalInsertarCliente').style.display = 'none';
            document.getElementById('formInsertarCliente').reset();
            document.getElementById('mensaje-error-insertar-cliente').style.display = 'none';
        }

        // Manejo del formulario de insertar cliente
        document.getElementById('formInsertarCliente').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const mensajeError = document.getElementById('mensaje-error-insertar-cliente');
            mensajeError.style.display = 'none';
            
            // Mostrar indicador de carga
            const btnGuardar = this.querySelector('.btn-guardar');
            const textoOriginal = btnGuardar.textContent;
            btnGuardar.textContent = 'Guardando...';
            btnGuardar.disabled = true;
            
            fetch('../controllers/insertar_cliente.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.text();
            })
            .then(text => {
                console.log('Response text:', text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    throw new Error('Respuesta no válida del servidor: ' + text);
                }
            })
            .then(data => {
                console.log('Parsed data:', data);
                if (data.success) {
                    // Agregar el nuevo cliente al select
                    const selectCliente = document.getElementById('id_cliente');
                    const option = document.createElement('option');
                    option.value = data.cliente_id;
                    option.textContent = `${data.nombre} - ${data.cedula}`;
                    selectCliente.appendChild(option);
                    
                    // Seleccionar el nuevo cliente
                    selectCliente.value = data.cliente_id;
                    
                    // Cerrar modal
                    cerrarModalInsertarCliente();
                    
                    // Mostrar mensaje de éxito
                    mostrarMensaje('Cliente agregado exitosamente', 'success');
                } else {
                    // Mostrar error en el modal
                    mensajeError.textContent = data.message || 'Error al agregar cliente';
                    mensajeError.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error completo:', error);
                mensajeError.textContent = 'Error de conexión: ' + error.message;
                mensajeError.style.display = 'block';
            })
            .finally(() => {
                // Restaurar botón
                btnGuardar.textContent = textoOriginal;
                btnGuardar.disabled = false;
            });
        });

        // Cerrar modal al hacer clic fuera de él
        window.onclick = function(event) {
            const modal = document.getElementById('modalInsertarCliente');
            if (event.target == modal) {
                cerrarModalInsertarCliente();
            }
        }
    </script>
</body>
</html>