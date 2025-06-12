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
            <?php
            // Obtener el mes y año actual
            $mesActual = date('m');
            $añoActual = date('Y');

            // Consulta para obtener el total de gastos del mes
            $sqlGastos = "SELECT SUM(valorCompra) as totalGastos 
                         FROM financiera 
                         WHERE MONTH(fechaCompra) = ? 
                         AND YEAR(fechaCompra) = ? 
                         AND tipoCompra = 'gasto'";
            
            $stmtGastos = $conexion->prepare($sqlGastos);
            $stmtGastos->bind_param("ss", $mesActual, $añoActual);
            $stmtGastos->execute();
            $resultadoGastos = $stmtGastos->get_result();
            $totalGastos = $resultadoGastos->fetch_assoc()['totalGastos'] ?? 0;

            // Consulta para obtener el total de inversiones del mes
            $sqlInversiones = "SELECT SUM(valorCompra) as totalInversiones 
                              FROM financiera 
                              WHERE MONTH(fechaCompra) = ? 
                              AND YEAR(fechaCompra) = ? 
                              AND tipoCompra = 'inversion'";
            
            $stmtInversiones = $conexion->prepare($sqlInversiones);
            $stmtInversiones->bind_param("ss", $mesActual, $añoActual);
            $stmtInversiones->execute();
            $resultadoInversiones = $stmtInversiones->get_result();
            $totalInversiones = $resultadoInversiones->fetch_assoc()['totalInversiones'] ?? 0;

            // Inicializar el total de ventas en 0
            $totalVentas = 0;
            $totalVentasAnual = 0;

            // Verificar si la tabla ventas existe antes de hacer la consulta
            $checkTable = $conexion->query("SHOW TABLES LIKE 'ventas'");
            if ($checkTable && $checkTable->num_rows > 0) {
                // Consulta para obtener el total de ventas del mes
                $sqlVentas = "SELECT SUM(total) as totalVentas 
                             FROM ventas 
                             WHERE MONTH(fecha_venta) = ? 
                             AND YEAR(fecha_venta) = ?";
                
                $stmtVentas = $conexion->prepare($sqlVentas);
                if ($stmtVentas) {
                    $stmtVentas->bind_param("ss", $mesActual, $añoActual);
                    $stmtVentas->execute();
                    $resultadoVentas = $stmtVentas->get_result();
                    $totalVentas = $resultadoVentas->fetch_assoc()['totalVentas'] ?? 0;
                }

                // Consulta para obtener el total de ventas del año
                $sqlVentasAnual = "SELECT SUM(total) as totalVentasAnual 
                                  FROM ventas 
                                  WHERE YEAR(fecha_venta) = ?";
                
                $stmtVentasAnual = $conexion->prepare($sqlVentasAnual);
                if ($stmtVentasAnual) {
                    $stmtVentasAnual->bind_param("s", $añoActual);
                    $stmtVentasAnual->execute();
                    $resultadoVentasAnual = $stmtVentasAnual->get_result();
                    $totalVentasAnual = $resultadoVentasAnual->fetch_assoc()['totalVentasAnual'] ?? 0;
                }
            }
            ?>

            <div class="dashboard-container">
                <div class="dashboard-card">
                    <div class="card-icon">
                        <span class="material-icons">payments</span>
                    </div>
                    <div class="card-content">
                        <h3>Gastos del Mes</h3>
                        <p class="card-value">$<?php echo number_format($totalGastos, 2, ',', '.'); ?></p>
                    </div>
                </div>
                <div class="dashboard-card">
                    <div class="card-icon">
                        <span class="material-icons">trending_up</span>
                    </div>
                    <div class="card-content">
                        <h3>Inversiones del Mes</h3>
                        <p class="card-value">$<?php echo number_format($totalInversiones, 2, ',', '.'); ?></p>
                    </div>
                </div>
                <div class="dashboard-card">
                    <div class="card-icon">
                        <span class="material-icons">shopping_cart</span>
                    </div>
                    <div class="card-content">
                        <h3>Ventas del Mes</h3>
                        <p class="card-value">$<?php echo number_format($totalVentas, 2, ',', '.'); ?></p>
                    </div>
                </div>
                <div class="dashboard-card">
                    <div class="card-icon">
                        <span class="material-icons">calendar_today</span>
                    </div>
                    <div class="card-content">
                        <h3>Ventas del Año</h3>
                        <p class="card-value">$<?php echo number_format($totalVentasAnual, 2, ',', '.'); ?></p>
                    </div>
                </div>
            </div>

            <style>
                .dashboard-container {
                    display: flex;
                    gap: 20px;
                    margin: 20px 0;
                    flex-wrap: wrap;
                }

                .dashboard-card {
                    background: white;
                    border-radius: 10px;
                    padding: 20px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                    display: flex;
                    align-items: center;
                    min-width: 250px;
                    flex: 1;
                }

                .card-icon {
                    background: #E1B8E2;
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-right: 15px;
                }

                .card-icon .material-icons {
                    color: white;
                    font-size: 24px;
                }

                .card-content h3 {
                    margin: 0;
                    font-size: 16px;
                    color: #666;
                }

                .card-value {
                    margin: 5px 0 0 0;
                    font-size: 24px;
                    font-weight: bold;
                    color: #333;
                }
            </style>

            <h2>Ventas por Mes</h2>
            
            <div class="tabla-ventas-mensual">
                <table>
                    <thead>
                        <tr>
                            <th>Primer Semestre</th>
                            <th>Total Ventas</th>
                            <th>Segundo Semestre</th>
                            <th>Total Ventas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Array con los nombres de los meses
                        $meses = [
                            1 => 'Enero',
                            2 => 'Febrero',
                            3 => 'Marzo',
                            4 => 'Abril',
                            5 => 'Mayo',
                            6 => 'Junio',
                            7 => 'Julio',
                            8 => 'Agosto',
                            9 => 'Septiembre',
                            10 => 'Octubre',
                            11 => 'Noviembre',
                            12 => 'Diciembre'
                        ];

                        // Consulta para obtener las ventas por mes del año actual
                        $sqlVentasMensual = "SELECT MONTH(fecha_venta) as mes, SUM(total) as total_mes 
                                            FROM ventas 
                                            WHERE YEAR(fecha_venta) = ? 
                                            GROUP BY MONTH(fecha_venta) 
                                            ORDER BY mes";
                        
                        $stmtVentasMensual = $conexion->prepare($sqlVentasMensual);
                        if ($stmtVentasMensual) {
                            $stmtVentasMensual->bind_param("s", $añoActual);
                            $stmtVentasMensual->execute();
                            $resultadoVentasMensual = $stmtVentasMensual->get_result();
                            
                            // Crear un array asociativo con los totales por mes
                            $ventasPorMes = array_fill(1, 12, 0); // Inicializar todos los meses con 0
                            while ($row = $resultadoVentasMensual->fetch_assoc()) {
                                $ventasPorMes[$row['mes']] = $row['total_mes'];
                            }

                            // Mostrar los meses en dos columnas
                            for ($i = 1; $i <= 6; $i++) {
                                $totalMes1 = $ventasPorMes[$i] ?? 0;
                                $totalMes2 = $ventasPorMes[$i + 6] ?? 0;
                                echo "<tr>";
                                echo "<td>{$meses[$i]}</td>";
                                echo "<td>$" . number_format($totalMes1, 2, ',', '.') . "</td>";
                                echo "<td>{$meses[$i + 6]}</td>";
                                echo "<td>$" . number_format($totalMes2, 2, ',', '.') . "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <style>
                .tabla-ventas-mensual {
                    margin: 20px 0;
                    background: white;
                    border-radius: 10px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                    overflow: hidden;
                }

                .tabla-ventas-mensual table {
                    width: 100%;
                    border-collapse: collapse;
                }

                .tabla-ventas-mensual th,
                .tabla-ventas-mensual td {
                    padding: 15px;
                    text-align: left;
                    border-bottom: 1px solid #eee;
                }

                .tabla-ventas-mensual th {
                    background-color: #E1B8E2;
                    color: white;
                    font-weight: bold;
                }

                .tabla-ventas-mensual tr:hover {
                    background-color: #f5f5f5;
                }

                .tabla-ventas-mensual td:nth-child(2),
                .tabla-ventas-mensual td:nth-child(4) {
                    text-align: right;
                    font-weight: bold;
                }

                .tabla-ventas-mensual td:nth-child(3) {
                    border-left: 2px solid #E1B8E2;
                }
            </style>

          

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
                    min-width: 150px;
                    max-width: 150px;
                    height: 200px;
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