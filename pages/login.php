<?php
require_once __DIR__ . '/../conexion.php';
session_start();

// Si ya está logueado, redirigir al index
if (isset($_SESSION['usuario'])) {
    header("Location: /inventarioNj/index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        if ($password === $usuario['contrasena']) {
            $_SESSION['usuario'] = $usuario['usuario'];
            $_SESSION['id'] = $usuario['id'];
            header("Location: /inventarioNj/index.php");
            exit();
        } else {
            $error = 'Contraseña incorrecta';
        }
    } else {
        $error = 'Usuario no encontrado';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Inventario</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-derecha img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
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
            background-color: #d4a7d5;
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
    </style>
</head>
<body>
    <div class="contenedor-login">
        <div class="login-izquierda">
            <h2>Iniciar Sesión</h2>

            <?php if ($error): ?>
                <div class="mensaje error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="usuario">Usuario:</label>
                    <input type="text" id="usuario" name="usuario" required>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit">Iniciar Sesión</button>

                <div class="registro-link">
                    <p>¿No tienes cuenta? <a href="/inventarioNj/pages/registro.php">Regístrate aquí</a></p>
                </div>
            </form>
        </div>

        <div class="login-derecha">
            <img src="/inventarioNj/img/loginimg.png" alt="Logo de la empresa">
        </div>
    </div>
</body>
</html>
<?php $conexion->close(); ?>