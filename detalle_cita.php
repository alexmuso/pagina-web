<?php
require_once "conexion.php";

$id = $_GET['id'];

$sql = "SELECT * FROM citas WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();
$cita = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<h2>Detalle de la cita</h2>
<p><b>Servicio:</b> <?= $cita['servicio'] ?></p>
<p><b>Lugar:</b> <?= $cita['lugar'] ?></p>
<p><b>Descripción:</b> <?= $cita['descripcion'] ?></p>
<p><b>Estado:</b> <?= $cita['estado'] ?></p>

<a href="mis_citas_pedidos.php">Volver</a>
