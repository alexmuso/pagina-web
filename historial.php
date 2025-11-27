<?php
session_start();
require_once "conexion.php";
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
$usuario_id = $_SESSION['usuario_id'];

// historial citas
$sql_citas = "SELECT * FROM citas WHERE usuario_id = :usuario_id ORDER BY fecha_registro DESC";
$stmt = $conn->prepare($sql_citas);
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// historial pedidos
$sql_pedidos = "SELECT * FROM pedidos WHERE usuario_id = :usuario_id ORDER BY fecha_pedido DESC";
$stmt2 = $conn->prepare($sql_pedidos);
$stmt2->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt2->execute();
$pedidos = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Historial</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: url("img/patron.jfif") no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
}
.container {
    width: 90%;
    max-width: 950px;
    margin: 30px auto;
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 0 10px #bbb;
}
h2 {
    text-align: center;
    background: #e91e63;
    color: white;
    padding: 10px;
    border-radius: 8px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0 40px;
    background: white;
}
table th {
    background: #e91e63;
    color: white;
    padding: 10px;
}
table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
}
.no-data {
    text-align: center;
    color: red;
    font-size: 18px;
    margin-top: 10px;
}
.btn-volver {
    display: block;
    width: 180px;
    margin: 0 auto;
    padding: 10px;
    background: #e91e63;
    color: white;
    text-decoration: none;
    text-align: center;
    border-radius: 6px;
}
.btn-volver:hover { background:#d81b60; }


</style>
</head>
<body>
<div class="container">
    <h2>📜 Historial de Citas</h2>
    <?php if (count($citas) > 0): ?>
    <table>
        <thead>
            <tr><th>ID</th><th>Fecha registro</th><th>Servicio</th><th>Lugar</th><th>Estado</th></tr>
        </thead>
        <tbody>
        <?php foreach ($citas as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['id']) ?></td>
                <td><?= htmlspecialchars($c['fecha_registro']) ?></td>
                <td><?= htmlspecialchars($c['servicio']) ?></td>
                <td><?= htmlspecialchars($c['lugar']) ?></td>
                <td><?= htmlspecialchars($c['estado']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p class="no-data">No hay citas en tu historial.</p>
    <?php endif; ?>

    <h2>📦 Historial de Pedidos</h2>
    <?php if (count($pedidos) > 0): ?>
    <table>
        <thead>
            <tr><th>ID</th><th>Dirección</th><th>Total</th><th>Fecha</th><th>Estado</th></tr>
        </thead>
        <tbody>
        <?php foreach ($pedidos as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['id']) ?></td>
                <td><?= htmlspecialchars($p['direccion_envio']) ?></td>
                <td>$<?= htmlspecialchars(number_format($p['total'],2)) ?></td>
                <td><?= htmlspecialchars($p['fecha_pedido']) ?></td>
                <td><?= htmlspecialchars($p['estado']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p class="no-data">No hay pedidos en tu historial.</p>
    <?php endif; ?>

    <a class="btn-volver" href="mis_citas_pedidos.php">⬅ Volver</a>
</div>
</body>
</html>

