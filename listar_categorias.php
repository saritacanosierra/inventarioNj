<?php
require 'conexion.php';

$sql = "SELECT id,codigo, nombre, ubicacion FROM categoria";
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
    <title>Lista de Categorias</title>
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
    /* Ajusta el tamaño del logo para que quepa bien dentro del círculo */
    max-width: 130%; /* El logo ocupará el 80% del ancho del círculo (ajusta a tu gusto) */
    max-height: 130%; /* El logo ocupará el 80% de la altura del círculo */
    display: block;  /* Ayuda a eliminar espacios extra */
    object-fit: contain; /* Asegura que la imagen se ajuste dentro del círculo sin distorsionarse */

    /* Si necesitas que el logo sea blanco sobre el fondo negro (y el círculo blanco) */
    /* filter: brightness(0) invert(1); */
    /* Si tu logo ya es blanco o quieres que conserve sus colores originales,
       elimina o comenta la propiedad 'filter'. */
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
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #E1B8E2;
        }
        .acciones a {
            margin-right: 10px;
            text-decoration: none;
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
                <li><a href="insertar_categorias.php">Nueva Categoria</a></li>
                <li><a href="index.php">Volver al Menú</a></li>
                </ul>
        </nav>
        </header>
    <div class="contenedor-principal">
        </div>
    <h1>Lista de Categorías</h1>
    <?php if ($resultado->num_rows === 0): ?>
        <p>No hay categorias registrados.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Ubicación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($categoria = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $categoria['id']; ?></td>
                        <td><?php echo $categoria['codigo']; ?></td>
                        <td><?php echo $categoria['nombre']; ?></td>
                        <td><?php echo $categoria['ubicacion']; ?></td>
                        <td class="acciones">
                            <a href="editar_categorias.php?id=<?php echo $categoria['id']; ?>">Editar</a>
                            <a href="eliminar_categorias.php?id=<?php echo $categoria['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
<?php $conexion->close(); ?>