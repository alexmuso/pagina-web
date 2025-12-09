<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Factura no válida.";
    exit;
}

$compra_id = intval($_GET['id']);

include "conexion.php";

// Obtener datos de la compra
$sql = "SELECT c.*, u.nombre 
        FROM compras c 
        INNER JOIN usuarios u ON c.usuario_id = u.id
        WHERE c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$compra_id]);
$compra = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$compra) {
    echo "Factura no existe.";
    exit;
}

// Obtener detalle
$sql = "SELECT cd.cantidad, cd.precio, p.nombre
        FROM compras_detalle cd
        INNER JOIN productos p ON p.id = cd.producto_id
        WHERE cd.compra_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$compra_id]);
$detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Factura #<?= $compra_id ?></title>
<style>
body { font-family: Arial; background:#f5f5f5; padding:20px; }
.factura { background:white; width:70%; margin:auto; padding:20px;
           border-radius:10px; box-shadow:0 3px 8px rgba(0,0,0,0.2); }
h2 { text-align:center; }
table { width:100%; border-collapse: collapse; margin-top:20px; }
th, td { border:1px solid #ddd; padding:10px; text-align:center; }
th { background:#e91e63; color:white; }
.btn { background:#e91e63; color:white; padding:10px; text-decoration:none; border-radius:5px; }
</style>
</head>
<body>

<div class="factura">
    <h2>Factura de Compra</h2>

    <p><strong>Factura N°:</strong> <?= $compra_id ?></p>
    <p><strong>Cliente:</strong> <?= $compra["nombre"] ?></p>
    <p><strong>Fecha:</strong> <?= $compra["fecha"] ?></p>
    <p><strong>Total:</strong> $<?= number_format($compra["total"]) ?></p>

    <h3>Detalle de productos</h3>
    <table>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unidad</th>
            <th>Subtotal</th>
        </tr>

        <?php foreach ($detalles as $d): ?>
        <tr>
            <td><?= $d["nombre"] ?></td>
            <td><?= $d["cantidad"] ?></td>
            <td>$<?= number_format($d["precio"]) ?></td>
            <td>$<?= number_format($d["precio"] * $d["cantidad"]) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div style="text-align:center; margin-top:20px;">
        <a class="btn" onclick="window.print()">🖨 Imprimir o Descargar PDF</a>
        <a class="btn" href="accesorios.php">Seguir comprando</a>
    </div>
</div>

</body>
</html>
