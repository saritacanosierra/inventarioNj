<?php
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../controllers/envios/envios.php';
session_start();

// Obtener el ID de la venta si existe
$id_venta = isset($_GET['id_venta']) ? intval($_GET['id_venta']) : null;

// Crear instancia del manager de envíos
$enviosManager = new EnviosManager($conexion);

// Modificar la tabla ventas para agregar la relación con clientes (solo si es necesario)
$resultado_modificacion = $enviosManager->modificarTablaVentas();
if (!$resultado_modificacion['success']) {
    echo "<script>console.log('" . $resultado_modificacion['message'] . "');</script>";
}

// Mostrar mensajes al inicio de la página
if (isset($_GET['mensaje'])) {
    echo "<script>alert('" . htmlspecialchars($_GET['mensaje']) . "');</script>";
}
if (isset($_GET['error'])) {
    echo "<script>alert('" . htmlspecialchars($_GET['error']) . "');</script>";
}

// Obtener todos los clientes usando el manager
$resultado = $enviosManager->obtenerClientes();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/listarUsuario.css">
    <link rel="stylesheet" href="../css/tablas.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
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

        .close {
            color: #666;
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

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
        }

        .btn-guardar {
            background-color: #e83e8c;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .btn-guardar:hover {
            background-color: #d63384;
        }

        .btn-cancelar {
            background-color: #6c757d;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            margin-left: 10px;
        }

        .btn-cancelar:hover {
            background-color: #5a6268;
        }

        .form-buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .modal h2 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .modal-content {
                width: 90%;
                margin: 10% auto;
            }
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

        .btn-asociar {
            background-color: #28a745;
            color: white;
            padding: 4px 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }

        .btn-asociar:hover {
            background-color: #218838;
        }

        .venta-info {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 4px solid #28a745;
        }

        .venta-info h3 {
            margin: 0 0 10px 0;
            color: #333;
        }

        .venta-info p {
            margin: 5px 0;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="contenedor-principal">
        <?php include '../components/header.php'; ?>
        
        <div class="contenido">
            <h2>Gestión de Clientes</h2>
            
            <?php if ($id_venta): ?>
            <div class="venta-info">
                <h3>Asociar Cliente a Venta #<?php echo $id_venta; ?></h3>
                <p>Seleccione un cliente existente o cree uno nuevo para asociarlo a esta venta.</p>
            </div>
            <?php endif; ?>
            
            <div class="filtro-agregar-contenedor">
                <div class="filtro-contenedor">
                    <input type="text" id="filtro-cliente" placeholder="Buscar cliente..." class="filtro-input">
                </div>
                <div class="btn-agregar-contenedor">
                    <button onclick="abrirModalInsertar()" class="btn-agregar">+</button>
                </div>
            </div>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Cédula</th>
                            <th>Celular</th>
                            <th>Dirección</th>
                            <th>Total Compras</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-clientes">
                        <?php
                        if ($resultado->num_rows > 0) {
                            while($cliente = $resultado->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $cliente['id'] . "</td>";
                                echo "<td>" . htmlspecialchars($cliente['nombre']) . "</td>";
                                echo "<td>" . htmlspecialchars($cliente['cedula']) . "</td>";
                                echo "<td>" . htmlspecialchars($cliente['celular']) . "</td>";
                                echo "<td>" . htmlspecialchars($cliente['direccion']) . "</td>";
                                echo "<td>" . $cliente['total_compras'] . "</td>";
                                echo "<td>" . $cliente['fecha_registro'] . "</td>";
                                echo "<td class='acciones'>";
                                echo "<button onclick='abrirModalEditar(" . htmlspecialchars(json_encode($cliente)) . ")' class='btn-editar'>";
                                echo "<span class='material-icons'>edit</span>";
                                echo "</button>";
                                if ($id_venta) {
                                    echo "<button onclick='asociarClienteAVenta(" . $cliente['id'] . ", " . $id_venta . ")' class='btn-asociar'>";
                                    echo "<span class='material-icons'>link</span>";
                                    echo "</button>";
                                }
                                echo "<button onclick='abrirModalGuia(" . htmlspecialchars(json_encode($cliente)) . ")' class='btn-guia'>";
                                echo "<span class='material-icons'>local_shipping</span>";
                                echo "</button>";
                                echo "<a href='../controllers/clientes/eliminar_cliente.php?id=" . $cliente['id'] . "' class='btn-eliminar' onclick='return confirm(\"¿Estás seguro de eliminar este cliente?\")'>";
                                echo "<span class='material-icons'>delete</span>";
                                echo "</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>No hay clientes registrados</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para insertar cliente -->
    <div id="modalInsertar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalInsertar()">&times;</span>
            <h2>Insertar Nuevo Cliente</h2>
            <div id="mensaje-error-insertar" class="mensaje error" style="display: none;"></div>
            <form id="formInsertar" method="POST" action="../controllers/envios/envios.php<?php echo $id_venta ? '?id_venta=' . $id_venta : ''; ?>">
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
                    <button type="button" class="btn-cancelar" onclick="cerrarModalInsertar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para editar cliente -->
    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalEditar()">&times;</span>
            <h2>Editar Cliente</h2>
            <div id="mensaje-error-editar" class="mensaje error" style="display: none;"></div>
            <form id="formEditar" class="form-editar">
                <input type="hidden" id="edit_id" name="id">
                <div class="form-group">
                    <label for="edit_nombre">Nombre:</label>
                    <input type="text" id="edit_nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="edit_cedula">Cédula:</label>
                    <input type="text" id="edit_cedula" name="cedula" required>
                </div>
                <div class="form-group">
                    <label for="edit_celular">Celular:</label>
                    <input type="text" id="edit_celular" name="celular" required>
                </div>
                <div class="form-group">
                    <label for="edit_direccion">Dirección:</label>
                    <input type="text" id="edit_direccion" name="direccion" required>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-guardar">Guardar</button>
                    <button type="button" class="btn-cancelar" onclick="cerrarModalEditar()">Cancelar</button>
                </div>
            </form>
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

    <script>
        // Funciones para el manejo de modales
        function abrirModalInsertar() {
            document.getElementById('modalInsertar').style.display = 'block';
        }

        function cerrarModalInsertar() {
            document.getElementById('modalInsertar').style.display = 'none';
        }

        function abrirModalEditar(cliente) {
            document.getElementById('edit_id').value = cliente.id;
            document.getElementById('edit_nombre').value = cliente.nombre;
            document.getElementById('edit_cedula').value = cliente.cedula;
            document.getElementById('edit_celular').value = cliente.celular;
            document.getElementById('edit_direccion').value = cliente.direccion;
            document.getElementById('modalEditar').style.display = 'block';
        }

        function cerrarModalEditar() {
            document.getElementById('modalEditar').style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }

        // Filtro de búsqueda
        document.getElementById('filtro-cliente').addEventListener('keyup', function() {
            const filtro = this.value.toLowerCase();
            const tabla = document.getElementById('tabla-clientes');
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

        function abrirModalGuia(cliente) {
            document.getElementById('fecha-guia').textContent = new Date().toLocaleDateString();
            document.getElementById('guia-nombre').textContent = cliente.nombre;
            document.getElementById('guia-direccion').textContent = cliente.direccion;
            document.getElementById('guia-celular').textContent = cliente.celular;
            document.getElementById('guia-cedula').textContent = cliente.cedula;
            document.getElementById('modalGuia').style.display = 'block';
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

        function asociarClienteAVenta(idCliente, idVenta) {
            if (confirm('¿Desea asociar este cliente a la venta #' + idVenta + '?')) {
                fetch('../controllers/clientes/asociar_cliente_venta.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_cliente: idCliente,
                        id_venta: idVenta
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cliente asociado exitosamente');
                        window.location.href = 'ventas.php';
                    } else {
                        alert('Error al asociar el cliente: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al procesar la solicitud');
                });
            }
        }

        // Función para validar el formulario antes de enviar
        document.getElementById('formInsertar').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const nombre = document.getElementById('nombre').value.trim();
            const cedula = document.getElementById('cedula').value.trim();
            const celular = document.getElementById('celular').value.trim();
            const direccion = document.getElementById('direccion').value.trim();
            
            if (!nombre || !cedula || !celular || !direccion) {
                alert('Todos los campos son obligatorios');
                return;
            }
            
            // Si todo está bien, enviar el formulario
            this.submit();
        });

        // Manejar el envío del formulario de edición
        document.getElementById('formEditar').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('../controllers/clientes/actualizar_cliente.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                // Extraer el mensaje del script de alerta
                const match = html.match(/alert\('([^']+)'\)/);
                if (match) {
                    alert(match[1]);
                }
                
                // Cerrar el modal
                cerrarModalEditar();
                
                // Recargar la página para mostrar los cambios
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
        });
    </script>
</body>
</html>
<?php $conexion->close(); ?>
