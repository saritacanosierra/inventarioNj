<?php
require 'conexion.php';

$sql = "SELECT id, codigo, nombre, precio, stock, foto FROM productos";
$resultado = $conexion->query($sql);

if (!$resultado) {
    die('Error al obtener la lista de productos: ' . $conexion->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de productos</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/listarUsuario.css">

</head>
<body>
    <div class="contenedor-principal">
        <?php include './components/header.php'; ?>
        
        <h1 id="bienvenida">Lista de productos</h1>
        
        <div class="contenido">
            <div class="btn-agregar-contenedor">
                <button onclick="abrirModal()" class="btn-agregar">+</button>
            </div>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Foto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado->num_rows === 0): ?>
                            <tr><td colspan="7" style="text-align: center;">No hay productos registrados</td></tr>
                        <?php else: ?>
                            <?php while ($producto = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $producto['id']; ?></td>
                                    <td><?php echo $producto['codigo']; ?></td>
                                    <td><?php echo $producto['nombre']; ?></td>
                                    <td><?php echo $producto['precio']; ?></td>
                                    <td><?php echo $producto['stock']; ?></td>
                                    <td><?php echo $producto['foto']; ?></td>
                                    <td class="acciones">
                                        <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" class="btn-editar">Editar</a>
                                        <a href="eliminar_producto.php?id=<?php echo $producto['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este producto?')">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para insertar producto -->
    <div id="modalInsertar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h2>Agregar Nuevo Producto</h2>
            <div id="mensaje-error" class="mensaje error" style="display: none;"></div>
            <form id="formInsertar" class="form-insertar">
                <div class="form-group">
                    <label for="codigo">Código:</label>
                    <input type="text" id="codigo" name="codigo" required>
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="precio">Precio:</label>
                    <input type="text" id="precio" name="precio" required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input type="text" id="stock" name="stock" required>
                </div>
                <div class="form-group">
                    <label for="foto">Foto:</label>
                    <input type="text" id="foto" name="foto" required>
                </div>
                <div class="form-buttons">
                    <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="btn-guardar">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function abrirModal() {
        document.getElementById('modalInsertar').style.display = 'block';
        document.getElementById('formInsertar').reset();
        document.getElementById('mensaje-error').style.display = 'none';
    }

    function cerrarModal() {
        document.getElementById('modalInsertar').style.display = 'none';
    }

    function mostrarError(mensaje) {
        const errorDiv = document.getElementById('mensaje-error');
        errorDiv.textContent = mensaje;
        errorDiv.style.display = 'block';
    }

    document.getElementById('formInsertar').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('insertar_producto.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                mostrarError(data.error);
            } else {
                cerrarModal();
                location.reload();
            }
        })
        .catch(error => {
            mostrarError('Error al procesar la solicitud');
        });
    });

    // Cerrar el modal si se hace clic fuera de él
    window.onclick = function(event) {
        const modal = document.getElementById('modalInsertar');
        if (event.target == modal) {
            cerrarModal();
        }
    }
    </script>
</body>
</html>
<?php $conexion->close(); ?>