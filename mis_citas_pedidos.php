<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

/* ====================== */
/*        CITAS           */
/* ====================== */
$sql_citas = "SELECT * FROM citas WHERE usuario_id = :usuario_id ORDER BY fecha_registro DESC";
$stmt_citas = $conn->prepare($sql_citas);
$stmt_citas->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt_citas->execute();
$citas = $stmt_citas->fetchAll(PDO::FETCH_ASSOC);

/* ====================== */
/*        PEDIDOS         */
/* ====================== */
$sql_pedidos = "SELECT * FROM pedidos WHERE usuario_id = :usuario_id ORDER BY fecha_pedido DESC";
$stmt_pedidos = $conn->prepare($sql_pedidos);
$stmt_pedidos->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt_pedidos->execute();
$pedidos = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);

/* ====================== */
/*        COMPRAS         */
/* ====================== */
$sql_compras = "SELECT * FROM compras WHERE usuario_id = ? ORDER BY fecha DESC";
$stmt_compras = $conn->prepare($sql_compras);
$stmt_compras->execute([$usuario_id]);
$compras = $stmt_compras->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mi Panel</title>
<style>
*{box-sizing:border-box;}
body{
    font-family: Arial, sans-serif;
    background:#f3f3f3;
    margin:0;
    padding:20px;
}
.topbar{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    margin-bottom:20px;
}
.btn{
    background:#e91e63;
    color:#fff;
    padding:8px 12px;
    border-radius:6px;
    text-decoration:none;
}
.btn.logout{ background:#222; }

/* CONTENEDOR */
.panel{
    max-width:1000px;
    margin:auto;
    background:#fff;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 14px rgba(0,0,0,0.1);
}

/* TABS */
.tabs{
    display:flex;
    justify-content:center;
    gap:10px;
    margin-bottom:20px;
}
.tab{
    padding:10px 20px;
    background:#e0e0e0;
    border-radius:8px;
    cursor:pointer;
    font-weight:bold;
}
.tab.active{
    background:#e91e63;
    color:white;
}

/* TAB CONTENT */
.tab-content{
    display:none;
}
.tab-content.active{
    display:block;
}

/* TABLAS */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}
table th, table td{
    border:1px solid #ddd;
    padding:10px;
    text-align:center;
}
table th{
    background:#e91e63;
    color:#fff;
}

.no-data{
    text-align:center;
    color:#b71c1c;
    padding:15px 0;
}
</style>
</head>
<body>

<div class="topbar">
    <a class="btn" href="editar_perfil.php">✏ Editar perfil</a>
    <a class="btn" href="historial.php">📜 Historial</a>
    <a class="btn logout" href="logout.php">⛔ Cerrar sesión</a>
</div>

<div class="panel">

<!-- TABS -->
<div class="tabs">
    <div class="tab active" data-tab="citas">📋 Mis Citas</div>
    <div class="tab" data-tab="pedidos">🛍 Mis Pedidos</div>
    <div class="tab" data-tab="compras">💰 Mis Compras</div>
</div>

<!-- ======================= -->
<!--       TAB CITAS         -->
<!-- ======================= -->
<div id="citas" class="tab-content active">
<?php if (count($citas) > 0): ?>
<table>
<thead>
    <tr>
        <th>ID</th>
        <th>Fecha</th>
        <th>Servicio</th>
        <th>Lugar</th>
        <th>Estado</th>
    </tr>
</thead>
<tbody>
<?php foreach ($citas as $c): ?>
<tr>
    <td><?= $c["id"] ?></td>
    <td><?= $c["fecha_registro"] ?></td>
    <td><?= $c["servicio"] ?></td>
    <td><?= $c["lugar"] ?></td>
    <td><?= $c["estado"] ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<p class="no-data">No tienes citas aún.</p>
<?php endif; ?>
</div>


<!-- ======================= -->
<!--       TAB PEDIDOS       -->
<!-- ======================= -->
<div id="pedidos" class="tab-content">
<?php if (count($pedidos) > 0): ?>
<table>
<thead>
<tr>
    <th>ID</th>
    <th>Total</th>
    <th>Fecha</th>
    <th>Estado</th>
</tr>
</thead>
<tbody>
<?php foreach ($pedidos as $p): ?>
<tr>
    <td><?= $p["id"] ?></td>
    <td>$<?= number_format($p["total"]) ?></td>
    <td><?= $p["fecha_pedido"] ?></td>
    <td><?= $p["estado"] ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<p class="no-data">No tienes pedidos registrados.</p>
<?php endif; ?>
</div>


<!-- ======================= -->
<!--       TAB COMPRAS       -->
<!-- ======================= -->
<div id="compras" class="tab-content">
<?php if (empty($compras)): ?>
<p class="no-data">No has realizado compras en la tienda.</p>
<?php else: ?>

<?php foreach ($compras as $c): ?>

<h3 style="text-align:center;">Compra #<?= $c['id'] ?> — <?= $c['fecha'] ?></h3>

<table>
<tr>
    <th>Producto</th>
    <th>Cantidad</th>
    <th>Precio</th>
</tr>

<?php
$sql_det = "SELECT * FROM compras_detalle WHERE compra_id = ?";
$stmt_det = $conn->prepare($sql_det);
$stmt_det->execute([$c["id"]]);
$detalles = $stmt_det->fetchAll(PDO::FETCH_ASSOC);

foreach ($detalles as $d):
?>
<tr>
    <td><?= $d["producto"] ?></td>
    <td><?= $d["cantidad"] ?></td>
    <td>$<?= number_format($d["precio"]) ?></td>
</tr>
<?php endforeach; ?>

</table>

<h3 style="text-align:center;">Total: $<?= number_format($c["total"]) ?></h3>

<?php endforeach; ?>

<?php endif; ?>
</div>

</div>

<script>
// Cambiar pestañas sin recargar
const tabs = document.querySelectorAll('.tab');
const contents = document.querySelectorAll('.tab-content');

tabs.forEach(tab => {
    tab.addEventListener('click', () => {

        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        contents.forEach(c => c.classList.remove('active'));
        document.getElementById(tab.dataset.tab).classList.add('active');
    });
});
</script>

</body>
</html>
