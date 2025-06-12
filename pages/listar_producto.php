<?php
require '../conexion.php';

$sql = "SELECT p.id, p.codigo, p.nombre, p.precio, p.stock, p.foto, p.id_categoria, c.nombre as categoria, c.codigo as codigo_categoria, c.ubicacion 
FROM productos p 
LEFT JOIN categorias c ON p.id_categoria = c.id 
ORDER BY p.nombre";
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
    <title>Lista de Productos</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/tablas.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

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

        .image-preview {
            margin-top: 10px;
        }

        .image-preview img {
            max-width: 150px;
            max-height: 150px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }

        .foto-actual {
            margin-bottom: 15px;
        }

        .foto-actual img {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }

        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        select:focus {
            outline: none;
            border-color: #E1B8E2;
            box-shadow: 0 0 0 2px rgba(225, 184, 226, 0.25);
        }

        select option {
            padding: 8px;
        }
    </style>

</head>
<body>
    <div class="contenedor-principal">
        <?php include '../components/header.php'; ?>
        
        <div class="contenido">
            <h2>Lista de Productos</h2>
            
            <?php if ($resultado->num_rows === 0): ?>
                <p>No hay productos registrados.</p>
            <?php else: ?>
                <div class="filtro-agregar-contenedor">
                    <div class="filtro-contenedor">
                        <input type="text" id="filtro-producto" placeholder="Buscar producto..." class="filtro-input">
                    </div>
                    <div class="btn-agregar-contenedor">
                        <button onclick="abrirModalInsertar()" class="btn-agregar">+</button>
                    </div>
                </div>

                <div class="tabla-contenedor">
                    <table id="tabla-productos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Categoría</th>
                                <th>Ubicación</th>
                                <th>Foto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($producto = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $producto['id']; ?></td>
                                    <td><?php echo $producto['codigo']; ?></td>
                                    <td><?php echo $producto['nombre']; ?></td>
                                    <td><?php echo $producto['precio']; ?></td>
                                    <td><?php echo $producto['stock']; ?></td>
                                    <td><?php echo $producto['categoria'] ? $producto['categoria'] . ' (' . $producto['codigo_categoria'] . ')' : 'Sin categoría'; ?></td>
                                    <td><?php echo $producto['ubicacion'] ?? 'N/A'; ?></td>
                                    <td class="celda-foto">
                                        <?php if (!empty($producto['foto'])): ?>
                                            <img src="../uploads/productos/<?php echo $producto['foto']; ?>" alt="Foto del producto" class="foto-producto">
                                        <?php else: ?>
                                            <div class="foto-placeholder">
                                                <span class="material-icons">image</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="acciones">
                                        <button onclick="abrirModalEditar(<?php echo htmlspecialchars(json_encode($producto)); ?>)" class="btn-editar">
                                            <span class="material-icons">edit</span>
                                        </button>
                                        <a href="../controllers/eliminar_producto.php?id=<?php echo $producto['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                            <span class="material-icons">delete</span>
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

    <!-- Modal de Insertar Producto -->
    <div id="modalInsertar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalInsertar()">&times;</span>
            <h2>Insertar Nuevo Producto</h2>
            <div id="mensaje-error-insertar" class="mensaje error" style="display: none;"></div>
            <form id="formInsertar" class="form-insertar" enctype="multipart/form-data">
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
                    <input type="number" id="precio" name="precio" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" required>
                </div>
                <div class="form-group">
                    <label for="id_categoria">Categoría:</label>
                    <select id="id_categoria" name="id_categoria" required>
                        <option value="">Seleccione una categoría</option>
                        <?php
                        $sql_categorias = "SELECT id, codigo, nombre, ubicacion FROM categorias ORDER BY nombre";
                        $resultado_categorias = $conexion->query($sql_categorias);
                        while ($categoria = $resultado_categorias->fetch_assoc()) {
                            echo "<option value='" . $categoria['id'] . "'>" . $categoria['nombre'] . " (" . $categoria['codigo'] . ") - " . $categoria['ubicacion'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="foto">Foto:</label>
                    <input type="file" id="foto" name="foto" accept="image/*">
                    <div id="imagePreview" class="image-preview"></div>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-guardar">Guardar</button>
                    <button type="button" class="btn-cancelar" onclick="cerrarModalInsertar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Editar Producto -->
    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalEditar()">&times;</span>
            <h2>Editar Producto</h2>
            <div id="mensaje-error-editar" class="mensaje error" style="display: none;"></div>
            <form id="formEditar" class="form-editar" enctype="multipart/form-data">
                <input type="hidden" id="edit-id" name="id">
                <input type="hidden" id="edit-foto-actual" name="foto_actual">
                <div class="form-group">
                    <label for="edit-codigo">Código:</label>
                    <input type="text" id="edit-codigo" name="codigo" required>
                </div>
                <div class="form-group">
                    <label for="edit-nombre">Nombre:</label>
                    <input type="text" id="edit-nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="edit-precio">Precio:</label>
                    <input type="number" id="edit-precio" name="precio" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="edit-stock">Stock:</label>
                    <input type="number" id="edit-stock" name="stock" required>
                </div>
                <div class="form-group">
                    <label for="edit-id_categoria">Categoría:</label>
                    <select id="edit-id_categoria" name="id_categoria" required>
                        <option value="">Seleccione una categoría</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit-foto">Foto:</label>
                    <?php if (!empty($producto['foto'])): ?>
                        <div class="foto-actual">
                            <img src="../uploads/productos/<?php echo $producto['foto']; ?>" alt="Foto actual" style="max-width: 100px; margin-bottom: 10px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="edit-foto" name="foto" accept="image/*">
                    <div id="editImagePreview" class="image-preview"></div>
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
            document.getElementById('imagePreview').innerHTML = '';
        }

        // Funciones para el modal de editar
        function abrirModalEditar(producto) {
            document.getElementById('modalEditar').style.display = 'block';
            document.getElementById('mensaje-error-editar').style.display = 'none';
            
            // Cargar las categorías actualizadas
            cargarCategorias();
            
            // Llenar el resto del formulario con los datos del producto
            document.getElementById('edit-id').value = producto.id;
            document.getElementById('edit-codigo').value = producto.codigo;
            document.getElementById('edit-nombre').value = producto.nombre;
            document.getElementById('edit-precio').value = producto.precio;
            document.getElementById('edit-stock').value = producto.stock;
            document.getElementById('edit-foto-actual').value = producto.foto;
            
            // Mostrar la imagen actual si existe
            const editImagePreview = document.getElementById('editImagePreview');
            editImagePreview.innerHTML = '';
            if (producto.foto) {
                const img = document.createElement('img');
                img.src = `../uploads/productos/${producto.foto}`;
                editImagePreview.appendChild(img);
            }

            // Establecer la categoría seleccionada después de cargar las categorías
            setTimeout(() => {
                if (producto.id_categoria) {
                    document.getElementById('edit-id_categoria').value = producto.id_categoria;
                }
            }, 100);
        }

        // Función para cargar las categorías en el select
        function cargarCategorias() {
            fetch('../controllers/obtener_categorias.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.categorias) {
                        const select = document.getElementById('edit-id_categoria');
                        select.innerHTML = '<option value="">Seleccione una categoría</option>';
                        
                        data.categorias.forEach(categoria => {
                            const option = document.createElement('option');
                            option.value = categoria.id;
                            option.textContent = `${categoria.nombre} (${categoria.codigo}) - ${categoria.ubicacion}`;
                            select.appendChild(option);
                        });
                    } else {
                        console.error('Error al cargar categorías:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error al cargar categorías:', error);
                });
        }

        // Cargar categorías al abrir la página
        document.addEventListener('DOMContentLoaded', function() {
            cargarCategorias();
        });

        // Actualizar categorías cada vez que se abre el modal
        document.getElementById('modalEditar').addEventListener('show.bs.modal', function () {
            cargarCategorias();
        });

        function cerrarModalEditar() {
            document.getElementById('modalEditar').style.display = 'none';
            document.getElementById('formEditar').reset();
            document.getElementById('mensaje-error-editar').style.display = 'none';
            document.getElementById('editImagePreview').innerHTML = '';
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

        // Previsualización de imágenes
        document.getElementById('foto').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        document.getElementById('edit-foto').addEventListener('change', function(e) {
            const preview = document.getElementById('editImagePreview');
            preview.innerHTML = '';
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Manejar el envío del formulario de insertar
        document.getElementById('formInsertar').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const mensajeError = document.getElementById('mensaje-error-insertar');
            mensajeError.style.display = 'none';
            
            fetch('../controllers/insertar_producto.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Agregar el nuevo producto a la tabla
                    const tbody = document.getElementById('tabla-productos');
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                        <td>${data.id}</td>
                        <td>${data.codigo}</td>
                        <td>${data.nombre}</td>
                        <td>${data.precio}</td>
                        <td>${data.stock}</td>
                        <td>${data.categoria ? data.categoria + ' (' + data.codigo_categoria + ')' : 'Sin categoría'}</td>
                        <td>${data.ubicacion || 'N/A'}</td>
                        <td>
                            ${data.foto ? `<img src="../uploads/productos/${data.foto}" alt="Foto del producto" style="max-width: 50px; max-height: 50px;">` : ''}
                        </td>
                        <td class="acciones">
                            <button onclick="abrirModalEditar(${JSON.stringify(data)})" class="btn-editar">
                                <span class="material-icons">edit</span>
                            </button>
                            <a href="../controllers/eliminar_producto.php?id=${data.id}" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                <span class="material-icons">delete</span>
                            </a>
                        </td>
                    `;
                    tbody.insertBefore(newRow, tbody.firstChild);
                    
                    cerrarModalInsertar();
                    alert('Producto agregado exitosamente');
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
            
            fetch('../controllers/actualizar_producto.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar la fila en la tabla
                    const rows = document.querySelectorAll('#tabla-productos tbody tr');
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
                            <td>${data.precio}</td>
                            <td>${data.stock}</td>
                            <td>${data.categoria ? data.categoria + ' (' + data.codigo_categoria + ')' : 'Sin categoría'}</td>
                            <td>${data.ubicacion || 'N/A'}</td>
                            <td>
                                ${data.foto ? `<img src="../uploads/productos/${data.foto}" alt="Foto del producto" style="max-width: 50px; max-height: 50px;">` : ''}
                            </td>
                            <td class="acciones">
                                <button onclick="abrirModalEditar(${JSON.stringify(data)})" class="btn-editar">
                                    <span class="material-icons">edit</span>
                                </button>
                                <a href="../controllers/eliminar_producto.php?id=${data.id}" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                    <span class="material-icons">delete</span>
                                </a>
                            </td>
                        `;
                    }
                    
                    cerrarModalEditar();
                    alert('Producto actualizado exitosamente');
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

        // Función para filtrar productos
        document.getElementById('filtro-producto').addEventListener('keyup', function() {
            const filtro = this.value.toLowerCase();
            const tabla = document.getElementById('tabla-productos');
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