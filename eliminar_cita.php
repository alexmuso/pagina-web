<?php
require_once "conexion.php";

if (!isset($_GET['id'])) {
    die("ID inválido");
}

$id = $_GET['id'];

$sql = "DELETE FROM citas WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt->execute([$id])) {
    header("Location: admin_dashboard.php?view=citas&msg=eliminado");
    exit;
} else {
    echo "Error eliminando la cita.";
}
?>
