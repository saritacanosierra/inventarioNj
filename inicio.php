<?php
require 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú Principal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        <?php include './css/header.css'; ?>
        <?php include './css/estilos.css'; ?>
            
    </style>
</head>
<body>

<div class="contenedor-principal">
<?php include './components/header.php'; ?>

    <div class="contenido">
        <?php
            if (isset($_GET['pagina'])) {
                $pagina = $_GET['pagina'];
                switch ($pagina) {
                    case 'usuarios':
                        echo '<h2>Gestión de Usuarios</h2>';
                        break;
                    case 'productos':
                        echo '<h2>Gestión de Productos</h2>';
                        break;
                    case 'categorias':
                        echo '<h2>Gestión de Categorías</h2>';
                        break;
                    default:
                        echo '<h2>Bienvenido al Panel Principal</h2>';
                        break;
                }
            } else {
                echo '<h2>Bienvenido al Panel Principal</h2>';
            }
        ?>
    </div>
</div>

</body>
</html>