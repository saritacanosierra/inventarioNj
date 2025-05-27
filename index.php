<?php
require 'conexion.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="contenedor-principal">

            <?php include 'components/header.php'; ?>
          

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
                            echo '<h2 id="bienvenida">Bienvenido al Panel Principal</h2>';
                            // Aquí podrías mostrar un panel principal o información general
                            break;
                    }
                } else {
                    echo '<h2 id="bienvenida">Bienvenido al Panel Principal</h2>';
                }
            ?>
        </div>
    </div>
</body>
</html>