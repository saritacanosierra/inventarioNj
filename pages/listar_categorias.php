<?php
require '../conexion.php';

$sql = "SELECT id, codigo, nombre, ubicacion FROM categoria";
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
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/tablas.css">
    <link rel="stylesheet" href="../css/listarCategorias.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="contenedor-principal">
        <?php include '../components/header.php'; ?>
        
        <div class="contenido">
            <h2>Lista de Categorías</h2>
            
            <div class="filtro-agregar-contenedor">
                <div class="filtro-contenedor">
                    <input type="text" id="filtro-categoria" placeholder="Buscar categoría..." class="filtro-input">
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
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-categorias">
                        <?php while ($categoria = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $categoria['id']; ?></td>
                                <td><?php echo $categoria['codigo']; ?></td>
                                <td><?php echo $categoria['nombre']; ?></td>
                                <td><?php echo $categoria['ubicacion']; ?></td>
                                <td class="acciones">
                                    <button onclick="abrirModalEditar(<?php echo htmlspecialchars(json_encode($categoria)); ?>)" class="btn-editar">
                                        <span class="material-icons">edit</span>
                                    </button>
                                    <a href="../controllers/categoria/eliminar_categorias.php?id=<?php echo $categoria['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">
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

    <!-- Modal de Insertar Categoría -->
    <div id="modalInsertar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalInsertar()">&times;</span>
            <h2>Insertar Nueva Categoría</h2>
            <div id="mensaje-error-insertar" class="mensaje error" style="display: none;"></div>
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
                    <label for="ubicacion">Ubicación:</label>
                    <input type="text" id="ubicacion" name="ubicacion" required>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-guardar">Guardar</button>
                    <button type="button" class="btn-cancelar" onclick="cerrarModalInsertar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Editar Categoría -->
    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalEditar()">&times;</span>
            <h2>Editar Categoría</h2>
            <div id="mensaje-error-editar" class="mensaje error" style="display: none;"></div>
            <form id="formEditar" class="form-editar">
                <input type="hidden" id="edit-id" name="id">
                <div class="form-group">
                    <label for="edit-codigo">Código:</label>
                    <input type="text" id="edit-codigo" name="codigo" required>
                </div>
                <div class="form-group">
                    <label for="edit-nombre">Nombre:</label>
                    <input type="text" id="edit-nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="edit-ubicacion">Ubicación:</label>
                    <input type="text" id="edit-ubicacion" name="ubicacion" required>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-guardar">Guardar Cambios</button>
                    <button type="button" class="btn-cancelar" onclick="cerrarModalEditar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

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
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-buttons {
            margin-top: 20px;
            text-align: right;
        }

        .btn-guardar, .btn-cancelar {
            padding: 8px 20px;
            margin-left: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-guardar {
            background-color: #E1B8E2;
            color: black;
        }

        .btn-cancelar {
            background-color: #f44336;
            color: white;
        }

        .mensaje {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
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

        .btn-editar, .btn-eliminar {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
        }

        .btn-editar .material-icons {
            color: #2196F3;
        }

        .btn-eliminar .material-icons {
            color: #f44336;
        }

        .btn-editar:hover .material-icons {
            color: #1976D2;
        }

        .btn-eliminar:hover .material-icons {
            color: #d32f2f;
        }
    </style>

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
        function abrirModalEditar(categoria) {
            document.getElementById('modalEditar').style.display = 'block';
            document.getElementById('mensaje-error-editar').style.display = 'none';
            
            // Llenar el formulario con los datos de la categoría
            document.getElementById('edit-id').value = categoria.id;
            document.getElementById('edit-codigo').value = categoria.codigo;
            document.getElementById('edit-nombre').value = categoria.nombre;
            document.getElementById('edit-ubicacion').value = categoria.ubicacion;
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
            
            fetch('../controllers/categoria/procesar_categoria.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Agregar la nueva categoría a la tabla
                    const tbody = document.getElementById('tabla-categorias');
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                        <td>${data.id}</td>
                        <td>${data.codigo}</td>
                        <td>${data.nombre}</td>
                        <td>${data.ubicacion}</td>
                        <td class="acciones">
                            <button onclick="abrirModalEditar(${JSON.stringify(data)})" class="btn-editar">
                                <span class="material-icons">edit</span>
                            </button>
                            <a href="../controllers/categoria/eliminar_categorias.php?id=${data.id}" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                <span class="material-icons">delete</span>
                            </a>
                        </td>
                    `;
                    tbody.insertBefore(newRow, tbody.firstChild);
                    
                    cerrarModalInsertar();
                    alert('Categoría agregada exitosamente');
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
            
            fetch('../controllers/categoria/actualizar_categoria.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar la fila en la tabla
                    const rows = document.querySelectorAll('#tabla-categorias tr');
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
                            <td>${data.codigo}</td>
                            <td>${data.nombre}</td>
                            <td>${data.ubicacion}</td>
                            <td class="acciones">
                                <button onclick="abrirModalEditar(${JSON.stringify(data)})" class="btn-editar">
                                    <span class="material-icons">edit</span>
                                </button>
                                <a href="../controllers/categoria/eliminar_categorias.php?id=${data.id}" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                    <span class="material-icons">delete</span>
                                </a>
                            </td>
                        `;
                    }
                    
                    cerrarModalEditar();
                    alert('Categoría actualizada exitosamente');
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

        // Función para filtrar categorías
        document.getElementById('filtro-categoria').addEventListener('keyup', function() {
            const filtro = this.value.toLowerCase();
            const tabla = document.getElementById('tabla-categorias');
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
</body>
</html>
<?php $conexion->close(); ?>