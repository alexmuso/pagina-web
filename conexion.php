
<?php
// conexion.php
$servername = "localhost";
$username = "root"; // Reemplaza con tu usuario de MySQL
$password = "";     // Reemplaza con tu contraseña de MySQL
$dbname = "boutique"; // Reemplaza con el nombre de tu base de datos

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    // Establecer el modo de error PDO a excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conexión exitosa"; // Descomenta para probar la conexión
} catch(PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}
?>