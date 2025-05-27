<?php
require 'conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];
    $ubicacion = $_POST['ubicacion'];
    // Utilizar sentencias preparadas para mayor seguridad
    $stmt = $conexion->prepare("INSERT INTO categoria (codigo, nombre, ubicacion) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $codigo, $nombre, $ubicacion);

    if ($stmt->execute()) {
        $mensaje = 'Categoria agregada correctamente.';
    } else {
        $mensaje = 'Error al agregar la categoria: ' . $conexion->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Categoría</title>
    <style>
        .cabecera-negra {
            background-color: black;
            width: 100%; 
            height: 100px; 
            padding: 5px 10px;

            display: flex;
            align-items: center;    
            justify-content: space-between;

            box-shadow: 0 2px 5px rgba(0,0,0,0.2); /* Sombra opcional */
        }

        .logo-circulo-blanco {
            width: 70px;
            height: 70px; 
            background-color: white; 
            border-radius: 50%; 
            overflow: hidden;
            position: relative; 
            display: flex;
            justify-content: center; 
            align-items: center;  
        }

        .logo-dentro-circulo {
            max-width: 130%; /* El logo ocupará el 80% del ancho del círculo (ajusta a tu gusto) */
            max-height: 130%; /* El logo ocupará el 80% de la altura del círculo */
            display: block;  /* Ayuda a eliminar espacios extra */
            object-fit: contain; /* Asegura que la imagen se ajuste dentro del círculo sin distorsionarse */

        }
        .menu-cabecera ul {
            list-style: none; /* Elimina los puntos de la lista */
            margin: 0; /* Elimina el margen por defecto de la ul */
            padding: 0; /* Elimina el padding por defecto de la ul */
            display: flex; /* Convierte la lista en un contenedor flex para los items */
        }

        .menu-cabecera li {
            margin-left: 50px; /* Espacio entre cada elemento del menú */
            /* Puedes ajustar este valor para más o menos separación */
        }

        .menu-cabecera a {
            text-decoration: none; /* Elimina el subrayado de los enlaces */
            color: white; /* Color del texto de los enlaces (blanco sobre la franja negra) */
            font-weight: bold; /* Negrita para los enlaces */
            font-size: 16px; /* Tamaño de la letra */
            padding: 5px 0; /* Un pequeño padding para hacer el área clickeable más grande */
            transition: color 0.3s ease; /* Transición suave para el hover */
        }

        .menu-cabecera a:hover {
            color: #E1B8E2;
        }
        #subtitulo {
            color: 0000;
            text-align: center;
            background-color: #E1B8E2;
            border: 1px solid #E1B8E2;
            padding: 10px
        }
   
        form {
            width: 50%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 20px;
            background-color:#EEEEEE;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            box-sizing: border-box;
        }
        button {
            text-align:center;
            button-align: center;
            padding: 10px 30px;
            background-color: #E1B8E2;
            color: black;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .mensaje {
            margin-top: 10px;
            padding: 10px;
            border-radius: 3px;
        }
        .exito {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <header class="cabecera-negra">
        <div class="logo-circulo-blanco">
            <img src="img/logo (40).png" alt="Logo de tu Empresa" class="logo-dentro-circulo">
        </div>
        <nav class="menu-cabecera">
            <ul>
                <li><a href="listar_categorias.php">Lista de Categorías</a></li>
                <li><a href="index.php">Volver al Menú</a></li>
                </ul>
        </nav>
        </header>
    <div class="contenedor-principal">
        </div>
    
    <?php if ($mensaje): ?>
        <div class="mensaje <?php echo (strpos($mensaje, 'correcto') !== false) ? 'exito' : 'error'; ?>">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <h1 id=subtitulo>Agregar Nueva Categoría</h1>
        <label for="codigo">Código:</label>
        <input type="text" name="codigo" id="codigo" required><br><br>

        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" required><br><br>

        <label for="ubicacion">Ubicación:</label>
        <input type="text" name="ubicacion" id="ubicacion" required><br><br>

        <button type="submit"><strong>Guardar Categoría</strong></button>
    </form>
</body>
</html>
<?php $conexion->close(); ?>