<?php
require 'conexion.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal</title>
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
            object-fit: contain; 
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
        nav {
            background-color: #f0f0f0;
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex; /* Para que los elementos del menú estén en línea horizontal */
        }

        nav ul li {
            margin-right: 20px; /* Espacio entre los elementos del menú */
        }

        nav ul li:last-child {
            margin-right: 0; /* Elimina el margen derecho del último elemento */
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        nav a:hover {
            color: #007bff;
        }
        #bienvenida{
            color: 000000;
            text-align: center;
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
                <li><a href="insertar_usuarios.php">Usuarios</a></li>
                <li><a href="insertar_producto.php">Productos</a></li>
                <li><a href="insertar_categorias.php">Categorías</a>
                </ul>
        </nav>
        </header>
    <div class="contenedor-principal">
        </div>

    <div class="contenido">
        <?php
            if (isset($_GET['pagina'])) {
                $pagina = $_GET['pagina'];
                switch ($pagina) {
                    case 'usuarios':
                        echo '<h2>Gestión de Usuarios</h2>';
                        // Aquí incluirías el contenido para la gestión de usuarios
                        break;
                    case 'productos':
                        echo '<h2>Gestión de Productos</h2>';
                        // Aquí incluirías el contenido para la gestión de productos
                        break;
                    case 'categorias':
                        echo '<h2>Gestión de Categorías</h2>';
                        // Aquí incluirías el contenido para la gestión de categorías
                        break;
                    default:
                        echo '<h2>Bienvenido al Panel Principal</h2>';
                        // Aquí podrías mostrar un panel principal o información general
                        break;
                }
            } else {
                echo "<h2 id=\"bienvenida\">Bienvenido al Panel Principal</h2>";
            }
        ?>
    </div>
</body>
</html>