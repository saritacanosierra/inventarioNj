<?php
require '../conexion.php';
require_once __DIR__ . '/../controllers/verificar_sesion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}

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
    <link rel="stylesheet" href="../css/registrar_venta.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
   
</head>
<body>
    
    <div class="contenedor-principal">
        <?php include '../components/header.php'; ?>
        
        <div class="contenido">
            <h2>Registrar Nueva Venta</h2>
            
            <div id="mensaje" class="mensaje" style="display: none;"></div>

            <form id="formVenta" method="POST" action="../controllers/ventas/procesar_venta.php">
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
                                    <div class="resumen-value" id="subtotal-venta">$0.00</div>
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
                            <h2><span class="material-icons" style="margin-right: 8px;">inventory</span>Seleccionar Productos</h2>

                            <div class="producto-seleccion-form">
                                <div class="form-group">
                                    <label for="select_categoria">Categoría:</label>
                                    <select id="select_categoria" onchange="filtrarProductosPorCategoria()">
                                        <option value="">Seleccione una categoría</option>
                                        <?php
                                        $categorias->data_seek(0);
                                        while ($categoria = $categorias->fetch_assoc()):
                                        ?>
                                            <option value="<?= $categoria['id'] ?>">
                                                <?= htmlspecialchars($categoria['nombre'] . ' (' . $categoria['codigo'] . ')') ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="select_producto">Producto:</label>
                                    <select id="select_producto" onchange="actualizarPrecioSeleccion()">
                                        <option value="">Seleccione un producto</option>
                                        <?php
                                        $productos->data_seek(0);
                                        while ($producto = $productos->fetch_assoc()):
                                        ?>
                                            <option value="<?= $producto['id'] ?>"
                                                    data-precio="<?= $producto['precio'] ?>"
                                                    data-stock="<?= $producto['stock'] ?>"
                                                    data-codigo="<?= $producto['codigo'] ?>"
                                                    data-nombre="<?= $producto['nombre'] ?>"
                                                    data-categoria="<?= $producto['id_categoria'] ?>">
                                                <?= htmlspecialchars($producto['codigo'] . ' - ' . $producto['nombre'] . ' (Stock: ' . $producto['stock'] . ')') ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <div class="producto-info-seleccion" id="info_producto_seleccion"></div>
                                </div>

                                <div class="form-group">
                                    <label for="input_cantidad">Cantidad:</label>
                                    <input type="number" id="input_cantidad" min="1" oninput="calcularSubtotalSeleccion()">
                                </div>

                                <div class="form-group">
                                    <label for="input_precio_unitario">Precio Unitario:</label>
                                    <input type="number" id="input_precio_unitario" step="0.01" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="input_subtotal_seleccion">Subtotal:</label>
                                    <input type="number" id="input_subtotal_seleccion" step="0.01" readonly>
                                </div>

                                <button type="button" class="btn-add-producto" onclick="agregarProductoATabla()">
                                    <span class="material-icons">add_shopping_cart</span>
                                    Agregar a la Venta
                                </button>
                            </div>

                            <div class="tabla-productos-venta-contenedor">
                                <h3>Productos Agregados a la Venta</h3>
                                <table class="tabla-productos-venta">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unit.</th>
                                            <th>Subtotal</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productos-agregados-table-body">
                                        <!-- Productos agregados dinámicamente aquí -->
                                    </tbody>
                                </table>
                            </div>
                            <input type="hidden" name="productos_venta_json" id="productos-venta-json">
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
            <form id="formInsertarCliente" class="form-insertar">
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
        // Datos de categorías disponibles (PHP fetched)
        const categorias = [
            <?php
            $categorias->data_seek(0);
            $categoriasArray = [];
            while ($categoria = $categorias->fetch_assoc()) {
                $categoriasArray[] = "{ id: {$categoria['id']}, codigo: '" . addslashes($categoria['codigo']) . "', nombre: '" . addslashes($categoria['nombre']) . "' }";
            }
            echo implode(",\n", $categoriasArray);
            ?>
        ];

        // Datos de productos disponibles (PHP fetched)
        const productos = [
            <?php
            $productos->data_seek(0);
            $productosArray = [];
            while ($producto = $productos->fetch_assoc()) {
                $productosArray[] = "{ id: {$producto['id']}, codigo: '" . addslashes($producto['codigo']) . "', nombre: '" . addslashes($producto['nombre']) . "', precio: {$producto['precio']}, stock: {$producto['stock']}, categoria_id: " . ($producto['id_categoria'] ?: 'null') . ", categoria: '" . addslashes($producto['categoria_nombre'] ?? 'Sin categoría') . "' }";
            }
            echo implode(",\n", $productosArray);
            ?>
        ];

        let productosAgregados = []; // Array para almacenar los productos de la venta

        // Establecer fecha actual
        document.getElementById('fecha_venta').value = new Date().toISOString().slice(0, 16);

        function filtrarProductosPorCategoria() {
            const categoriaId = document.getElementById('select_categoria').value;
            const selectProducto = document.getElementById('select_producto');
            const options = selectProducto.getElementsByTagName('option');

            // Resetear el select de productos y campos relacionados
            selectProducto.value = '';
            document.getElementById('input_precio_unitario').value = '';
            document.getElementById('input_cantidad').value = '';
            document.getElementById('input_subtotal_seleccion').value = '';
            document.getElementById('info_producto_seleccion').innerHTML = '';

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

        function actualizarPrecioSeleccion() {
            const select = document.getElementById('select_producto');
            const option = select.options[select.selectedIndex];
            const precioInput = document.getElementById('input_precio_unitario');
            const cantidadInput = document.getElementById('input_cantidad');
            const subtotalInput = document.getElementById('input_subtotal_seleccion');
            const infoDiv = document.getElementById('info_producto_seleccion');

            if (select.value) {
                const producto = productos.find(p => p.id == select.value);
                if (producto) {
                    precioInput.value = producto.precio.toFixed(2);
                    cantidadInput.max = producto.stock;
                    cantidadInput.value = ''; // Limpiar cantidad al cambiar producto
                    subtotalInput.value = ''; // Limpiar subtotal

                    let stockClass = 'stock-disponible';
                    if (producto.stock <= 5 && producto.stock > 0) {
                        stockClass = 'stock-bajo';
                    } else if (producto.stock === 0) {
                        stockClass = 'sin-stock';
                    }

                    infoDiv.innerHTML = `
                        <span class="${stockClass}">Stock disponible: ${producto.stock}</span><br>
                        <span>Categoría: ${producto.categoria}</span>
                    `;
                }
            } else {
                precioInput.value = '';
                cantidadInput.max = '';
                cantidadInput.value = '';
                subtotalInput.value = '';
                infoDiv.innerHTML = '';
            }
        }

        function calcularSubtotalSeleccion() {
            const cantidad = parseFloat(document.getElementById('input_cantidad').value) || 0;
            const precioUnitario = parseFloat(document.getElementById('input_precio_unitario').value) || 0;
            const subtotalInput = document.getElementById('input_subtotal_seleccion');

            if (cantidad && precioUnitario) {
                const subtotal = (cantidad * precioUnitario).toFixed(2);
                subtotalInput.value = subtotal;
            } else {
                subtotalInput.value = '';
            }
        }

        function agregarProductoATabla() {
            const selectProducto = document.getElementById('select_producto');
            const inputCantidad = document.getElementById('input_cantidad');
            const inputPrecioUnitario = document.getElementById('input_precio_unitario');
            const inputSubtotal = document.getElementById('input_subtotal_seleccion');
            const mensajeError = document.getElementById('mensaje');

            mensajeError.style.display = 'none';

            if (!selectProducto.value || !inputCantidad.value || !inputPrecioUnitario.value || !inputSubtotal.value) {
                mensajeError.textContent = 'Por favor, complete todos los campos del producto.';
                mensajeError.style.display = 'block';
                return;
            }

            const productoId = selectProducto.value;
            const cantidad = parseInt(inputCantidad.value);
            const precioUnitario = parseFloat(inputPrecioUnitario.value);
            const subtotal = parseFloat(inputSubtotal.value);

            const productoExistenteIndex = productosAgregados.findIndex(p => p.id == productoId);
            const productoOriginal = productos.find(p => p.id == productoId);

            if (!productoOriginal) {
                mensajeError.textContent = 'Producto no válido.';
                mensajeError.style.display = 'block';
                return;
            }

            if (cantidad > productoOriginal.stock) {
                mensajeError.textContent = `La cantidad (${cantidad}) excede el stock disponible (${productoOriginal.stock}) para ${productoOriginal.nombre}.`;
                mensajeError.style.display = 'block';
                return;
            }

            if (productoExistenteIndex > -1) {
                // Actualizar cantidad y subtotal si el producto ya está en la tabla
                const productoEnTabla = productosAgregados[productoExistenteIndex];
                const nuevaCantidad = productoEnTabla.cantidad + cantidad;

                if (nuevaCantidad > productoOriginal.stock) {
                    mensajeError.textContent = `La cantidad total (${nuevaCantidad}) excede el stock disponible (${productoOriginal.stock}) para ${productoOriginal.nombre}.`;
                    mensajeError.style.display = 'block';
                    return;
                }

                productoEnTabla.cantidad = nuevaCantidad;
                productoEnTabla.subtotal = (nuevaCantidad * productoEnTabla.precio_unitario).toFixed(2);
            } else {
                // Agregar nuevo producto
                productosAgregados.push({
                    id: productoId,
                    codigo: productoOriginal.codigo,
                    nombre: productoOriginal.nombre,
                    cantidad: cantidad,
                    precio_unitario: precioUnitario,
                    subtotal: subtotal
                });
            }

            renderizarTablaProductos();
            calcularTotales();

            // Limpiar formulario de selección
            document.getElementById('select_categoria').value = '';
            selectProducto.value = '';
            inputCantidad.value = '';
            inputPrecioUnitario.value = '';
            inputSubtotal.value = '';
            document.getElementById('info_producto_seleccion').innerHTML = '';
            // Restablecer opciones de producto para que se muestren todas
            filtrarProductosPorCategoria();
        }

        function renderizarTablaProductos() {
            const tbody = document.getElementById('productos-agregados-table-body');
            tbody.innerHTML = ''; // Limpiar tabla

            productosAgregados.forEach((producto, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${htmlspecialchars(producto.codigo)}</td>
                    <td>${htmlspecialchars(producto.nombre)}</td>
                    <td>${producto.cantidad}</td>
                    <td>$${parseFloat(producto.precio_unitario).toFixed(2)}</td>
                    <td>$${parseFloat(producto.subtotal).toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn-eliminar-producto-tabla" onclick="eliminarProductoDeTabla(${index})">
                            <span class="material-icons">remove_circle</span>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function eliminarProductoDeTabla(index) {
            productosAgregados.splice(index, 1); // Eliminar del array
            renderizarTablaProductos(); // Volver a renderizar la tabla
            calcularTotales(); // Recalcular totales
        }

        function calcularTotales() {
            let cantidadProductos = productosAgregados.length;
            let totalUnidades = 0;
            let subtotalVenta = 0;

            productosAgregados.forEach(producto => {
                totalUnidades += producto.cantidad;
                subtotalVenta += parseFloat(producto.subtotal);
            });

            document.getElementById('cantidad-productos').textContent = cantidadProductos;
            document.getElementById('total-unidades').textContent = totalUnidades;
            document.getElementById('subtotal-venta').textContent = `$${subtotalVenta.toFixed(2)}`;
            document.getElementById('total-general').textContent = `$${subtotalVenta.toFixed(2)}`;

            // Habilitar/deshabilitar botón de guardar
            document.getElementById('btn-guardar').disabled = cantidadProductos === 0;

            // Actualizar el campo JSON oculto para el envío
            document.getElementById('productos-venta-json').value = JSON.stringify(productosAgregados);
        }

        // Manejar envío del formulario principal
        document.getElementById('formVenta').addEventListener('submit', function(e) {
            e.preventDefault(); // Evitar el envío tradicional del formulario

            // Asegurarse de que el JSON de productos esté actualizado
            calcularTotales();

            if (productosAgregados.length === 0) {
                const mensajeError = document.getElementById('mensaje');
                mensajeError.textContent = 'Debe agregar al menos un producto a la venta para guardar.';
                mensajeError.className = 'mensaje error';
                mensajeError.style.display = 'block';
                return;
            }

            const formData = new FormData(this);

            // Agregar el JSON de productos al formData para enviar
            formData.append('productos_venta_json', document.getElementById('productos-venta-json').value);

            // Deshabilitar botón de guardar y mostrar mensaje de proceso
            const btnGuardar = document.getElementById('btn-guardar');
            const originalBtnText = btnGuardar.innerHTML;
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<span class="material-icons">hourglass_empty</span> Guardando...';

            fetch(this.action, { // Usar la acción del formulario como URL
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const mensajeExito = document.getElementById('mensaje');
                    mensajeExito.textContent = data.message || 'Venta guardada exitosamente!';
                    mensajeExito.className = 'mensaje exito';
                    mensajeExito.style.display = 'block';

                    // Limpiar el formulario y la tabla de productos después de un éxito
                    document.getElementById('formVenta').reset();
                    productosAgregados = [];
                    renderizarTablaProductos();
                    calcularTotales(); // Resetear totales y deshabilitar botón

                    // Redirigir a la vista de ventas después de un breve retraso
                    setTimeout(() => {
                        const status = data.success ? 'success' : 'error';
                        const message = encodeURIComponent(data.message || 'Venta guardada exitosamente!');
                        window.location.href = `ventas.php?status=${status}&message=${message}`;
                    }, 2000); // 2 segundos de espera

                } else {
                    const mensajeError = document.getElementById('mensaje');
                    mensajeError.textContent = data.message || 'Error al guardar la venta.';
                    mensajeError.className = 'mensaje error';
                    mensajeError.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const mensajeError = document.getElementById('mensaje');
                mensajeError.textContent = 'Error de conexión o servidor. Por favor, intente de nuevo.';
                mensajeError.className = 'mensaje error';
                mensajeError.style.display = 'block';
            })
            .finally(() => {
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = originalBtnText;
            });
        });

        // Funciones del modal para insertar cliente (EXISTING CODE)
        function abrirModalInsertarCliente() {
            document.getElementById('modalInsertarCliente').style.display = 'block';
            document.getElementById('mensaje-error-insertar-cliente').style.display = 'none';
        }

        function cerrarModalInsertarCliente() {
            document.getElementById('modalInsertarCliente').style.display = 'none';
            document.getElementById('formInsertarCliente').reset();
            document.getElementById('mensaje-error-insertar-cliente').style.display = 'none';
        }

        document.getElementById('formInsertarCliente').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const mensajeError = document.getElementById('mensaje-error-insertar-cliente');
            mensajeError.style.display = 'none';

            fetch('../controllers/clientes/insertar_cliente_venta.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Respuesta del servidor (cliente):', response.status);
                if (!response.ok) {
                    throw new Error('Error de conexión: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos (cliente):', data);
                if (data.success) {
                    cerrarModalInsertarCliente();
                    alert('Cliente agregado exitosamente');
                    console.log('Cliente creado con ID:', data.cliente_id);
                    // Aquí podrías actualizar la lista de clientes si es necesario
                    location.reload(); // Recargar para actualizar el select de clientes
                } else {
                    mensajeError.textContent = data.message;
                    mensajeError.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mensajeError.textContent = 'Error de conexión: ' + error.message;
                mensajeError.style.display = 'block';
            });
        });

        // Helper para escapar HTML, útil para los nombres de productos en la tabla
        function htmlspecialchars(str) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return str.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Inicializar cálculos al cargar la página
        document.addEventListener('DOMContentLoaded', (event) => {
            calcularTotales(); // Asegura que el botón guardar esté deshabilitado si no hay productos
        });
    </script>
</body>
</html>