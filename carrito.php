<?php
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php?redir=carrito");
    exit;
}

// Productos disponibles
$productos = [
    1 => ["nombre" => "Bolso artesanal", "precio" => 45000],
    2 => ["nombre" => "Monedero tejido", "precio" => 15000],
    3 => ["nombre" => "Cartera elegante", "precio" => 55000],
];

// Inicializar carrito
if (!isset($_SESSION["carrito"])) {
    $_SESSION["carrito"] = [];
}
$carrito = $_SESSION["carrito"];

// 🔹 ELIMINAR UN PRODUCTO
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    unset($_SESSION["carrito"][$id]);
    header("Location: carrito.php");
    exit;
}

// 🔹 VACIAR TODO EL CARRITO
if (isset($_GET['vaciar'])) {
    $_SESSION["carrito"] = [];
    header("Location: carrito.php");
    exit;
}

// 🔹 FINALIZAR COMPRA (guardar en BD)
$mensajeCompra = "";
if (isset($_GET["comprar"]) && !empty($carrito)) {

    include "conexion.php";
    $usuario_id = $_SESSION["usuario_id"];
    $total = 0;

    foreach ($carrito as $id => $cant) {
        $total += $productos[$id]["precio"] * $cant;
    }

    $sql = "INSERT INTO compras(usuario_id, total, fecha) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$usuario_id, $total]);

    $compra_id = $conn->lastInsertId();

    foreach ($carrito as $id => $cant) {
        $sql2 = "INSERT INTO compras_detalle(compra_id, producto, cantidad, precio) VALUES (?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute([$compra_id, $productos[$id]["nombre"], $cant, $productos[$id]["precio"]]);
    }

    $_SESSION["carrito"] = [];
    $mensajeCompra = "✔ Compra realizada con éxito";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mi Carrito</title>
<style>
    body { font-family: Arial; background: #f7f7f7; padding: 20px; }
    table {
        width: 80%;
        margin: auto;
        border-collapse: collapse;
        background: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
    th { background: #e91e63; color: white; }
    .btn { background: #e91e63; color: white; padding: 8px 12px; border-radius: 5px; text-decoration: none; }
    .btn-danger { background: red; }
    .btn-green { background: green; }
</style>
</head>
<body>

<h1 style="text-align:center">🛒 Mi Carrito</h1>

<?php if ($mensajeCompra): ?>
    <p style="text-align:center; color:green; font-weight:bold;"><?= $mensajeCompra ?></p>
<?php endif; ?>

<?php if (empty($carrito)): ?>
<p style="text-align:center; color:red;">Tu carrito está vacío.</p>

<?php else: ?>

<table>
    <tr>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Subtotal</th>
        <th>Acciones</th>
        <th>Editar Cantidad</th>
    </tr>

    <?php
    $total = 0;
    foreach ($carrito as $id => $cant):
        $subtotal = $productos[$id]["precio"] * $cant;
        $total += $subtotal;
    ?>
    
    <tr>
        <td><?= $productos[$id]["nombre"] ?></td>
        <td><?= $cant ?></td>
        <td>$<?= number_format($subtotal) ?></td>
        <td>
            <a class="btn btn-danger" href="carrito.php?eliminar=<?= $id ?>">Eliminar</a>
            <td>
    <form method="POST" action="update_cantidad.php">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="number" name="cantidad" value="<?= $cant ?>" min="1" max="10" style="width:60px;">
        <button class="btn">Actualizar</button>
    </form>
</td>

        </td>
    </tr>


    <?php endforeach; ?>

</table>

<h2 style="text-align:center;">Total: $<?= number_format($total) ?></h2>

<div style="text-align:center; margin-top:20px;">
    <a class="btn" href="accesorios.php">Seguir comprando</a>
    <a class="btn btn-danger" href="carrito.php?vaciar=1">Vaciar carrito</a>
    <a class="btn btn-green" href="carrito.php?comprar=1">Finalizar compra</a>
</div>

<?php endif; ?>

</body>
</html>
