<?php
require '../conexion.php';

$sql = "SELECT v.id, v.fecha_venta, v.total, v.tipo_pago, v.estado, v.created_at,
       c.cedula as cedula_cliente, c.nombre as nombre_cliente,
       COUNT(dv.id) as cantidad_productos,
       GROUP_CONCAT(CONCAT(p.codigo, ' - ', p.nombre, ' (', dv.cantidad, ')') SEPARATOR ', ') as productos
FROM ventas v 
LEFT JOIN clientes c ON v.id_cliente = c.id 
LEFT JOIN detalle_ventas dv ON v.id = dv.venta_id
LEFT JOIN productos p ON dv.producto_id = p.id
WHERE MONTH(v.fecha_venta) = MONTH(CURRENT_DATE()) AND YEAR(v.fecha_venta) = YEAR(CURRENT_DATE())
GROUP BY v.id
ORDER BY v.fecha_venta DESC";
$resultado = $conexion->query($sql);

if (!$resultado) {
    die('Error al obtener la lista de ventas: ' . $conexion->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas - Sistema de Inventario</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/tablas.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: modalFadeIn 0.3s ease-out;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #000;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #E1B8E2;
            box-shadow: 0 0 0 2px rgba(225, 184, 226, 0.25);
        }

        .form-group input[readonly] {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }

        .form-buttons {
            margin-top: 25px;
            text-align: right;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-guardar,
        .btn-cancelar {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-guardar {
            background-color: #E1B8E2;
            color: #000;
        }

        .btn-guardar:hover {
            background-color: #d4a7d5;
            transform: translateY(-1px);
        }

        .btn-cancelar {
            background-color: #f44336;
            color: white;
        }

        .btn-cancelar:hover {
            background-color: #d32f2f;
            transform: translateY(-1px);
        }

        .mensaje {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .acciones {
            display: flex;
            gap: 10px;
        }

        .btn-editar,
        .btn-eliminar {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            transition: transform 0.2s ease;
        }

        .btn-editar:hover,
        .btn-eliminar:hover {
            transform: scale(1.1);
        }

        

        
        /* Estilos para el select de productos */
        select#id_producto,
        select#edit_id_producto {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            font-size: 14px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        select#id_producto:focus,
        select#edit_id_producto:focus {
            outline: none;
            border-color: #E1B8E2;
            box-shadow: 0 0 0 2px rgba(225, 184, 226, 0.25);
        }

        /* Estilos para los campos de fecha y hora */
        input[type="datetime-local"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="datetime-local"]:focus {
            outline: none;
            border-color: #E1B8E2;
            box-shadow: 0 0 0 2px rgba(225, 184, 226, 0.25);
        }

        /* Estilos para los campos numéricos */
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="number"]:focus {
            outline: none;
            border-color: #E1B8E2;
            box-shadow: 0 0 0 2px rgba(225, 184, 226, 0.25);
        }

        /* Estilos para el contenedor del modal */
        .modal-content h2 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #E1B8E2;
        }

        /* Estilos para el botón de ventas mensuales */
        .btn-ventas-mensuales {
            background-color: #E1B8E2;
            color: #000;
            border: none;
            border-radius: 4px;
            padding: 8px 15px;
            margin-right: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .btn-ventas-mensuales:hover {
            background-color: #d4a7d5;
            transform: translateY(-1px);
        }

        .btn-ventas-mensuales .material-icons {
            font-size: 20px;
        }

        /* Estilos para el modal de ventas mensuales */
        .modal-ventas-mensuales {
            max-width: 800px !important;
        }

        .selectores-container {
            margin-bottom: 20px;
            text-align: center;
        }

        .selector-fecha {
            padding: 10px 20px;
            border: 1px solid #E1B8E2;
            border-radius: 4px;
            font-size: 16px;
            color: #333;
            background-color: white;
            cursor: pointer;
            min-width: 200px;
            transition: all 0.3s ease;
        }

        .selector-fecha:hover {
            border-color: #d4a7d5;
        }

        .selector-fecha:focus {
            outline: none;
            border-color: #E1B8E2;
            box-shadow: 0 0 0 2px rgba(225, 184, 226, 0.25);
        }

        .resumen-ventas {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .total-ventas, .cantidad-ventas {
            text-align: center;
        }

        .total-ventas h3, .cantidad-ventas h3 {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        .total-ventas p, .cantidad-ventas p {
            margin: 5px 0 0;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .tabla-ventas-mensuales {
            max-height: 400px;
            overflow-y: auto;
        }

        .tabla-ventas-mensuales table {
            width: 100%;
            border-collapse: collapse;
        }

        .tabla-ventas-mensuales th,
        .tabla-ventas-mensuales td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .tabla-ventas-mensuales th {
            background-color: #f8f9fa;
            position: sticky;
            top: 0;
        }

        .tabla-ventas-mensuales tbody tr:hover {
            background-color: #f5f5f5;
        }

        /* Estilos para productos con bajo stock */
        .stock-bajo {
            color: #ffd700;
            font-weight: bold;
            animation: parpadeo 2s infinite;
            background-color: #fff8dc;
            padding: 2px 5px;
            border-radius: 3px;
        }

        @keyframes parpadeo {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        .stock-bajo::after {
            content: "⚠️";
            margin-left: 5px;
        }

        .btn-factura {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-factura .material-icons {
            color: white;
        }

        .btn-factura:hover .material-icons {
            color: white;
        }

        .btn-guia {
            background-color: #28a745;
            color: white;
            padding: 4px 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }

        .btn-guia:hover {
            background-color: #218838;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 0 auto;
            padding: 10px;
            border: 1px solid #ddd;
            width: 6.5in;
            border-radius: 8px;
            position: relative;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .guia-contenido {
            padding: 5px;
            background: white;
            border-radius: 8px;
            max-width: 6.5in;
            margin: 0 auto;
        }

        .guia-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }

        .guia-logo {
            text-align: center;
        }

        .guia-logo img {
            max-width: 120px;
            height: auto;
        }

        .guia-titulo {
            text-align: left;
        }

        .guia-titulo h1 {
            color: #333;
            font-size: 22px;
            margin-bottom: 8px;
        }

        .guia-titulo p {
            font-size: 14px;
            margin: 0;
        }

        .guia-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .guia-emisor, .guia-receptor {
            width: 48%;
        }

        .guia-emisor h3, .guia-receptor h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .guia-emisor p, .guia-receptor p {
            margin: 4px 0;
            color: #666;
            font-size: 14px;
        }

        .guia-delicado {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #ffd6e7;
            border: 2px dashed #ff69b4;
            border-radius: 4px;
        }

        .guia-delicado h2 {
            color: #ff1493;
            font-size: 36px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .guia-agradecimiento {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .guia-agradecimiento p {
            font-size: 14px;
            color: #333;
            margin: 0;
            font-style: italic;
        }

        .guia-footer {
            margin-top: 20px;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        .btn-imprimir {
            background-color: #17a2b8;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-imprimir:hover {
            background-color: #138496;
        }

        .material-icons {
            font-size: 18px;
        }

        @media print {
            .modal-content {
                width: 6.5in;
                margin: 0;
                padding: 0.1in;
                box-shadow: none;
                border: none;
            }

            .guia-contenido {
                padding: 0;
            }

            .guia-delicado {
                background-color: #ffd6e7 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .guia-agradecimiento {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .btn-imprimir {
                display: none;
            }
        }

        .sin-cliente {
            color: #dc3545;
            font-style: italic;
            font-size: 0.9em;
        }

        /* Estilos para el header de la tabla */
        .tabla-contenedor {
            max-height: 70vh;
            overflow-y: auto;
            margin-top: 20px;
            border: 1px solid #E1B8E2;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .tabla-contenedor table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }

        .tabla-contenedor th {
            position: sticky;
            top: 0;
            background-color: #E1B8E2;
            color: white;
            font-weight: 600;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #d4a7d5;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            z-index: 1;
        }

        .tabla-contenedor td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
            color: #333;
        }

        .tabla-contenedor tr:hover {
            background-color: #f8f5f9;
        }

        .tabla-contenedor::-webkit-scrollbar {
            width: 8px;
        }

        .tabla-contenedor::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .tabla-contenedor::-webkit-scrollbar-thumb {
            background: #E1B8E2;
            border-radius: 4px;
        }

        .tabla-contenedor::-webkit-scrollbar-thumb:hover {
            background: #d4a7d5;
        }

        /* Estilos para los botones de acción */
        .acciones {
            display: flex;
            gap: 5px;
            justify-content: flex-start;
            align-items: center;
        }

        .btn-editar, .btn-guia, .btn-eliminar, .btn-factura {
            padding: 6px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-editar {
            background-color: #17a2b8;
           
        }

        .btn-guia {
            background-color: #28a745;
            color: white;
        }

        .btn-eliminar {
            background-color: #dc3545;
            color: white;
        }

        .btn-factura {
            background-color:rgb(224, 147, 190);
            color: white;
        }

        .btn-editar:hover {
            background-color: #138496;
        }

        .btn-guia:hover {
            background-color: #218838;
        }

        .btn-eliminar:hover {
            background-color: #c82333;
        }

        .btn-factura:hover {
            background-color: #5a6268;
            color: white;
        }

        .material-icons {
            font-size: 18px;
        }

        /* Estilos para el contenedor principal */
        .contenido {
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 20px;
        }

        .contenido h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 600;
        }

        /* Estilos para el filtro y botón de agregar */
        .filtro-agregar-contenedor {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filtro-input {
            padding: 8px 12px;
            border: 1px solid #E1B8E2;
            border-radius: 4px;
            width: 300px;
            font-size: 14px;
        }

        .filtro-input:focus {
            outline: none;
            border-color: #d4a7d5;
            box-shadow: 0 0 0 2px rgba(225, 184, 226, 0.2);
        }

        .btn-agregar {
            background-color: #E1B8E2;
            color: white;
            border: none;
            border-radius: 4px;
            width: 40px;
            height: 40px;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-agregar:hover {
            background-color: #d4a7d5;
        }

        /* Estilos para las pestañas */
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-btn {
            padding: 10px 20px;
            border: 1px solid #E1B8E2;
            background-color: white;
            color: #333;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .tab-btn:hover {
            background-color: #E1B8E2;
            color: #000;
        }

        .tab-btn.active {
            background-color: #E1B8E2;
            color: #000;
            font-weight: bold;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Estilos para los botones de año */
        .años-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn-año {
            padding: 10px 20px;
            border: 1px solid #E1B8E2;
            background-color: white;
            color: #333;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-año:hover {
            background-color: #E1B8E2;
            color: #000;
        }

        .btn-año.active {
            background-color: #E1B8E2;
            color: #000;
            font-weight: bold;
        }

        /* Estilos para el selector de año */
        .selector-anio-container {
            margin-bottom: 20px;
            text-align: center;
        }

        .selector-fecha {
            padding: 10px 20px;
            border: 1px solid #E1B8E2;
            border-radius: 4px;
            font-size: 16px;
            color: #333;
            background-color: white;
            cursor: pointer;
            min-width: 200px;
            transition: all 0.3s ease;
        }

        .selector-fecha:hover {
            border-color: #d4a7d5;
        }

        .selector-fecha:focus {
            outline: none;
            border-color: #E1B8E2;
            box-shadow: 0 0 0 2px rgba(225, 184, 226, 0.25);
        }

        /* Estilos para los botones de mes en la vista anual */
        .meses-container-anual {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn-mes-anual {
            padding: 10px;
            border: 1px solid #E1B8E2;
            background-color: white;
            color: #333;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-mes-anual:hover {
            background-color: #E1B8E2;
            color: #000;
        }

        .btn-mes-anual.active {
            background-color: #E1B8E2;
            color: #000;
            font-weight: bold;
        }

        .meses-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
            padding: 0 20px;
        }

        .btn-mes {
            padding: 12px 15px;
            border: 1px solid #E1B8E2;
            border-radius: 4px;
            background-color: white;
            color: #333;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            text-align: center;
            min-width: 100px;
        }

        .btn-mes:hover {
            background-color: #f8f0f8;
            border-color: #d4a7d5;
        }

        .btn-mes.active {
            background-color: #E1B8E2;
            color: white;
            border-color: #E1B8E2;
        }

        .btn-mes.active:hover {
            background-color: #d4a7d5;
        }
    </style>
</head>
<body>
    <div class="contenedor-principal">
        <?php include '../components/header.php'; ?>
        
        <div class="contenido">
            <h2>Registro de Ventas</h2>
            
            <div class="filtro-agregar-contenedor">
                <div class="filtro-contenedor">
                    <input type="text" id="filtro-venta" placeholder="Buscar venta..." class="filtro-input">
                </div>
                <div class="btn-agregar-contenedor">
                    <a href="registrar_venta.php" class="btn-ventas-mensuales" style="text-decoration: none; margin-right: 10px;">
                        <span class="material-icons">add_shopping_cart</span>
                        Nueva Venta Completa
                    </a>
                    <button onclick="abrirModalVentasMensuales()" class="btn-ventas-mensuales">
                        <span class="material-icons">calendar_month</span>
                        Ventas Mensuales
                    </button>
                   
                </div>
            </div>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Cédula Cliente</th>
                            <th>Cantidad de Productos</th>
                            <th>Productos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-ventas">
                        <?php
                        if ($resultado->num_rows > 0) {
                            while($venta = $resultado->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $venta['id'] . "</td>";
                                echo "<td>" . $venta['fecha_venta'] . "</td>";
                                echo "<td>" . ($venta['cedula_cliente'] ? htmlspecialchars($venta['cedula_cliente']) : '<span class="sin-cliente">Sin cliente</span>') . "</td>";
                                echo "<td>" . $venta['cantidad_productos'] . " productos</td>";
                                echo "<td>" . htmlspecialchars($venta['productos'] ?: 'Sin productos') . "</td>";
                                echo "<td class='acciones'>";
                              
                            
                                
                    
                                echo "</a>";
                                echo "<a href='../controllers/ventas/eliminar_venta.php?id=" . $venta['id'] . "' class='btn-eliminar' onclick='return confirm(\"¿Estás seguro de eliminar esta venta?\")'>";
                                echo "<span class='material-icons'>delete</span>";
                                echo "</a>";
                                echo "<a href='../controllers/ventas/generar_factura.php?id=" . $venta['id'] . "' class='btn-factura' target='_blank'>";
                                echo "<span class='material-icons'>receipt</span>";
                                echo "</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='10' class='text-center'>No hay ventas registradas</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de Insertar Venta -->
    <div id="modalInsertar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalInsertar()">&times;</span>
            <h2>Registrar Nueva Venta</h2>
            <div id="mensaje-error-insertar" class="mensaje error" style="display: none;"></div>
            <form id="formInsertar" class="form-insertar">
                <input type="hidden" id="id_cliente" name="id_cliente" value="">
                <div class="form-group">
                    <label for="fecha_venta">Fecha de Venta:</label>
                    <input type="datetime-local" id="fecha_venta" name="fecha_venta" required>
                </div>
                <div class="form-group">
                    <label for="id_categoria">Categoría:</label>
                    <select id="id_categoria" name="id_categoria" required onchange="filtrarProductos()">
                        <option value="">Seleccione una categoría</option>
                        <?php
                        $sql_categorias = "SELECT id, codigo, nombre FROM categoria ORDER BY nombre";
                        $categorias = $conexion->query($sql_categorias);
                        while ($categoria = $categorias->fetch_assoc()) {
                            echo "<option value='{$categoria['id']}'>{$categoria['nombre']} ({$categoria['codigo']})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_producto">Producto:</label>
                    <select id="id_producto" name="id_producto" required onchange="actualizarPrecio()">
                        <option value="">Seleccione un producto</option>
                        <?php
                        $sql_productos = "SELECT p.id, p.codigo, p.nombre, p.precio, p.stock, p.id_categoria 
                                        FROM productos p 
                                        WHERE p.stock > 0 
                                        ORDER BY p.nombre";
                        $productos = $conexion->query($sql_productos);
                        while ($producto = $productos->fetch_assoc()) {
                            echo "<option value='{$producto['id']}' 
                                    data-precio='{$producto['precio']}' 
                                    data-stock='{$producto['stock']}'
                                    data-categoria='{$producto['id_categoria']}'
                                    style='display: none;'>
                                    {$producto['codigo']} - {$producto['nombre']} (Stock: {$producto['stock']})
                                </option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" id="cantidad" name="cantidad" min="1" required onchange="calcularTotal()">
                </div>
                <div class="form-group">
                    <label for="precio_unitario">Precio Unitario:</label>
                    <input type="number" id="precio_unitario" name="precio_unitario" step="0.01" readonly>
                </div>
                <div class="form-group">
                    <label for="total">Total:</label>
                    <input type="number" id="total" name="total" step="0.01" readonly>
                </div>
                <div class="form-group">
                    <label for="tipo_pago">Tipo de Pago:</label>
                    <select id="tipo_pago" name="tipo_pago" required>
                        <option value="">Seleccione el tipo de pago</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-guardar">Guardar</button>
                    <button type="button" class="btn-cancelar" onclick="cerrarModalInsertar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Editar Venta -->
    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalEditar()">&times;</span>
            <h2>Editar Venta</h2>
            <div id="mensaje-error-editar" class="mensaje error" style="display: none;"></div>
            <form id="formEditar" class="form-editar">
                <input type="hidden" id="edit_id" name="id">
                <input type="hidden" id="edit_id_cliente" name="id_cliente" value="">
                <div class="form-group">
                    <label for="edit_fecha_venta">Fecha de Venta:</label>
                    <input type="datetime-local" id="edit_fecha_venta" name="fecha_venta" required>
                </div>
                <div class="form-group">
                    <label for="edit_id_producto">Producto:</label>
                    <select id="edit_id_producto" name="id_producto" required onchange="actualizarPrecioEditar()">
                        <?php
                        $productos->data_seek(0);
                        while ($producto = $productos->fetch_assoc()) {
                            echo "<option value='{$producto['id']}' data-precio='{$producto['precio']}' data-stock='{$producto['stock']}'>{$producto['codigo']} - {$producto['nombre']} (Stock: {$producto['stock']})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_cantidad">Cantidad:</label>
                    <input type="number" id="edit_cantidad" name="cantidad" min="1" required onchange="calcularTotalEditar()">
                </div>
                <div class="form-group">
                    <label for="edit_precio_unitario">Precio Unitario:</label>
                    <input type="number" id="edit_precio_unitario" name="precio_unitario" step="0.01" readonly>
                </div>
                <div class="form-group">
                    <label for="edit_total">Total:</label>
                    <input type="number" id="edit_total" name="total" step="0.01" readonly>
                </div>
                <div class="form-group">
                    <label for="edit_tipo_pago">Tipo de Pago:</label>
                    <select id="edit_tipo_pago" name="tipo_pago" required>
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-guardar">Guardar Cambios</button>
                    <button type="button" class="btn-cancelar" onclick="cerrarModalEditar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Ventas Anuales -->
    <div id="modalVentasMensuales" class="modal">
        <div class="modal-content modal-ventas-mensuales">
            <span class="close" onclick="cerrarModalVentasMensuales()">&times;</span>
            <h2>Ventas Anuales</h2>
            
            <div class="selector-anio-container">
                <select id="select-anio" class="selector-fecha" onchange="cargarVentasMes(document.querySelector('.btn-mes.active').getAttribute('data-mes'))">
                    <?php
                    $añoActual = date('Y');
                    for ($i = $añoActual; $i >= $añoActual - 4; $i--) {
                        $selected = $i == $añoActual ? 'selected' : '';
                        echo "<option value='$i' $selected>$i</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="meses-container">
                <?php
                $meses = [
                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                ];
                foreach ($meses as $num => $nombre) {
                    $clase = $num == date('n') ? 'active' : '';
                    echo "<button class='btn-mes $clase' data-mes='$num' onclick='cargarVentasMes($num)'>$nombre</button>";
                }
                ?>
            </div>

            <div class="resumen-ventas">
                <div class="total-ventas">
                    <h3>Total Ventas del Mes</h3>
                    <p id="total-ventas-mes">$0.00</p>
                </div>
                <div class="cantidad-ventas">
                    <h3>Cantidad de Ventas</h3>
                    <p id="cantidad-ventas-mes">0</p>
                </div>
            </div>

            <div class="tabla-ventas-mensuales">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-ventas-mensuales">
                        <!-- Las ventas se cargarán aquí dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para guía de envíos -->
    <div id="modalGuia" class="modal">
        <div class="modal-content" style="width: 6.5in;">
            <span class="close" onclick="cerrarModalGuia()">&times;</span>
            <div class="guia-contenido">
                <div class="guia-header">
                    <div class="guia-logo">
                        <img src="/inventarioNj/img/logo (40).png" alt="Ropa Nunca Jamás">
                    </div>
                    <div class="guia-titulo">
                        <h1>GUÍA DE ENVÍO</h1>
                        <p>Fecha: <span id="fecha-guia"></span></p>
                    </div>
                </div>
                
                <div class="guia-info">
                    <div class="guia-emisor">
                        <h3>DATOS DEL REMITENTE</h3>
                        <p><strong>Empresa:</strong> Ropa Nunca Jamás</p>
                        <p><strong>Dirección:</strong> Cl. 48 #49-41, La Candelaria, Medellín, La Candelaria, Medellín, Antioquia</p>
                        <p><strong>Teléfono:</strong> 301 691 75 71</p>
                    </div>
                    
                    <div class="guia-receptor">
                        <h3>DATOS DEL DESTINATARIO</h3>
                        <p><strong>Nombre:</strong> <span id="guia-nombre"></span></p>
                        <p><strong>Dirección:</strong> <span id="guia-direccion"></span></p>
                        <p><strong>Teléfono:</strong> <span id="guia-celular"></span></p>
                        <p><strong>Cédula:</strong> <span id="guia-cedula"></span></p>
                    </div>
                </div>

                <div class="guia-delicado">
                    <h2>DELICADO</h2>
                </div>

                <div class="guia-agradecimiento">
                    <p>¡Gracias por tu compra! Tu satisfacción es nuestra prioridad.</p>
                </div>

                <div class="guia-footer">
                    <button onclick="imprimirGuia()" class="btn-imprimir">
                        <span class="material-icons">print</span> Imprimir Guía
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para insertar cliente -->
    <div id="modalInsertarCliente" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalInsertarCliente()">&times;</span>
            <h2>Insertar Nuevo Cliente</h2>
            <div id="mensaje-error-insertar-cliente" class="mensaje error" style="display: none;"></div>
            <form id="formInsertarCliente" class="form-insertar">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="cedula">Cédula:</label>
                    <input type="text" id="cedula" name="cedula" required>
                </div>
                <div class="form-group">
                    <label for="celular">Celular:</label>
                    <input type="text" id="celular" name="celular" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" required>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-guardar">Guardar</button>
                    <button type="button" class="btn-cancelar" onclick="cerrarModalInsertarCliente()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Función para abrir el modal de insertar
        function abrirModalInsertar() {
            document.getElementById('modalInsertar').style.display = 'block';
            document.getElementById('mensaje-error-insertar').style.display = 'none';
            
            // Limpiar campos al abrir el modal
            document.getElementById('precio_unitario').value = '';
            document.getElementById('total').value = '';
            document.getElementById('cantidad').value = '';
            document.getElementById('id_producto').value = '';
            document.getElementById('id_categoria').value = '';
            
            console.log('Modal de inserción abierto - campos inicializados');
        }

        function cerrarModalInsertar() {
            document.getElementById('modalInsertar').style.display = 'none';
            document.getElementById('formInsertar').reset();
            document.getElementById('mensaje-error-insertar').style.display = 'none';
            document.getElementById('precio_unitario').value = '';
            document.getElementById('total').value = '';
            document.getElementById('cantidad').value = '';
            console.log('Modal de inserción cerrado - campos limpiados');
        }

        // Funciones para el modal de editar
        function abrirModalEditar(venta) {
            document.getElementById('modalEditar').style.display = 'block';
            document.getElementById('edit_id').value = venta.id;
            document.getElementById('edit_id_cliente').value = venta.id_cliente;
            // Convertir la fecha al formato correcto para el input datetime-local
            const fecha = new Date(venta.fecha_venta);
            const fechaFormateada = fecha.toISOString().slice(0, 16);
            document.getElementById('edit_fecha_venta').value = fechaFormateada;
            document.getElementById('edit_id_producto').value = venta.id_producto;
            document.getElementById('edit_cantidad').value = venta.cantidad;
            document.getElementById('edit_precio_unitario').value = venta.precio_unitario;
            document.getElementById('edit_total').value = venta.total;
            document.getElementById('edit_tipo_pago').value = venta.tipo_pago || 'efectivo';
        }

        function cerrarModalEditar() {
            document.getElementById('modalEditar').style.display = 'none';
            document.getElementById('formEditar').reset();
        }

        // Funciones para cálculos
        function calcularTotal() {
            const cantidad = document.getElementById('cantidad').value;
            const precio = document.getElementById('precio_unitario').value;
            
            console.log('calcularTotal - Cantidad:', cantidad);
            console.log('calcularTotal - Precio unitario:', precio);
            
            if (cantidad && precio) {
                const total = (parseFloat(cantidad) * parseFloat(precio)).toFixed(2);
                document.getElementById('total').value = total;
                console.log('calcularTotal - Total calculado:', total);
            } else {
                document.getElementById('total').value = '';
            }
        }

        function calcularTotalEditar() {
            const cantidad = document.getElementById('edit_cantidad').value;
            const precio = document.getElementById('edit_precio_unitario').value;
            
            console.log('calcularTotalEditar - Cantidad:', cantidad);
            console.log('calcularTotalEditar - Precio unitario:', precio);
            
            if (cantidad && precio) {
                const total = (parseFloat(cantidad) * parseFloat(precio)).toFixed(2);
                document.getElementById('edit_total').value = total;
                console.log('calcularTotalEditar - Total calculado:', total);
            } else {
                document.getElementById('edit_total').value = '';
            }
        }

        // Función para filtrar productos por categoría
        function filtrarProductos() {
            const categoriaId = document.getElementById('id_categoria').value;
            const selectProducto = document.getElementById('id_producto');
            const options = selectProducto.getElementsByTagName('option');
            
            // Resetear el select de productos
            selectProducto.value = '';
            document.getElementById('precio_unitario').value = '';
            document.getElementById('cantidad').value = '';
            document.getElementById('total').value = '';
            
            // Mostrar/ocultar productos según la categoría seleccionada
            for (let option of options) {
                if (option.value === '') {
                    option.style.display = ''; // Siempre mostrar la opción por defecto
                } else {
                    const categoriaProducto = option.getAttribute('data-categoria');
                    option.style.display = categoriaId === '' || categoriaId === categoriaProducto ? '' : 'none';
                }
            }
        }

        // Modificar la función actualizarPrecio para incluir la validación de categoría
        function actualizarPrecio() {
            const select = document.getElementById('id_producto');
            const option = select.options[select.selectedIndex];
            const precio = option.getAttribute('data-precio');
            const stock = option.getAttribute('data-stock');
            
            console.log('actualizarPrecio - Producto seleccionado:', select.value);
            console.log('actualizarPrecio - Precio obtenido:', precio);
            console.log('actualizarPrecio - Stock obtenido:', stock);
            
            if (select.value !== '') {
                document.getElementById('precio_unitario').value = precio;
                document.getElementById('cantidad').max = stock;
                document.getElementById('cantidad').value = '';
                document.getElementById('total').value = '';
                console.log('actualizarPrecio - Precio unitario establecido:', precio);
            } else {
                document.getElementById('precio_unitario').value = '';
                document.getElementById('total').value = '';
            }
        }

        // Función para actualizar precio en el modal de editar
        function actualizarPrecioEditar() {
            const select = document.getElementById('edit_id_producto');
            const option = select.options[select.selectedIndex];
            const precio = option.getAttribute('data-precio');
            const stock = option.getAttribute('data-stock');
            
            console.log('actualizarPrecioEditar - Producto seleccionado:', select.value);
            console.log('actualizarPrecioEditar - Precio obtenido:', precio);
            console.log('actualizarPrecioEditar - Stock obtenido:', stock);
            
            if (select.value !== '') {
                document.getElementById('edit_precio_unitario').value = precio;
                document.getElementById('edit_cantidad').max = stock;
                calcularTotalEditar();
                console.log('actualizarPrecioEditar - Precio unitario establecido:', precio);
            } else {
                document.getElementById('edit_precio_unitario').value = '';
                document.getElementById('edit_total').value = '';
            }
        }

        // Función para actualizar la tabla después de una venta
        function actualizarTablaVentas(nuevaVenta) {
            const tbody = document.getElementById('tabla-ventas');
            const tr = document.createElement('tr');
            
            // Crear el contenido de la celda del producto con el indicador de stock bajo si es necesario
            let productoCell = `${nuevaVenta.nombre_producto}`;
            if (nuevaVenta.stock_actual == 1) {
                productoCell += ` <span class="stock-bajo">(Última unidad disponible)</span>`;
            }

            tr.innerHTML = `
                <td>${nuevaVenta.id}</td>
                <td>${new Date(nuevaVenta.fecha_venta).toLocaleString('es-ES', {
                    day: 'numeric',
                    month: 'numeric',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                })}</td>
                <td>${nuevaVenta.cedula_cliente ? htmlspecialchars($nuevaVenta.cedula_cliente) : '<span class="sin-cliente">Sin cliente</span>'}</td>
                <td>${nuevaVenta.cantidad_productos} productos</td>
                <td>${productoCell}</td>
                <td>-</td>
                <td>-</td>
                <td>$${parseFloat(nuevaVenta.total).toFixed(2)}</td>
                <td>${nuevaVenta.tipo_pago ? nuevaVenta.tipo_pago.charAt(0).toUpperCase() + nuevaVenta.tipo_pago.slice(1) : 'No especificado'}</td>
                <td class="acciones">
                    <button onclick="abrirModalEditar(${JSON.stringify(nuevaVenta)})" class="btn-editar">
                        <span class="material-icons">edit</span>
                    </button>
                    <a href="../controllers/ventas/eliminar_venta.php?id=${nuevaVenta.id}" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar esta venta?')">
                        <span class="material-icons">delete</span>
                    </a>
                    <a href="../controllers/ventas/generar_factura.php?id=${nuevaVenta.id}" class="btn-factura" target="_blank">
                        <span class="material-icons">receipt</span>
                    </a>
                </td>
            `;
            
            tbody.insertBefore(tr, tbody.firstChild);
        }

        // Modificar el manejador del formulario de insertar para usar la nueva función
        document.getElementById('formInsertar').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const mensajeError = document.getElementById('mensaje-error-insertar');
            mensajeError.style.display = 'none';
            
            // Log para debugging
            console.log('Enviando formulario de venta...');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            fetch('../controllers/ventas/insertar_venta.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Respuesta del servidor:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos:', data);
                if (data.success) {
                    actualizarTablaVentas(data.venta);
                    cerrarModalInsertar();
                    alert('Venta registrada exitosamente');
                    location.reload(); // Recargar para actualizar el stock en los selectores
                } else {
                    mensajeError.textContent = data.message;
                    mensajeError.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mensajeError.textContent = 'Error al procesar la solicitud. Por favor, intente nuevamente.';
                mensajeError.style.display = 'block';
            });
        });

        // Manejar el envío del formulario de editar
        document.getElementById('formEditar').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            // Asegurarse de que la fecha esté en el formato correcto
            const fechaVenta = formData.get('fecha_venta');
            if (!fechaVenta) {
                alert('Por favor seleccione una fecha');
                return;
            }
            formData.set('fecha_venta', fechaVenta.replace('T', ' '));
            
            const mensajeError = document.getElementById('mensaje-error-editar');
            mensajeError.style.display = 'none';
            
            fetch('../controllers/ventas/actualizar_venta.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar la fila en la tabla
                    const rows = document.querySelectorAll('#tabla-ventas tr');
                    let targetRow = null;
                    
                    for (let row of rows) {
                        if (row.cells[0].textContent === data.id.toString()) {
                            targetRow = row;
                            break;
                        }
                    }
                    
                    if (targetRow) {
                        targetRow.innerHTML = `
                            <td>${data.id}</td>
                            <td>${new Date(data.fecha_venta).toLocaleString('es-ES', {
                                day: 'numeric',
                                month: 'numeric',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false
                            })}</td>
                            <td>${data.cedula_cliente ? htmlspecialchars($data.cedula_cliente) : '<span class="sin-cliente">Sin cliente</span>'}</td>
                            <td>${data.cantidad_productos} productos</td>
                            <td>${data.productos ? htmlspecialchars($data.productos) : 'Sin productos'}</td>
                            <td>-</td>
                            <td>-</td>
                            <td>$${parseFloat(data.total).toFixed(2)}</td>
                            <td>${data.tipo_pago ? data.tipo_pago.charAt(0).toUpperCase() + data.tipo_pago.slice(1) : 'No especificado'}</td>
                            <td class="acciones">
                                <button onclick="abrirModalEditar(${JSON.stringify(data)})" class="btn-editar">
                                    <span class="material-icons">edit</span>
                                </button>
                                <a href="../controllers/ventas/eliminar_venta.php?id=${data.id}" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar esta venta?')">
                                    <span class="material-icons">delete</span>
                                </a>
                                <a href="../controllers/ventas/generar_factura.php?id=${data.id}" class="btn-factura" target="_blank">
                                    <span class="material-icons">receipt</span>
                                </a>
                            </td>
                        `;
                    }
                    
                    cerrarModalEditar();
                    alert('Venta actualizada exitosamente');
                    location.reload(); // Recargar para actualizar el stock en los selectores
                } else {
                    mensajeError.textContent = data.message;
                    mensajeError.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mensajeError.textContent = 'Error al procesar la solicitud. Por favor, intente nuevamente.';
                mensajeError.style.display = 'block';
            });
        });

        // Filtro de búsqueda
        document.getElementById('filtro-venta').addEventListener('keyup', function() {
            const filtro = this.value.toLowerCase();
            const tabla = document.getElementById('tabla-ventas');
            const filas = tabla.getElementsByTagName('tr');

            for (let i = 0; i < filas.length; i++) {
                const celdas = filas[i].getElementsByTagName('td');
                let mostrar = false;

                for (let j = 0; j < celdas.length; j++) {
                    if (celdas[j].textContent.toLowerCase().indexOf(filtro) > -1) {
                        mostrar = true;
                        break;
                    }
                }

                filas[i].style.display = mostrar ? '' : 'none';
            }
        });

        // Funciones para el modal de ventas mensuales
        function abrirModalVentasMensuales() {
            document.getElementById('modalVentasMensuales').style.display = 'block';
            // Cargar ventas del mes actual
            cargarVentasMes(new Date().getMonth() + 1);
        }

        function cerrarModalVentasMensuales() {
            document.getElementById('modalVentasMensuales').style.display = 'none';
        }

        function cargarVentasMes(mes) {
            const año = document.getElementById('select-anio').value;
            
            // Actualizar botones activos
            document.querySelectorAll('.btn-mes').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            // Realizar la petición al servidor
            fetch(`../controllers/ventas/obtener_ventas_mes.php?mes=${mes}&año=${año}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar resumen
                        document.getElementById('total-ventas-mes').textContent = 
                            `$${parseFloat(data.total_ventas).toFixed(2)}`;
                        document.getElementById('cantidad-ventas-mes').textContent = 
                            data.cantidad_ventas;

                        // Actualizar tabla
                        const tbody = document.getElementById('tabla-ventas-mensuales');
                        tbody.innerHTML = '';

                        data.ventas.forEach(venta => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${new Date(venta.fecha_venta).toLocaleString('es-ES', {
                                    day: 'numeric',
                                    month: 'numeric',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: false
                                })}</td>
                                <td>${venta.productos}</td>
                                <td>${venta.cantidad}</td>
                                <td>-</td>
                                <td>$${parseFloat(venta.total).toFixed(2)}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    } else {
                        console.error('Error al cargar las ventas:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            var modalVentasMensuales = document.getElementById('modalVentasMensuales');
            if (event.target == modalVentasMensuales) {
                cerrarModalVentasMensuales();
            }
        }

        function abrirModalGuia(idCliente) {
            if (!idCliente) {
                // Si no hay ID de cliente, redirigir a la vista de envíos
                window.location.href = 'envios.php';
                return;
            }

            // Si hay ID de cliente, continuar con la lógica existente
            fetch(`../controllers/clientes/obtener_cliente.php?id=${idCliente}`)
                .then(response => response.json())
                .then(cliente => {
                    document.getElementById('fecha-guia').textContent = new Date().toLocaleDateString();
                    document.getElementById('guia-nombre').textContent = cliente.nombre;
                    document.getElementById('guia-direccion').textContent = cliente.direccion;
                    document.getElementById('guia-celular').textContent = cliente.celular;
                    document.getElementById('guia-cedula').textContent = cliente.cedula;
                    document.getElementById('modalGuia').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al obtener los datos del cliente');
                });
        }

        function cerrarModalGuia() {
            document.getElementById('modalGuia').style.display = 'none';
        }

        function imprimirGuia() {
            const contenido = document.querySelector('.guia-contenido').innerHTML;
            const ventana = window.open('', 'PRINT', 'height=8.5in,width=6.5in');
            
            ventana.document.write('<html><head><title>Guía de Envío</title>');
            ventana.document.write('<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">');
            ventana.document.write('<style>');
            ventana.document.write(`
                @page {
                    size: 6.5in 8.5in;
                    margin: 0.1in 0.15in 0.15in 0.15in;
                }
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                }
                .guia-contenido {
                    padding: 0;
                    max-width: 6.5in;
                }
                .guia-header {
                    text-align: center;
                    margin-bottom: 20px;
                    border-bottom: 1px solid #eee;
                    padding-bottom: 15px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 20px;
                }
                .guia-logo {
                    text-align: center;
                }
                .guia-logo img {
                    max-width: 120px;
                    height: auto;
                }
                .guia-titulo {
                    text-align: left;
                }
                .guia-titulo h1 {
                    font-size: 22px;
                    margin-bottom: 8px;
                }
                .guia-titulo p {
                    font-size: 14px;
                    margin: 0;
                }
                .guia-info {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 20px;
                    font-size: 14px;
                }
                .guia-emisor, .guia-receptor {
                    width: 48%;
                }
                .guia-emisor h3, .guia-receptor h3 {
                    font-size: 16px;
                    margin-bottom: 10px;
                }
                .guia-emisor p, .guia-receptor p {
                    margin: 4px 0;
                    font-size: 14px;
                }
                .guia-delicado {
                    text-align: center;
                    margin: 20px 0;
                    padding: 15px;
                    background-color: #ffd6e7;
                    border: 2px dashed #ff69b4;
                }
                .guia-delicado h2 {
                    font-size: 36px;
                    margin: 0;
                    color: #ff1493;
                    text-transform: uppercase;
                    letter-spacing: 2px;
                    text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
                }
                .guia-agradecimiento {
                    text-align: center;
                    margin: 20px 0;
                    padding: 15px;
                    background-color: #f8f9fa;
                }
                .guia-agradecimiento p {
                    font-size: 14px;
                    margin: 0;
                    font-style: italic;
                }
                .guia-footer {
                    text-align: center;
                    margin-top: 20px;
                    border-top: 1px solid #eee;
                    padding-top: 15px;
                }
                .btn-imprimir {
                    background-color: #17a2b8;
                    color: white;
                    padding: 8px 16px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 14px;
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                }
                .btn-imprimir:hover {
                    background-color: #138496;
                }
                .material-icons {
                    font-size: 18px;
                }
                @media print {
                    .guia-delicado {
                        background-color: #ffd6e7 !important;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    .guia-agradecimiento {
                        background-color: #f8f9fa !important;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    .btn-imprimir {
                        display: none;
                    }
                }
            `);
            ventana.document.write('</style></head><body>');
            ventana.document.write(contenido);
            ventana.document.write('</body></html>');
            
            ventana.document.close();
            ventana.focus();
            
            setTimeout(() => {
                ventana.print();
                ventana.close();
            }, 250);
        }

        // Funciones para el modal de insertar cliente
        function abrirModalInsertarCliente() {
            document.getElementById('modalInsertarCliente').style.display = 'block';
            document.getElementById('mensaje-error-insertar-cliente').style.display = 'none';
        }

        function cerrarModalInsertarCliente() {
            document.getElementById('modalInsertarCliente').style.display = 'none';
            document.getElementById('formInsertarCliente').reset();
            document.getElementById('mensaje-error-insertar-cliente').style.display = 'none';
        }

        // Manejar el envío del formulario de insertar cliente
        document.getElementById('formInsertarCliente').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const mensajeError = document.getElementById('mensaje-error-insertar-cliente');
            mensajeError.style.display = 'none';
            
            // Log para debugging
            console.log('Enviando formulario de cliente...');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            fetch('../controllers/clientes/insertar_cliente_venta.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Respuesta del servidor (cliente):', response.status);
                if (!response.ok) {
                    throw new Error('Error de conexión: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos (cliente):', data);
                if (data.success) {
                    cerrarModalInsertarCliente();
                    alert('Cliente agregado exitosamente');
                    console.log('Cliente creado con ID:', data.cliente_id);
                    // Aquí podrías actualizar la lista de clientes si es necesario
                } else {
                    mensajeError.textContent = data.message;
                    mensajeError.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mensajeError.textContent = 'Error de conexión: ' + error.message;
                mensajeError.style.display = 'block';
            });
        });

        function cambiarTab(tab) {
            // Actualizar botones de pestaña
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            // Mostrar contenido correspondiente
            document.getElementById('contenido-mensual').style.display = tab === 'mensual' ? 'block' : 'none';
            document.getElementById('contenido-anual').style.display = tab === 'anual' ? 'block' : 'none';

            // Cargar datos iniciales
            if (tab === 'mensual') {
                cargarVentasMes(new Date().getMonth() + 1);
            } else {
                // Cargar el mes actual del año seleccionado
                cargarVentasMesAnio(new Date().getMonth() + 1);
            }
        }

        function cargarVentasAnio(año) {
            // Realizar la petición al servidor
            fetch(`../controllers/ventas/obtener_ventas_anio.php?año=${año}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar resumen
                        document.getElementById('total-ventas-anio').textContent =
                            `$${parseFloat(data.total_ventas).toFixed(2)}`;
                        document.getElementById('cantidad-ventas-anio').textContent =
                            data.cantidad_ventas;

                        // Actualizar tabla
                        const tbody = document.getElementById('tabla-ventas-anuales');
                        tbody.innerHTML = '';

                        data.ventas.forEach(venta => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${new Date(venta.fecha_venta).toLocaleString('es-ES', {
                                    day: 'numeric',
                                    month: 'numeric',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: false
                                })}</td>
                                <td>${venta.productos}</td>
                                <td>${venta.cantidad}</td>
                                <td>-</td>
                                <td>$${parseFloat(venta.total).toFixed(2)}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    } else {
                        console.error('Error al cargar las ventas:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function cargarVentasMesAnio(mes) {
            const año = document.getElementById('select-anio').value;
            
            // Actualizar botones activos
            document.querySelectorAll('.btn-mes-anual').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            // Realizar la petición al servidor
            fetch(`../controllers/ventas/obtener_ventas_mes.php?mes=${mes}&año=${año}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar resumen
                        document.getElementById('total-ventas-anio').textContent =
                            `$${parseFloat(data.total_ventas).toFixed(2)}`;
                        document.getElementById('cantidad-ventas-anio').textContent =
                            data.cantidad_ventas;

                        // Actualizar tabla
                        const tbody = document.getElementById('tabla-ventas-anuales');
                        tbody.innerHTML = '';

                        data.ventas.forEach(venta => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${new Date(venta.fecha_venta).toLocaleString('es-ES', {
                                    day: 'numeric',
                                    month: 'numeric',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: false
                                })}</td>
                                <td>${venta.productos}</td>
                                <td>${venta.cantidad}</td>
                                <td>-</td>
                                <td>$${parseFloat(venta.total).toFixed(2)}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    } else {
                        console.error('Error al cargar las ventas:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>
</body>
</html>
