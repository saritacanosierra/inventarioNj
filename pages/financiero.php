<?php
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../controllers/verificar_sesion.php';

$sql = "SELECT * FROM financiera";
$resultado = $conexion->query($sql);

if (!$resultado) {
    die('Error al obtener la lista de registros financieros: ' . $conexion->error);
}

$uploadDir = '../../uploads/productos/';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros Financieros - Sistema de Inventario</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/tablas.css">
    <link rel="stylesheet" href="../css/financiero.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<div class="contenedor-principal">
        <?php include '../components/header.php'; ?>
        
    <div class="container">
            <h2>Registros Financieros, de compras y gastos</h2>
            
            <div class="filtro-agregar-contenedor">
                <div class="filtro-contenedor">
                    <input type="text" id="filtro-financiero" placeholder="Buscar registro..." class="filtro-input">
                </div>
                <div class="btn-agregar-contenedor">
                    <button onclick="abrirModalInsertar()" class="btn-agregar">+</button>
                </div>
            </div>
            <div class="contenedor">
                <div class="tabla-contenedor">
                    <table>
                        <thead>
                            <tr>
                                <th>Código proveedor</th>
                                <th>Nombre proveedor</th>
                                <th>Fecha compra</th>
                                <th>Valor compra</th>
                                <th>Número teléfono</th>
                                <th>Cantidad comprada</th>
                                <th>Tipo de compra</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-financiera">
                            <?php while ($registro = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $registro['codigoProveedor']; ?></td>
                                    <td><?php echo $registro['nombreProveedor']; ?></td>
                                    <td><?php echo $registro['fechaCompra']; ?></td>
                                    <td><?php echo $registro['valorCompra']; ?></td>
                                    <td><?php echo $registro['numeroTelefono']; ?></td>
                                    <td><?php echo $registro['cantidadComprada']; ?></td>
                                    <td><?php echo $registro['tipoCompra']; ?></td>
                                    <td class="acciones">
                                        <button onclick="abrirModalEditar(<?php echo htmlspecialchars(json_encode($registro)); ?>)" class="btn-editar">
                                            <span class="material-icons">edit</span>
                                        </button>
                                        <a href="../controllers/financiero/eliminar_financiero.php?id=<?php echo $registro['codigoProveedor']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este registro?')">
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
    </div>

    <!-- Modal de Insertar Registro -->
    <div id="modalInsertar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalInsertar()">&times;</span>
            <h2>Insertar Nuevo Registro</h2>
            <div id="mensaje-error-insertar" class="mensaje error" style="display: none;"></div>
            <form id="formInsertar" class="form-insertar">
                <div class="form-group">
                    <label for="codigoProveedor">Código Proveedor:</label>
                    <input type="number" id="codigoProveedor" name="codigoProveedor" required>
                </div>
                <div class="form-group">
                    <label for="nombreProveedor">Nombre Proveedor:</label>
                    <input type="text" id="nombreProveedor" name="nombreProveedor" required>
                </div>
                <div class="form-group">
                    <label for="fechaCompra">Fecha Compra:</label>
                    <input type="date" id="fechaCompra" name="fechaCompra" required>
                </div>
                <div class="form-group">
                    <label for="valorCompra">Valor Compra:</label>
                    <input type="number" id="valorCompra" name="valorCompra" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="numeroTelefono">Número Teléfono:</label>
                    <input type="tel" id="numeroTelefono" name="numeroTelefono" required>
                </div>
                <div class="form-group">
                    <label for="cantidadComprada">Cantidad Comprada:</label>
                    <input type="number" id="cantidadComprada" name="cantidadComprada" required>
                </div>
                <div class="form-group">
                    <label for="tipoCompra">Tipo de Compra:</label>
                    <select id="tipoCompra" name="tipoCompra" required>
                        <option value="gasto">Gasto</option>
                        <option value="inversion">Inversión</option>
                        <option value="compra">Compra</option>
                    </select>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-guardar">Guardar</button>
                    <button type="button" class="btn-cancelar" onclick="cerrarModalInsertar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Editar Registro -->
    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalEditar()">&times;</span>
            <h2>Editar Registro</h2>
            <div id="mensaje-error-editar" class="mensaje error" style="display: none;"></div>
            <form id="formEditar" class="form-editar">
                <input type="hidden" id="edit-codigoProveedor" name="codigoProveedor">
                <div class="form-group">
                    <label for="edit-nombreProveedor">Nombre Proveedor:</label>
                    <input type="text" id="edit-nombreProveedor" name="nombreProveedor" required>
                </div>
                <div class="form-group">
                    <label for="edit-fechaCompra">Fecha Compra:</label>
                    <input type="date" id="edit-fechaCompra" name="fechaCompra" required>
                </div>
                <div class="form-group">
                    <label for="edit-valorCompra">Valor Compra:</label>
                    <input type="number" id="edit-valorCompra" name="valorCompra" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="edit-numeroTelefono">Número Teléfono:</label>
                    <input type="tel" id="edit-numeroTelefono" name="numeroTelefono" required>
                </div>
                <div class="form-group">
                    <label for="edit-cantidadComprada">Cantidad Comprada:</label>
                    <input type="number" id="edit-cantidadComprada" name="cantidadComprada" required>
                </div>
                <div class="form-group">
                    <label for="edit-tipoCompra">Tipo de Compra:</label>
                    <select id="edit-tipoCompra" name="tipoCompra" required>
                        <option value="gasto">Gasto</option>
                        <option value="inversion">Inversión</option>
                        <option value="compra">Compra</option>
                    </select>
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
            document.getElementById('mensaje-error-insertar').style.display = 'none';
        }

        function cerrarModalInsertar() {
            document.getElementById('modalInsertar').style.display = 'none';
            document.getElementById('formInsertar').reset();
            document.getElementById('mensaje-error-insertar').style.display = 'none';
        }

        // Funciones para el modal de editar
        function abrirModalEditar(registro) {
            document.getElementById('modalEditar').style.display = 'block';
            document.getElementById('mensaje-error-editar').style.display = 'none';
            
            document.getElementById('edit-codigoProveedor').value = registro.codigoProveedor;
            document.getElementById('edit-nombreProveedor').value = registro.nombreProveedor;
            document.getElementById('edit-fechaCompra').value = registro.fechaCompra;
            document.getElementById('edit-valorCompra').value = registro.valorCompra;
            document.getElementById('edit-numeroTelefono').value = registro.numeroTelefono;
            document.getElementById('edit-cantidadComprada').value = registro.cantidadComprada;
            document.getElementById('edit-tipoCompra').value = registro.tipoCompra;
        }

        function cerrarModalEditar() {
            document.getElementById('modalEditar').style.display = 'none';
            document.getElementById('formEditar').reset();
            document.getElementById('mensaje-error-editar').style.display = 'none';
        }

        // Cerrar modales al hacer clic fuera
        window.onclick = function(event) {
            var modalInsertar = document.getElementById('modalInsertar');
            var modalEditar = document.getElementById('modalEditar');
            if (event.target == modalInsertar) {
                cerrarModalInsertar();
            }
            if (event.target == modalEditar) {
                cerrarModalEditar();
            }
        }

        // Manejar el envío del formulario de insertar
        document.getElementById('formInsertar').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const mensajeError = document.getElementById('mensaje-error-insertar');
            mensajeError.style.display = 'none';
            
            fetch('../controllers/financiero/insertar_financiero.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Agregar el nuevo registro a la tabla
                    const tbody = document.getElementById('tabla-financiera');
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                        <td>${data.codigoProveedor}</td>
                        <td>${data.nombreProveedor}</td>
                        <td>${data.fechaCompra}</td>
                        <td>${data.valorCompra}</td>
                        <td>${data.numeroTelefono}</td>
                        <td>${data.cantidadComprada}</td>
                        <td>${data.tipoCompra}</td>
                        <td class="acciones">
                            <button onclick="abrirModalEditar(${JSON.stringify(data)})" class="btn-editar">
                                <span class="material-icons">edit</span>
                            </button>
                            <a href="../controllers/financiero/eliminar_financiero.php?id=${data.codigoProveedor}" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este registro?')">
                                <span class="material-icons">delete</span>
                            </a>
                        </td>
                    `;
                    tbody.insertBefore(newRow, tbody.firstChild);
                    
                    cerrarModalInsertar();
                    alert('Registro agregado exitosamente');
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
    const mensajeError = document.getElementById('mensaje-error-editar');
    mensajeError.style.display = 'none';
    
    fetch('../controllers/financiero/actualizar_financiero.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar la fila en la tabla
            const filas = document.querySelectorAll('#tabla-financiera tr');
            filas.forEach(fila => {
                const codigoCelda = fila.querySelector('td:first-child');
                if (codigoCelda && codigoCelda.textContent === data.codigoProveedor.toString()) {
                    fila.innerHTML = `
                        <td>${data.codigoProveedor}</td>
                        <td>${data.nombreProveedor}</td>
                        <td>${data.fechaCompra}</td>
                        <td>${data.valorCompra}</td>
                        <td>${data.numeroTelefono}</td>
                        <td>${data.cantidadComprada}</td>
                        <td>${data.tipoCompra}</td>
                        <td class="acciones">
                            <button onclick="abrirModalEditar(${JSON.stringify(data)})" class="btn-editar">
                                <span class="material-icons">edit</span>
                            </button>
                            <a href="../controllers/financiero/eliminar_financiero.php?id=${data.codigoProveedor}" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este registro?')">
                                <span class="material-icons">delete</span>
                            </a>
                        </td>
                    `;
                }
            });
            cerrarModalEditar();
            alert('Registro actualizado exitosamente');
        } else {
            // Solo mostrar error si el backend dice que no se hizo el cambio
            mensajeError.textContent = data.message || 'No se pudo actualizar el registro.';
            mensajeError.style.display = 'block';
        }
    })
    // Solo mostrar error si el fetch realmente falla (por ejemplo, servidor caído)
    .catch(error => {
        console.error('Error:', error);
        mensajeError.textContent = 'Error de conexión con el servidor.';
        mensajeError.style.display = 'block';
    });
});
        // Función para filtrar registros
        document.getElementById('filtro-financiero').addEventListener('keyup', function() {
            const filtro = this.value.toLowerCase();
            const tabla = document.getElementById('tabla-financiera');
            const filas = tabla.getElementsByTagName('tr');

            for (let i = 0; i < filas.length; i++) {
                const fila = filas[i];
                const celdas = fila.getElementsByTagName('td');
                let mostrar = false;

                for (let j = 0; j < celdas.length - 1; j++) {
                    const texto = celdas[j].textContent || celdas[j].innerText;
                    if (texto.toLowerCase().indexOf(filtro) > -1) {
                        mostrar = true;
                        break;
                    }
                }

                fila.style.display = mostrar ? '' : 'none';
            }
        });
    </script>

<?php include '../components/footer.php'; ?>
<?php $conexion->close(); ?>
