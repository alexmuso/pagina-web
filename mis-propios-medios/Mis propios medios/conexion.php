<?php
$servidor = getenv('DB_HOST') ?: 'localhost';
$usuario = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$base_datos = getenv('DB_NAME') ?: 'boutique';

try {
    $conn = new PDO("mysql:host=$servidor;dbname=$base_datos;charset=utf8", $usuario, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo '❌ Error de conexión: ' . $e->getMessage();
    exit;
}
?>
