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

            <div class="social-media-container">
                <div class="social-media-item">
                    <iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2FropaNuncaJamas&tabs=timeline&width=300&height=400&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId" 
                            width="300" 
                            height="400" 
                            style="border:none;overflow:hidden;border-radius: 15px;box-shadow: 0 4px 8px rgba(0,0,0,0.1);" 
                            scrolling="no" 
                            frameborder="0" 
                            allowfullscreen="true" 
                            allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share">
                    </iframe>
                </div>
                <div class="social-media-item">
                    <iframe src="https://www.instagram.com/ropa_nunca_jamas/embed" 
                            width="300" 
                            height="400" 
                            style="border:none;overflow:hidden;border-radius: 15px;box-shadow: 0 4px 8px rgba(0,0,0,0.1);" 
                            frameborder="0" 
                            scrolling="no" 
                            allowtransparency="true">
                    </iframe>
                </div>
            </div>

            <style>
                .social-media-container {
                    display: flex;
                    justify-content: center;
                    gap: 20px;
                    margin-top: 30px;
                    flex-wrap: wrap;
                }
                .social-media-item {
                    flex: 1;
                    min-width: 300px;
                    max-width: 300px;
                    height: 400px;
                }
                .social-media-item iframe {
                    width: 100%;
                    height: 100%;
                }
            </style>

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