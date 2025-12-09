<?php
session_start();
require "config.php"; // archivo con la conexión PDO

// 1. Validar si el carrito está vacío
if (!isset($_SESSION["carrito"]) || empty($_SESSION["carrito"])) {
    echo "<h2>Tu carrito está vacío</h2>";
    echo "<a href='index.php'>Volver a la tienda</a>";
    exit;
}

// Carrito actual
$carrito = $_SESSION["carrito"];

// 2. Calcular total
$total = 0;
foreach ($carrito as $id => $item) {
    $total += $item["precio"] * $item["cantidad"];
}

// 3. Registrar compra
$sql = "INSERT INTO compras (fecha, total) VALUES (NOW(), ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$total]);
$compra_id = $pdo->lastInsertId();

// 4. Registrar detalles de compra
$sqlDetalle = "INSERT INTO compras_detalle (compra_id, accesorio_id, cantidad, precio)
               VALUES (?, ?, ?, ?)";
$stmtDetalle = $pdo->prepare($sqlDetalle);

foreach ($carrito as $id => $item) {
    $stmtDetalle->execute([
        $compra_id,       // compra_id
        $id,              // accesorio_id (ID del producto)
        $item["cantidad"],
        $item["precio"]
    ]);
}

// 5. Vaciar carrito
unset($_SESSION["carrito"]);

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Compra Finalizada</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

<h1>¡Compra finalizada con éxito!</h1>
<p>Tu número de pedido es: <strong><?php echo $compra_id; ?></strong></p>
<p>Total pagado: <strong>$<?php echo number_format($total); ?></strong></p>

<a href="index.php">Volver a la tienda</a>

</body>
</html>
