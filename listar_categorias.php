<?php
require 'conexion.php';

$sql = "SELECT id,codigo, nombre, ubicacion FROM categoria";
$resultado = $conexion->query($sql);

if (!$resultado) {
    die('Error al obtener la lista de categorías: ' . $conexion->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Categorías</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/listarCategorias.css">
</head>
<body>
    <div class="contenedor-principal">
        <?php include 'components/header.php'; ?>
        
        <div class="contenido">
            <div class="titulo-seccion">
                <h1>Lista de Categorías</h1>
            </div>
            <div class="btn-agregar-contenedor">
                <button onclick="abrirModal()" class="btn-agregar">+</button>
            </div>

            <?php if ($resultado->num_rows === 0): ?>
                <p class="mensaje">No hay categorías registradas.</p>
            <?php else: ?>
                <div class="tabla-contenedor">
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Ubicación</th>
                               
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($categoria = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $categoria['codigo']; ?></td>
                                    <td><?php echo $categoria['nombre']; ?></td>
                                    <td><?php echo $categoria['ubicacion']; ?></td>
                                    <td class="acciones">
                                        <a href="editar_categorias.php?id=<?php echo $categoria['id']; ?>" class="btn-editar">
                                            <span class="material-icons">edit</span>
                                            Editar
                                        </a>
                                        <a href="eliminar_categorias.php?id=<?php echo $categoria['id']; ?>" 
                                           onclick="return confirm('¿Estás seguro de eliminar esta categoría?')" 
                                           class="btn-eliminar">
                                            <span class="material-icons">delete</span>
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para agregar categoría -->
    <div id="modalAgregar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h2>Agregar Nueva Categoría</h2>
            <form id="formCategoria" class="modal-form" enctype="multipart/form-data">
                <div class="modal-form-group">
                    <label for="codigo">Código:</label>
                    <input type="text" id="codigo" name="codigo" required>
                </div>
                <div class="modal-form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="modal-form-group">
                    <label for="ubicacion">Ubicación:</label>
                    <input type="text" id="ubicacion" name="ubicacion" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn modal-btn-secondary" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="modal-btn modal-btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Funciones para el modal
        function abrirModal() {
            document.getElementById('modalAgregar').style.display = 'block';
        }

        function cerrarModal() {
            document.getElementById('modalAgregar').style.display = 'none';
            document.getElementById('formCategoria').reset();
        }

        // Cerrar el modal si se hace clic fuera de él
        window.onclick = function(event) {
            var modal = document.getElementById('modalAgregar');
            if (event.target == modal) {
                cerrarModal();
            }
        }

        // Manejar el envío del formulario
        document.getElementById('formCategoria').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('procesar_categoria.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    cerrarModal();
                    // Recargar la página para mostrar la nueva categoría
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
        });
    </script>
</body>
</html>
<?php $conexion->close(); ?>