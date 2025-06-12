<?php
require '../conexion.php';

$sql = "SELECT v.*, p.nombre as nombre_producto, p.codigo as codigo_producto, 
        DATE_FORMAT(v.fecha_venta, '%Y-%m-%d %H:%i:%s') as fecha_venta 
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
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
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
                                <td><?php echo $venta['nombre_producto']; ?></td>
                                <td><?php echo $venta['cantidad']; ?></td>
                                <td>$<?php echo number_format($venta['precio_unitario'], 2); ?></td>
                                <td>$<?php echo number_format($venta['total'], 2); ?></td>
                                <td class="acciones">
                                    <button onclick="abrirModalEditar(<?php echo htmlspecialchars(json_encode($venta)); ?>)" class="btn-editar">
                                        <span class="material-icons">edit</span>
                                    </button>
                                    <a href="../controllers/eliminar_venta.php?id=<?php echo $venta['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar esta venta?')">
                                        <span class="material-icons">delete</span>
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
                    <label for="id_producto">Producto:</label>
                    <select id="id_producto" name="id_producto" required onchange="actualizarPrecio()">
                        <option value="">Seleccione un producto</option>
                        <?php
                        $sql_productos = "SELECT id, codigo, nombre, precio, stock FROM productos WHERE stock > 0";
                        $productos = $conexion->query($sql_productos);
                        while ($producto = $productos->fetch_assoc()) {
                            echo "<option value='{$producto['id']}' data-precio='{$producto['precio']}' data-stock='{$producto['stock']}'>{$producto['codigo']} - {$producto['nombre']} (Stock: {$producto['stock']})</option>";
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
                <div class="form-buttons">
                    <button type="submit" class="btn-guardar">Guardar Cambios</button>
                    <button type="button" class="btn-cancelar" onclick="cerrarModalEditar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Funciones para el modal de insertar
        function abrirModalInsertar() {
            document.getElementById('modalInsertar').style.display = 'block';
        }

        function cerrarModalInsertar() {
            document.getElementById('modalInsertar').style.display = 'none';
            document.getElementById('formInsertar').reset();
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
        }

        function cerrarModalEditar() {
            document.getElementById('modalEditar').style.display = 'none';
            document.getElementById('formEditar').reset();
        }

        // Funciones para cálculos
        function actualizarPrecio() {
            const select = document.getElementById('id_producto');
            const option = select.options[select.selectedIndex];
            const precio = option.getAttribute('data-precio');
            const stock = option.getAttribute('data-stock');
            
            document.getElementById('precio_unitario').value = precio;
            document.getElementById('cantidad').max = stock;
            calcularTotal();
        }

        function calcularTotal() {
            const cantidad = document.getElementById('cantidad').value;
            const precio = document.getElementById('precio_unitario').value;
            document.getElementById('total').value = (cantidad * precio).toFixed(2);
        }

        function actualizarPrecioEditar() {
            const select = document.getElementById('edit_id_producto');
            const option = select.options[select.selectedIndex];
            const precio = option.getAttribute('data-precio');
            const stock = option.getAttribute('data-stock');
            
            document.getElementById('edit_precio_unitario').value = precio;
            document.getElementById('edit_cantidad').max = stock;
            calcularTotalEditar();
        }

        function calcularTotalEditar() {
            const cantidad = document.getElementById('edit_cantidad').value;
            const precio = document.getElementById('edit_precio_unitario').value;
            document.getElementById('edit_total').value = (cantidad * precio).toFixed(2);
        }

        // Manejar el envío del formulario de insertar
        document.getElementById('formInsertar').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            // Asegurarse de que la fecha esté en el formato correcto
            const fechaVenta = formData.get('fecha_venta');
            if (!fechaVenta) {
                alert('Por favor seleccione una fecha');
                return;
            }
            formData.set('fecha_venta', fechaVenta.replace('T', ' '));
            
            const mensajeError = document.getElementById('mensaje-error-insertar');
            mensajeError.style.display = 'none';
            
            fetch('../controllers/insertar_venta.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Agregar la nueva venta a la tabla
                    const tbody = document.getElementById('tabla-ventas');
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
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
                        <td class="acciones">
                            <button onclick="abrirModalEditar(${JSON.stringify(data)})" class="btn-editar">
                                <span class="material-icons">edit</span>
                            </button>
                            <a href="../controllers/eliminar_venta.php?id=${data.id}" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar esta venta?')">
                                <span class="material-icons">delete</span>
                            </a>
                        </td>
                    `;
                    tbody.insertBefore(newRow, tbody.firstChild);
                    
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
                            <td class="acciones">
                                <button onclick="abrirModalEditar(${JSON.stringify(data)})" class="btn-editar">
                                    <span class="material-icons">edit</span>
                                </button>
                                <a href="../controllers/eliminar_venta.php?id=${data.id}" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar esta venta?')">
                                    <span class="material-icons">delete</span>
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
    </script>
</body>
</html>