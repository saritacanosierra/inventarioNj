<?php
$host = 'sql311.infinityfree.com'; // Cambia esto si tu servidor MySQL está en otro lugar
$dbname = 'if0_38881193_inventario'; // Nombre de la base de datos
$usuario = 'if0_38881193'; // Tu usuario de MySQL
$password = 'Senaadso123'; // Tu contraseña de MySQL (déjala vacía si no tienes)

$conexion = new mysqli($host, $usuario, $password, $dbname);

if ($conexion->connect_error) {
    die('Error al conectar a la base de datos: ' . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>