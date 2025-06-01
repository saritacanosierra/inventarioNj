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

<?php
// Conexión local (comentada)
/*
$host = "localhost";       // o 127.0.0.1
$usuario = "root";         // usuario por defecto en XAMPP
$contrasena = "";          // contraseña vacía por defecto
$base_de_datos = "inventario"; 
*/

// Conexión con el servidor
//$host = "localhost";
//$usuario = "u753706441_inventario";
//$contrasena = "Inventario2024*";
//$base_de_datos = "u753706441_inventario";

//$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error al conectar a la base de datos: " . $conexion->connect_error);
}

// Opcional: mensaje de éxito
// echo "Conexión exitosa";
?>
