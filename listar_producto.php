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
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/tablas.css">

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

</head>
<body>
    <div class="contenedor-principal">
        <?php include './components/header.php'; ?>
        
      
        
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
                        <button onclick="abrirModal()" class="btn-agregar">+</button>
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
                                    <td class="celda-foto">
                                        <?php if (!empty($producto['foto'])): ?>
                                            <img src="uploads/productos/<?php echo $producto['foto']; ?>" alt="Foto del producto" class="foto-producto">
                                        <?php else: ?>
                                            <div class="foto-placeholder">
                                                <span class="material-icons">image</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="acciones">
                                        <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" class="btn-editar">Editar</a>
                                        <a href="eliminar_producto.php?id=<?php echo $producto['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este producto?')">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para insertar producto -->
    <div id="modalInsertar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h2>Agregar Nuevo Producto</h2>
            <div id="mensaje-error" class="mensaje error" style="display: none;"></div>
            <form id="formProducto" class="form-insertar" enctype="multipart/form-data">
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
                    <label for="foto">Foto del Producto:</label>
                    <input type="file" id="foto" name="foto" accept="image/*" onchange="previewImage(this)">
                    <div id="imagePreview" class="image-preview"></div>
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
        document.getElementById('formProducto').reset();
        document.getElementById('mensaje-error').style.display = 'none';
        document.getElementById('imagePreview').innerHTML = '';
    }

    function cerrarModal() {
        document.getElementById('modalInsertar').style.display = 'none';
    }

    function mostrarError(mensaje) {
        const errorDiv = document.getElementById('mensaje-error');
        errorDiv.textContent = mensaje;
        errorDiv.style.display = 'block';
    }

    function validarFormulario() {
        const codigo = document.getElementById('codigo').value.trim();
        const nombre = document.getElementById('nombre').value.trim();
        const precio = document.getElementById('precio').value.trim();
        const stock = document.getElementById('stock').value.trim();
        const foto = document.getElementById('foto').files[0];

        if (!codigo || !nombre || !precio || !stock) {
            mostrarError('Todos los campos son requeridos');
            return false;
        }

        if (isNaN(precio) || parseFloat(precio) <= 0) {
            mostrarError('El precio debe ser un número válido mayor a 0');
            return false;
        }

        if (isNaN(stock) || parseInt(stock) < 0) {
            mostrarError('El stock debe ser un número válido mayor o igual a 0');
            return false;
        }

        return true;
    }

    document.getElementById('formProducto').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validarFormulario()) {
            return;
        }

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
            console.error('Error:', error);
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

    // Función para previsualizar la imagen
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '150px';
                img.style.maxHeight = '150px';
                img.style.objectFit = 'contain';
                preview.appendChild(img);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Función para filtrar productos
    document.getElementById('filtro-producto').addEventListener('keyup', function() {
        const filtro = this.value.toLowerCase();
        const tabla = document.getElementById('tabla-productos');
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