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