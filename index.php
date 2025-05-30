<?php
require 'conexion.php';

// Definir las páginas disponibles
$paginas = [
    'usuarios' => 'pages/listar_usuarios.php',
    'productos' => 'listar_producto.php',
    'categorias' => 'listar_categorias.php'
];

// Obtener la página solicitada
$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'inicio';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/index.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="contenedor-principal">
        <?php include 'components/header.php'; ?>
        <div class="contenido">
            <?php ?>
            <h2>Catálogo</h2>
            
            <div class="slider-container">
                <div class="slider">
                    <?php
                    $directorio = 'uploads/productos/';
                    $imagenes = glob($directorio . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
                    
                    foreach($imagenes as $imagen) {
                        echo '<div class="slide">';
                        echo '<img src="' . $imagen . '" alt="Producto">';
                        echo '</div>';
                    }
                    ?>
                </div>
                <button class="slider-btn prev">❮</button>
                <button class="slider-btn next">❯</button>
            </div>

           
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const slider = document.querySelector('.slider');
                    const slides = document.querySelectorAll('.slide');
                    const prevBtn = document.querySelector('.prev');
                    const nextBtn = document.querySelector('.next');
                    let currentSlide = 0;

                    function updateSlider() {
                        slider.style.transform = `translateX(-${currentSlide * 100}%)`;
                    }

                    function nextSlide() {
                        currentSlide = (currentSlide + 1) % slides.length;
                        updateSlider();
                    }

                    function prevSlide() {
                        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
                        updateSlider();
                    }

                    // Auto slide cada 5 segundos
                    setInterval(nextSlide, 5000);

                    // Eventos de botones
                    nextBtn.addEventListener('click', nextSlide);
                    prevBtn.addEventListener('click', prevSlide);
                });
            </script>
        </div>
    </div>
</body>
</html>