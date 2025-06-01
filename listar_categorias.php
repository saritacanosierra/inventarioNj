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
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/tablas.css">
</head>
<body>
    <div class="contenedor-principal">
        <?php include 'components/header.php'; ?>
        
        <div class="contenido">
            <div class="titulo-seccion">
                <h1>Lista de Categorías</h1>
            </div>
            <div class="filtro-agregar-contenedor">
                <div class="filtro-contenedor">
                    <input type="text" id="filtro-categoria" placeholder="Buscar categoría..." class="filtro-input">
                </div>
                <div class="btn-agregar-contenedor">
                    <button onclick="abrirModal()" class="btn-agregar">+</button>
                </div>
            </div>

            <?php if ($resultado->num_rows === 0): ?>
                <p class="mensaje">No hay categorías registradas.</p>
            <?php else: ?>
                <div class="tabla-contenedor">
                    <table id="tabla-categorias">
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

    <style>
        .filtro-agregar-contenedor {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            width: 100%;
            padding: 0 15px;
            gap: 2px;
        }

        .filtro-contenedor {
            flex: 1;
            margin-right: 10px;
            max-width: calc(100% - 40px);
        }

        .filtro-input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            font-size: 14px;
            transition: border-color 0.3s;
            min-width: 300px;
        }

        .filtro-input:focus {
            outline: none;
            border-color: #E1B8E2;
            box-shadow: 0 0 0 2px rgba(225, 184, 226, 0.25);
        }

        .filtro-input::placeholder {
            color: #999;
        }
    </style>

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

        // Función para filtrar categorías
        document.getElementById('filtro-categoria').addEventListener('keyup', function() {
            const filtro = this.value.toLowerCase();
            const tabla = document.getElementById('tabla-categorias');
            const filas = tabla.getElementsByTagName('tr');

            for (let i = 0; i < filas.length; i++) {
                const fila = filas[i];
                const celdas = fila.getElementsByTagName('td');
                let mostrar = false;

                // Buscar en todas las celdas excepto la última (acciones)
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
</body>
</html>
<?php $conexion->close(); ?>