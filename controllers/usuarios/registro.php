<?php
require '../../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];
    $email = $_POST['email'];

    // Verificar si las contraseñas coinciden
    if ($contrasena !== $confirmar_contrasena) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Verificar si el usuario ya existe
        $check_sql = "SELECT * FROM usuarios WHERE usuario = ? OR email = ? OR cedula = ?";
        $check_stmt = $conexion->prepare($check_sql);
        $check_stmt->bind_param("sss", $usuario, $email, $cedula);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['cedula'] === $cedula) {
                $error = "La cédula ya está registrada.";
            } else if ($row['usuario'] === $usuario) {
                $error = "El usuario ya está registrado.";
            } else {
                $error = "El email ya está registrado.";
            }
        } else {
            // Insertar nuevo usuario
            $sql = "INSERT INTO usuarios (cedula, nombre, apellido, usuario, contrasena, email) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssssss", $cedula, $nombre, $apellido, $usuario, $contrasena, $email);

            if ($stmt->execute()) {
                header("Location: login.php?registro=exitoso");
                exit();
            } else {
                $error = "Error al registrar el usuario: " . $conexion->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Inventario</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .contenedor-login {
            display: flex;
            width: 80%;
            max-width: 1000px;
            margin: auto;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            background-color: white;
        }

        .login-izquierda {
            width: 50%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-derecha {
            width: 50%;
            background-color: #f8f9fa;
        }

        .login-derecha img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        h2 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #E1B8E2;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #E1B8E2;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #E1B8E2;
        }

        .mensaje {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .registro-link {
            text-align: center;
            margin-top: 20px;
        }

        .registro-link a {
            color: #E1B8E2;
            text-decoration: none;
        }

        .registro-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .contenedor-login {
                flex-direction: column;
                width: 95%;
            }

            .login-izquierda,
            .login-derecha {
                width: 100%;
            }

            .login-derecha {
                display: none;
            }
        }

        .error-mensaje {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }
        
        .success-mensaje {
            color: #28a745;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="contenedor-login">
        <div class="login-izquierda">
            <h2>Registro de Usuario</h2>
            
            <?php if (isset($error)): ?>
                <div class="mensaje error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="registro.php">
                <div class="form-group">
                    <label for="cedula">Cédula:</label>
                    <input type="text" id="cedula" name="cedula" required onkeyup="verificarCedula(this.value)">
                    <span id="cedula-error" class="error-mensaje"></span>
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
                    <input type="text" id="usuario" name="usuario" required onkeyup="verificarUsuario(this.value)">
                    <span id="usuario-error" class="error-mensaje"></span>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required onkeyup="verificarEmail(this.value)">
                    <span id="email-error" class="error-mensaje"></span>
                </div>
                <div class="form-group">
                    <label for="contrasena">Contraseña:</label>
                    <input type="password" id="contrasena" name="contrasena" required>
                </div>
                <div class="form-group">
                    <label for="confirmar_contrasena">Confirmar Contraseña:</label>
                    <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required>
                </div>
                <button type="submit">Registrarse</button>

                <div class="registro-link">
                    <p>¿Ya tienes cuenta? <a href="/inventarioNj/pages/login.php">Inicia sesión aquí</a></p>
                </div>
            </form>
        </div>

        <div class="login-derecha">
            <img src="/inventarioNj/img/Frame 21.png" alt="Imagen de registro">
        </div>
    </div>

    <script>
        function verificarCedula(cedula) {
            if (cedula.length < 3) return;
            
            fetch('verificar_cedula.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'cedula=' + encodeURIComponent(cedula)
            })
            .then(response => response.json())
            .then(data => {
                const errorElement = document.getElementById('cedula-error');
                if (data.exists) {
                    errorElement.textContent = 'Esta cédula ya está registrada';
                    errorElement.className = 'error-mensaje';
                    document.getElementById('cedula').setCustomValidity('Esta cédula ya está registrada');
                } else {
                    errorElement.textContent = 'Cédula disponible';
                    errorElement.className = 'success-mensaje';
                    document.getElementById('cedula').setCustomValidity('');
                }
            });
        }

        function verificarUsuario(usuario) {
            if (usuario.length < 3) return;
            
            fetch('verificar_usuario.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'usuario=' + encodeURIComponent(usuario)
            })
            .then(response => response.json())
            .then(data => {
                const errorElement = document.getElementById('usuario-error');
                if (data.exists) {
                    errorElement.textContent = 'Este usuario ya está registrado';
                    errorElement.className = 'error-mensaje';
                    document.getElementById('usuario').setCustomValidity('Este usuario ya está registrado');
                } else {
                    errorElement.textContent = 'Usuario disponible';
                    errorElement.className = 'success-mensaje';
                    document.getElementById('usuario').setCustomValidity('');
                }
            });
        }

        function verificarEmail(email) {
            if (email.length < 5) return;
            
            fetch('verificar_email.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'email=' + encodeURIComponent(email)
            })
            .then(response => response.json())
            .then(data => {
                const errorElement = document.getElementById('email-error');
                if (data.exists) {
                    errorElement.textContent = 'Este email ya está registrado';
                    errorElement.className = 'error-mensaje';
                    document.getElementById('email').setCustomValidity('Este email ya está registrado');
                } else {
                    errorElement.textContent = 'Email disponible';
                    errorElement.className = 'success-mensaje';
                    document.getElementById('email').setCustomValidity('');
                }
            });
        }

        // Validar el formulario antes de enviar
        document.querySelector('form').addEventListener('submit', function(e) {
            const usuario = document.getElementById('usuario').value;
            const email = document.getElementById('email').value;
            const cedula = document.getElementById('cedula').value;
            
            // Verificar una última vez antes de enviar
            Promise.all([
                fetch('verificar_usuario.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'usuario=' + encodeURIComponent(usuario)
                }).then(response => response.json()),
                fetch('verificar_email.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'email=' + encodeURIComponent(email)
                }).then(response => response.json()),
                fetch('verificar_cedula.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'cedula=' + encodeURIComponent(cedula)
                }).then(response => response.json())
            ]).then(([usuarioData, emailData, cedulaData]) => {
                if (usuarioData.exists || emailData.exists || cedulaData.exists) {
                    e.preventDefault();
                    alert('Por favor, corrija los errores antes de continuar.');
                }
            });
        });
    </script>
</body>
</html>
<?php $conexion->close(); ?> 