<?php
require 'conexion.php';

$sql = "SELECT id, cedula, nombre, apellido, usuario, contraseña, email FROM usuarios";
$resultado = $conexion->query($sql);

if (!$resultado) {
    die('Error al obtener la lista de usuarios: ' . $conexion->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/listarUsuario.css">
    <link rel="stylesheet" href="css/tablas.css">
</head>
<body>
    <div class="contenedor-principal">
        <?php include './components/header.php'; ?>
        
        <div class="contenido">
            <h2>Lista de Usuarios</h2>
            
            <?php if ($resultado->num_rows === 0): ?>
                <p>No hay usuarios registrados.</p>
            <?php else: ?>
                <div class="filtro-agregar-contenedor">
                    <div class="filtro-contenedor">
                        <input type="text" id="filtro-usuario" placeholder="Buscar usuario..." class="filtro-input">
                    </div>
                    <div class="btn-agregar-contenedor">
                        <button onclick="abrirModal()" class="btn-agregar">+</button>
                    </div>
                </div>
                <div class="tabla-contenedor">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Usuario</th>
                                <th>Contraseña</th>
                                <th>Email</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-usuarios">
                            <?php while ($usuario = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $usuario['id']; ?></td>
                                    <td><?php echo $usuario['cedula']; ?></td>
                                    <td><?php echo $usuario['nombre']; ?></td>
                                    <td><?php echo $usuario['apellido']; ?></td>
                                    <td><?php echo $usuario['usuario']; ?></td>
                                    <td><?php echo $usuario['contraseña']; ?></td>
                                    <td><?php echo $usuario['email']; ?></td>
                                    <td class="acciones">
                                        <a href="editar_usuarios.php?id=<?php echo $usuario['id']; ?>" class="btn-editar">Editar</a>
                                        <a href="eliminar_usuarios.php?id=<?php echo $usuario['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal de Insertar Usuario -->
    <div id="modalInsertar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h2>Insertar Nuevo Usuario</h2>
            <div id="mensaje-error" class="mensaje error" style="display: none;"></div>
            <form id="formInsertar" class="form-insertar">
                <div class="form-group">
                    <label for="cedula">Cédula:</label>
                    <input type="text" id="cedula" name="cedula" required>
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" required>
                </div>
                <div class="form-group">
                    <label for="usuario">Usuario:</label>
                    <input type="text" id="usuario" name="usuario" required>
                </div>
                <div class="form-group">
                    <label for="contraseña">Contraseña:</label>
                    <input type="password" id="contraseña" name="contraseña" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-guardar">Guardar</button>
                    <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
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
            gap: 2px; /* Reducir el espacio entre elementos */
        }

        .filtro-contenedor {
            flex: 1;
            margin-right: 10px; /* Reducir el margen derecho */
            max-width: calc(100% - 40px); /* Reducir el espacio para el botón */
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

    

        .tabla-contenedor {
            width: 100%;
            overflow-x: auto;
            padding: 0 15px; /* Añadir padding para alinear con el filtro */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
    </style>

    <script>
        function abrirModal() {
            document.getElementById('modalInsertar').style.display = 'block';
            document.getElementById('mensaje-error').style.display = 'none';
        }

        function cerrarModal() {
            document.getElementById('modalInsertar').style.display = 'none';
            document.getElementById('formInsertar').reset();
            document.getElementById('mensaje-error').style.display = 'none';
        }

        function mostrarError(mensaje) {
            const mensajeError = document.getElementById('mensaje-error');
            mensajeError.textContent = mensaje;
            mensajeError.style.display = 'block';
        }

        // Cerrar el modal si se hace clic fuera de él
        window.onclick = function(event) {
            var modal = document.getElementById('modalInsertar');
            if (event.target == modal) {
                cerrarModal();
            }
        }

        // Manejar el envío del formulario con AJAX
        document.getElementById('formInsertar').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const mensajeError = document.getElementById('mensaje-error');
            mensajeError.style.display = 'none';
            
            fetch('insertar_usuarios.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Agregar el nuevo usuario a la tabla
                    const tbody = document.getElementById('tabla-usuarios');
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                        <td>${data.id}</td>
                        <td>${data.cedula}</td>
                        <td>${data.nombre}</td>
                        <td>${data.apellido}</td>
                        <td>${data.usuario}</td>
                        <td>${data.contraseña}</td>
                        <td>${data.email}</td>
                        <td class="acciones">
                            <a href="editar_usuarios.php?id=${data.id}" class="btn-editar">Editar</a>
                            <a href="eliminar_usuarios.php?id=${data.id}" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</a>
                        </td>
                    `;
                    tbody.insertBefore(newRow, tbody.firstChild);
                    
                    // Cerrar el modal y limpiar el formulario
                    cerrarModal();
                    
                    // Mostrar mensaje de éxito
                    alert('Usuario agregado exitosamente');
                } else {
                    mostrarError(data.message || 'Error al agregar el usuario');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al procesar la solicitud. Por favor, intente nuevamente.');
            });
        });

        // Función para filtrar usuarios
        document.getElementById('filtro-usuario').addEventListener('keyup', function() {
            const filtro = this.value.toLowerCase();
            const tabla = document.getElementById('tabla-usuarios');
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