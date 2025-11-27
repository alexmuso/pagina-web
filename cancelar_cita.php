<?php
require_once "conexion.php";

if (!isset($_GET['id'])) {
    header("Location: mis_citas_pedidos.php");
    exit;
}

$id = $_GET['id'];

$sql = "UPDATE citas SET estado='cancelada' WHERE id=:id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();

header("Location: mis_citas_pedidos.php");
exit;
