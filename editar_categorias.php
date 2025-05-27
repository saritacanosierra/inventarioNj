<?php
require 'conexion.php';

$mensaje = '';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conexion->prepare("SELECT id, codigo, nombre, ubicacion FROM categoria WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $categoria = $resultado->fetch_assoc();
    $stmt->close();

    if (!$categoria) {
        die("Categoría no encontrado.");
    }
} else {
    die("ID de la categoría no proporcionado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];
    $ubicacion = $_POST['ubicacion'];

    $stmt = $conexion->prepare("UPDATE categoria SET codigo = ?, nombre = ?, ubicacion = ? WHERE id = ?");
    $stmt->bind_param("sssi", $codigo, $nombre, $ubicacion, $id);

    if ($stmt->execute()) {
        $mensaje = 'Categorái actualizada correctamente.';
        header("Location: listar_categorias.php?mensaje=actualizado");
        exit();
    } else {
        $mensaje = 'Error al actualizar la categoría: ' . $conexion->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Categoría</title>
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
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .enlace-cancelar-estilo {
            display: inline-block;
            padding: 10px 20px;
            background-color: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .contenedor-botones {
    text-align: center;
    padding: 10px 0;
    }

/* Estilos básicos para los botones para que se vean bien */
.contenedor-botones button {
    padding: 15px 50px;
    margin: 10px; /* Para que haya un pequeño espacio entre ellos */
    border: none;
    border-radius: 10px;
    cursor: pointer;
}

.contenedor-botones button[type="submit"] {
    font-size: 16px;
    background-color: #E1B8E2;
    color: black;
}

.contenedor-botones button[type="button"] {
    font-size: 16px;
    background-color: #E1B8E2;
    color: black;
}
.grupo-inputs {
    display: flex; 
    justify-content: space-between; 
    align-items: flex-end;
} 
}

.grupo-inputs > div {
    flex: 1; /* Esto hace que cada div ocupe una parte igual del espacio disponible */
    margin: 0 0; /* Margen horizontal entre los grupos de input */
}

.grupo-inputs label {
    display: block; /* Hace que la etiqueta ocupe su propia línea */
    margin-bottom: 1px;
    font-weight: bold;
}

.grupo-inputs input[type="text"] {
    width: calc(100% - 22px); /* Ancho completo menos padding y borde */
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
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
                <li><a href="listar_categorias.php">Lista de Categorías</li>
                <li><a href="insertar_categorias.php">Nueva Categoría</a></li>
                <li><a href="index.php">Volver al Menú</a></li>
                </ul>
        </nav>
        </header>
    <div class="contenedor-principal">
        </div>
    <h1>Editar Categoría</h1>

    <?php if ($mensaje): ?>
        <div class="mensaje error">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <h1 id=subtitulo>Formulario Categorías</h1>
        
        <div class="grupo-inputs">
    <div>
        <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
    </div>
    <div>
        <label for="codigo">Código:</label>
        <input type="text" name="codigo" id="codigo" value="<?php echo $categoria['codigo']; ?>" required>
    </div>
    <div>
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" value="<?php echo $categoria['nombre']; ?>" required>
    </div>
    <div>
        <label for="ubicacion">Ubicación:</label>
        <input type="text" name="ubicacion" id="ubicacion" value="<?php echo $categoria['ubicacion']; ?>" required>
        </div>
    </div>
        <div class="contenedor-botones">
        <button type="submit"><strong>Actualizar</strong></button>
        <button type="button" onclick="cancelarActualizacion()" class="boton-cancelar-estilo"><strong>Cancelar</strong></button>
        </div>
    </form>
    </div>

<script>
    
    function cancelarActualizacion() {
        window.history.back(); // O window.location.href = 'listar_categorias.php';
    }
</script>
</body>
</html>
<?php $conexion->close(); ?>