<?php
session_start();

$id = $_POST["id"];
$cantidad = intval($_POST["cantidad"]);

if ($cantidad > 0) {
    $_SESSION["carrito"][$id] = $cantidad;
}

header("Location: carrito.php");
exit;
?>
