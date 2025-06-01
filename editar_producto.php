<?php
require 'conexion.php';

$mensaje = '';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conexion->prepare("SELECT id, codigo, nombre, precio, stock, foto FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $producto = $resultado->fetch_assoc();
    $stmt->close();

    if (!$producto) {
        die("Producto no encontrado.");
    }
} else {
    die("ID del producto no proporcionado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $foto_actual = $_POST['foto_actual'];
    
    // Manejar la subida de la nueva imagen
    $foto = $foto_actual; // Por defecto, mantener la foto actual
    
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $directorio = "uploads/productos/";
        
        // Crear el directorio si no existe
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        
        // Generar nombre único para la nueva imagen
        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nuevo_nombre = uniqid() . '.' . $extension;
        $ruta_completa = $directorio . $nuevo_nombre;
        
        // Mover la nueva imagen
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_completa)) {
            // Eliminar la imagen anterior si existe
            if (!empty($foto_actual) && file_exists($directorio . $foto_actual)) {
                unlink($directorio . $foto_actual);
            }
            $foto = $nuevo_nombre;
        } else {
            $mensaje = 'Error al subir la nueva imagen.';
        }
    }

    $stmt = $conexion->prepare("UPDATE productos SET codigo = ?, nombre = ?, precio = ?, stock = ?, foto = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $codigo, $nombre, $precio, $stock, $foto, $id);

    if ($stmt->execute()) {
        $mensaje = 'Producto actualizado correctamente.';
        header("Location: listar_producto.php?mensaje=actualizado");
        exit();
    } else {
        $mensaje = 'Error al actualizar el producto: ' . $conexion->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
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
            max-width: 130%;
            max-height: 130%; 
            display: block; 
            object-fit: contain; 
        }
        .menu-cabecera {
            padding: 0; 
            display: flex; 
        }

        .menu-cabecera li {
            margin-left: 50px;
        }      

        .menu-cabecera a {
            color: white;  
            font-weight: bold;
            font-size: 16px;
            padding: 5px 0; 
            transition: color 0.3s ease; 
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
        .foto-actual {
            margin-bottom: 15px;
        }
        
        .foto-actual img {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }
        
        .foto-actual p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }

        .image-preview {
            margin-top: 10px;
        }

        .image-preview img {
            max-width: 150px;
            max-height: 150px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
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
                <li><a href="listar_producto.php">Lista de Productos</a></li>
                <li><a href="insertar_producto.php">Agregar nuevo producto</a></li>
                <li><a href="index.php">Volver al Menú</a></li>
                </ul>
        </nav>
        </header>
    <div class="contenedor-principal">
        </div>
    

    <?php if ($mensaje): ?>
        <div class="mensaje error">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <h1 id=subtitulo>Editar Producto</h1>
        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">

        <label for="codigo">Código:</label>
        <input type="text" name="codigo" id="codigo" value="<?php echo $producto['codigo']; ?>" required>

        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" value="<?php echo $producto['nombre']; ?>" required>

        <label for="precio">Precio:</label>
        <input type="text" name="precio" id="precio" value="<?php echo $producto['precio']; ?>" required>

        <label for="stock">Stock:</label>
        <input type="text" name="stock" id="stock" value="<?php echo $producto['stock']; ?>" required>

        <div class="form-group">
            <label for="foto">Foto:</label>
            <?php if (!empty($producto['foto'])): ?>
                <div class="foto-actual">
                    <img src="uploads/productos/<?php echo $producto['foto']; ?>" alt="Foto actual" style="max-width: 150px; margin-bottom: 10px;">
                    <p>Foto actual: <?php echo $producto['foto']; ?></p>
                </div>
            <?php endif; ?>
            <input type="file" name="foto" id="foto" accept="image/*" onchange="previewImage(this)">
            <div id="imagePreview" class="image-preview"></div>
            <input type="hidden" name="foto_actual" value="<?php echo $producto['foto']; ?>">
        </div>

        <script>
            function previewImage(input) {
                const preview = document.getElementById('imagePreview');
                preview.innerHTML = '';
                
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        preview.appendChild(img);
                    }
                    
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>

        <button type="submit"><strong>Guardar Cambios</strong></button>
    </form>
</body>
</html>
<?php $conexion->close(); ?>