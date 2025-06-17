<?php
require '../conexion.php';

$sql = "SELECT id, cedula, nombre, apellido, usuario, contrasena, email FROM usuarios";
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
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/listarUsuario.css">
    <link rel="stylesheet" href="../css/tablas.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="contenedor-principal">
        <?php include '../components/header.php'; ?>
        
        <div class="contenido">
            <h2>Lista de Usuarios</h2>
            
            <div class="filtro-agregar-contenedor">
                <div class="filtro-contenedor">
                    <input type="text" id="filtro-usuario" placeholder="Buscar usuario..." class="filtro-input">
                </div>
                <div class="btn-agregar-contenedor">
                    <button onclick="abrirModalInsertar()" class="btn-agregar">
                        <span class="material-icons">add</span>
                    </button>
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
                                <td><?php echo $usuario['contrasena']; ?></td>
                                <td><?php echo $usuario['email']; ?></td>
                                <td class="acciones">
                                    <button onclick="abrirModalEditar(<?php echo $usuario['id']; ?>)" class="btn-editar" 
                                            data-id="<?php echo $usuario['id']; ?>"
                                            data-cedula="<?php echo htmlspecialchars($usuario['cedula']); ?>"
                                            data-nombre="<?php echo htmlspecialchars($usuario['nombre']); ?>"
                                            data-apellido="<?php echo htmlspecialchars($usuario['apellido']); ?>"
                                            data-usuario="<?php echo htmlspecialchars($usuario['usuario']); ?>"
                                            data-contrasena="<?php echo htmlspecialchars($usuario['contrasena']); ?>"
                                            data-email="<?php echo htmlspecialchars($usuario['email']); ?>">
                                        <span class="material-icons">edit</span>
                                    </button>
                                    <button onclick="eliminarUsuario(<?php echo $usuario['id']; ?>)" class="btn-eliminar">
                                        <span class="material-icons">delete</span>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de Insertar Usuario -->
    <div id="modalInsertar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalInsertar()">&times;</span>
            <h2>Insertar Nuevo Usuario</h2>
            <div id="mensaje-error-insertar" class="mensaje error" style="display: none;"></div>
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
                    <label for="contrasena">Contraseña:</label>
                    <input type="password" id="contrasena" name="contrasena" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-guardar">Guardar</button>
                    <button type="button" class="btn-cancelar" onclick="cerrarModalInsertar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Editar Usuario -->
    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalEditar()">&times;</span>
            <h2>Editar Usuario</h2>
            <div id="mensaje-error-editar" class="mensaje error" style="display: none;"></div>
            <form id="formEditar" class="form-editar">
                <input type="hidden" id="edit-id" name="id">
                <div class="form-group">
                    <label for="edit-cedula">Cédula:</label>
                    <input type="text" id="edit-cedula" name="cedula" required>
                </div>
                <div class="form-group">
                    <label for="edit-nombre">Nombre:</label>
                    <input type="text" id="edit-nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="edit-apellido">Apellido:</label>
                    <input type="text" id="edit-apellido" name="apellido" required>
                </div>
                <div class="form-group">
                    <label for="edit-usuario">Usuario:</label>
                    <input type="text" id="edit-usuario" name="usuario" required>
                </div>
                <div class="form-group">
                    <label for="edit-contrasena">Contraseña:</label>
                    <input type="password" id="edit-contrasena" name="contrasena" required>
                </div>
                <div class="form-group">
                    <label for="edit-email">Email:</label>
                    <input type="email" id="edit-email" name="email" required>
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

        /* Estilos para el contenedor de la tabla con scroll */
        .tabla-contenedor {
            max-height: 70vh;
            overflow-y: auto;
            margin-top: 20px;
            border: 1px solid #E1B8E2;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .tabla-contenedor table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }

        .tabla-contenedor th {
            position: sticky;
            top: 0;
            background-color: #E1B8E2;
            color: white;
            font-weight: 600;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #d4a7d5;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            z-index: 1;
        }

        .tabla-contenedor td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
            color: #333;
        }

        .tabla-contenedor tr:hover {
            background-color: #f8f5f9;
        }

        /* Estilos para la barra de scroll */
        .tabla-contenedor::-webkit-scrollbar {
            width: 8px;
        }

        .tabla-contenedor::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .tabla-contenedor::-webkit-scrollbar-thumb {
            background: #E1B8E2;
            border-radius: 4px;
        }

        .tabla-contenedor::-webkit-scrollbar-thumb:hover {
            background: #d4a7d5;
        }

        /* Estilos para el contenedor principal */
        .contenido {
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 20px;
        }

        .contenido h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 600;
        }

        /* Estilos para el filtro y botón de agregar */
        .filtro-agregar-contenedor {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filtro-input {
            padding: 8px 12px;
            border: 1px solid #E1B8E2;
            border-radius: 4px;
            width: 300px;
            font-size: 14px;
        }

        .filtro-input:focus {
            outline: none;
            border-color: #d4a7d5;
            box-shadow: 0 0 0 2px rgba(225, 184, 226, 0.2);
        }

        .btn-agregar {
            background-color: #E1B8E2;
            color: white;
            border: none;
            border-radius: 4px;
            width: 40px;
            height: 40px;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-agregar:hover {
            background-color: #d4a7d5;
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
        function abrirModalEditar(id) {
            try {
                const botonEditar = document.querySelector(`button[data-id="${id}"]`);
                if (botonEditar) {
                    document.getElementById('modalEditar').style.display = 'block';
                    document.getElementById('mensaje-error-editar').style.display = 'none';
                    
                    // Llenar el formulario con los datos del usuario
                    document.getElementById('edit-id').value = id;
                    document.getElementById('edit-cedula').value = botonEditar.getAttribute('data-cedula');
                    document.getElementById('edit-nombre').value = botonEditar.getAttribute('data-nombre');
                    document.getElementById('edit-apellido').value = botonEditar.getAttribute('data-apellido');
                    document.getElementById('edit-usuario').value = botonEditar.getAttribute('data-usuario');
                    document.getElementById('edit-contrasena').value = botonEditar.getAttribute('data-contrasena');
                    document.getElementById('edit-email').value = botonEditar.getAttribute('data-email');
                }
            } catch (error) {
                console.error('Error al abrir modal de editar:', error);
                alert('Error al abrir el modal de editar');
            }
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
            
            fetch('../controllers/usuarios/insertar_usuarios.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
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
                        <td>${data.contrasena}</td>
                        <td>${data.email}</td>
                        <td class="acciones">
                            <button onclick="abrirModalEditar(${data.id})" class="btn-editar" 
                                    data-id="${data.id}"
                                    data-cedula="${data.cedula}"
                                    data-nombre="${data.nombre}"
                                    data-apellido="${data.apellido}"
                                    data-usuario="${data.usuario}"
                                    data-contrasena="${data.contrasena}"
                                    data-email="${data.email}">
                                <span class="material-icons">edit</span>
                            </button>
                            <button onclick="eliminarUsuario(${data.id})" class="btn-eliminar">
                                <span class="material-icons">delete</span>
                            </button>
                        </td>
                    `;
                    tbody.insertBefore(newRow, tbody.firstChild);
                    
                    cerrarModalInsertar();
                    alert('Usuario agregado exitosamente');
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
            
            fetch('../controllers/usuarios/actualizar_usuarios.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Buscar la fila correcta y actualizarla
                    const tbody = document.getElementById('tabla-usuarios');
                    const filas = tbody.getElementsByTagName('tr');
                    
                    for (let i = 0; i < filas.length; i++) {
                        const primeraCelda = filas[i].getElementsByTagName('td')[0];
                        if (primeraCelda && primeraCelda.textContent == data.id) {
                            filas[i].innerHTML = `
                                <td>${data.id}</td>
                                <td>${data.cedula}</td>
                                <td>${data.nombre}</td>
                                <td>${data.apellido}</td>
                                <td>${data.usuario}</td>
                                <td>${data.contrasena}</td>
                                <td>${data.email}</td>
                                <td class="acciones">
                                    <button onclick="abrirModalEditar(${data.id})" class="btn-editar" 
                                            data-id="${data.id}"
                                            data-cedula="${data.cedula}"
                                            data-nombre="${data.nombre}"
                                            data-apellido="${data.apellido}"
                                            data-usuario="${data.usuario}"
                                            data-contrasena="${data.contrasena}"
                                            data-email="${data.email}">
                                        <span class="material-icons">edit</span>
                                    </button>
                                    <button onclick="eliminarUsuario(${data.id})" class="btn-eliminar">
                                        <span class="material-icons">delete</span>
                                    </button>
                                </td>
                            `;
                            break;
                        }
                    }
                    
                    cerrarModalEditar();
                    alert('Usuario actualizado exitosamente');
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

        // Función para filtrar usuarios
        document.getElementById('filtro-usuario').addEventListener('keyup', function() {
            const filtro = this.value.toLowerCase();
            const tabla = document.getElementById('tabla-usuarios');
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

        // Función para eliminar usuario
        function eliminarUsuario(id) {
            if (confirm('¿Estás seguro de eliminar este usuario?')) {
                fetch(`../controllers/usuarios/eliminar_usuarios.php?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Eliminar la fila de la tabla
                            const filas = document.querySelectorAll('#tabla-usuarios tr');
                            filas.forEach(fila => {
                                const primeraCelda = fila.querySelector('td:first-child');
                                if (primeraCelda && primeraCelda.textContent == id) {
                                    fila.remove();
                                }
                            });
                            alert('Usuario eliminado exitosamente');
                        } else {
                            alert(data.message || 'Error al eliminar el usuario');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al procesar la solicitud. Por favor, intente nuevamente.');
                    });
            }
        }
    </script>
</body>
</html>
<?php $conexion->close(); ?>